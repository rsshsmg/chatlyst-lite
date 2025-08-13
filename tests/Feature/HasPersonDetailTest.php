<?php

use App\Enums\Gender;
use App\Enums\IdentityType;
use App\Models\Person;
use App\Models\Identity;
use App\Models\Phone;
use App\Models\Email;
use App\Services\HISSync\DTO\PersonDTO;
use App\Services\HISSync\DTO\IdentityDTO;
use App\Services\HISSync\DTO\PhoneDTO;
use App\Services\HISSync\DTO\EmailDTO;
use App\Exceptions\DuplicateDataException;
use App\Services\HISSync\Traits\HasPersonDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

// uses(RefreshDatabase::class);
// uses(DatabaseMigrations::class);
uses(HasPersonDetail::class);

beforeEach(function () {
    $this->person = new Person();
});

it('can set and get person', function () {
    $person = Person::factory()->create([
        'full_name' => 'John Doe',
        'date_of_birth' => '1990-01-01',
        'gender' => Gender::Male,
    ]);

    $this->setPerson($person);
    $this->setForce(true);

    expect($this->getPerson())->toBeInstanceOf(Person::class);
    expect($this->getPerson()->full_name)->toBe('John Doe');
});

it('can check for person duplicates', function () {
    // Create existing person
    $person = Person::factory()->create([
        'full_name' => 'Jane Smith',
        'date_of_birth' => '1985-05-15',
        'gender' => Gender::Female,
    ]);

    $this->setPerson($person);
    $this->setForce(true);

    $personDTO = new PersonDTO(
        fullName: 'Jane Smith',
        birthDate: '1985-05-15',
        gender: Gender::Female,
        birthPlace: 'New York',
        motherName: 'Jane Doe',
    );

    $result = $this->checkDuplicates($personDTO);

    expect($result['summary']['has_duplicates'])->toBeTrue();
    expect($result['person']['full_name'])->toBe('Jane Smith');
});

it('can check for identity duplicates', function () {
    $person = Person::factory()->create();
    $existingIdentity = Identity::factory()->create([
        'person_id' => $person->id,
        'identity_type' => IdentityType::KTP,
        'number' => '1234567890123456'
    ]);

    $this->setPerson($person);
    $this->setForce(true);

    $personDTO = new PersonDTO(
        fullName: 'Test User',
        birthDate: '1990-01-01',
        gender: Gender::Male,
        birthPlace: 'Jakarta',
        motherName: 'Jane Doe',
    );

    $identities = [
        new IdentityDTO(
            personId: null,
            number: '1234567890123456',
            identityType: IdentityType::KTP,
        )
    ];

    $result = $this->checkDuplicates($personDTO, $identities);

    expect($result['summary']['has_duplicates'])->toBeTrue();
    expect($result['identities'])->toHaveCount(1);
});

it('can check for phone duplicates', function () {
    $person = Person::factory()->create();
    $existingPhone = Phone::factory()->create([
        'person_id' => $person->id,
        'number' => '+6281234567890'
    ]);

    $this->setPerson($person);
    $this->setForce(true);

    $personDTO = new PersonDTO(
        fullName: 'Test User',
        birthDate: '1990-01-01',
        gender: Gender::Male,
        birthPlace: 'Jakarta',
        motherName: 'Jane Doe',
    );

    $phones = [
        new PhoneDTO(
            personId: null,
            number: '081234567890'
        )
    ];

    $result = $this->checkDuplicates($personDTO, [], $phones, []);

    expect($result['summary']['has_duplicates'])->toBeTrue();
    expect($result['phones'])->toHaveCount(1);
});

it('can check for email duplicates', function () {
    $person = Person::factory()->create();
    $existingEmail = Email::factory()->create([
        'person_id' => $person->id,
        'email' => 'test@example.com'
    ]);

    $this->setPerson($person);
    $this->setForce(true);

    $personDTO = new PersonDTO(
        fullName: 'Test User',
        birthDate: '1990-01-01',
        gender: Gender::Male,
        birthPlace: 'Jakarta',
        motherName: 'Jane Doe',
    );

    $emails = [
        new EmailDTO(
            personId: null,
            email: 'test@example.com'
        )
    ];

    $result = $this->checkDuplicates($personDTO, [], [], $emails);

    expect($result['summary']['has_duplicates'])->toBeTrue();
    expect($result['emails'])->toHaveCount(1);
});

it('throws exception when duplicates found and force is false', function () {
    $person = Person::factory()->create([
        'full_name' => 'Duplicate User',
        'date_of_birth' => '1990-01-01',
        'gender' => Gender::Male,
    ]);

    $this->setPerson($person);
    $this->setForce(false);

    $personDTO = new PersonDTO(
        fullName: 'Duplicate User',
        birthDate: '1990-01-01',
        gender: Gender::Male,
        birthPlace: 'Jakarta',
        motherName: 'Jane Doe',
    );

    expect(fn() => $this->checkDuplicates($personDTO, [], [], [], false))
        ->toThrow(DuplicateDataException::class);
});

it('allows duplicates when force is true', function () {
    $person = Person::factory()->create([
        'full_name' => 'Duplicate User',
        'date_of_birth' => '1990-01-01',
        'gender' => Gender::Male
    ]);

    $this->setPerson($person);
    $this->setForce(true);

    $personDTO = new PersonDTO(
        fullName: 'Duplicate User',
        birthDate: '1990-01-01',
        gender: Gender::Male,
        birthPlace: 'Jakarta',
        motherName: 'Jane Doe',
    );

    $result = $this->checkDuplicates($personDTO, [], [], []);

    expect($result['summary']['has_duplicates'])->toBeTrue();
    expect($result['person']['full_name'])->toBe('Duplicate User');
});

it('can format indonesian phone numbers to e164', function () {
    expect($this->formatToIndonesiaE164('081234567890'))->toBe('+6281234567890');
    expect($this->formatToIndonesiaE164('81234567890'))->toBe('+6281234567890');
    expect($this->formatToIndonesiaE164('62-812-345-678-90'))->toBe('+6281234567890');
    expect($this->formatToIndonesiaE164('+6281234567890'))->toBe('+6281234567890');
    expect($this->formatToIndonesiaE164('0812-3456-7890'))->toBe('+6281234567890');
});

it('returns empty duplicates when no duplicates found', function () {
    $personDTO = new PersonDTO(
        fullName: 'Unique User',
        birthDate: '1990-01-01',
        gender: Gender::Male,
        birthPlace: 'Jakarta',
        motherName: 'Jane Doe',
    );

    $result = $this->checkDuplicates($personDTO);

    expect($result['summary']['has_duplicates'])->toBeFalse();
    expect($result['summary']['total_count'])->toBe(0);
    expect($result['person'])->toBeNull();
});

it('can sync person data', function () {
    $personDTO = new PersonDTO(
        fullName: 'John Sync',
        birthDate: '1990-01-01',
        gender: Gender::Male,
        nickname: 'Johnny',
        birthPlace: 'Jakarta',
        motherName: 'Jane Doe',
    );

    $this->syncPerson($personDTO);

    $this->assertDatabaseHas('people', [
        'full_name' => 'John Sync',
        'nickname' => 'Johnny',
        'place_of_birth' => 'Jakarta',
        'date_of_birth' => '1990-01-01',
        'gender' => Gender::Male,
        'mother_name' => 'Jane Doe',
    ]);
});

it('can sync identities', function () {
    $person = Person::factory()->create();
    $this->setPerson($person);

    $identities = [
        new IdentityDTO(
            personId: null,
            number: '1234567890123456',
            identityType: IdentityType::KTP
        )
    ];

    $this->syncIdentities($identities);

    $this->assertDatabaseHas('identities', [
        'person_id' => $person->id,
        'identity_type' => IdentityType::KTP,
        'number' => '1234567890123456'
    ]);
});

it('can sync phones', function () {
    $person = Person::factory()->create();
    $this->setPerson($person);

    $phones = [
        new PhoneDTO(
            personId: null,
            number: '081234567890',
            countryCode: 'ID',
            isWhatsapp: true
        )
    ];

    $this->syncPhones($phones);

    $this->assertDatabaseHas('phones', [
        'person_id' => $person->id,
        'number' => '+6281234567890',
        'country_code' => 'ID',
        'is_whatsapp' => true
    ]);
});

it('can sync emails', function () {
    $person = Person::factory()->create();
    $this->setPerson($person);

    $emails = [
        new EmailDTO(
            personId: null,
            email: 'test@example.com'
        )
    ];

    $this->syncEmails($emails);

    $this->assertDatabaseHas('emails', [
        'person_id' => $person->id,
        'email' => 'test@example.com'
    ]);
});

it('marks first identity as primary when none exist', function () {
    $person = Person::factory()->create();
    $this->setPerson($person);

    $identities = [
        new IdentityDTO(
            personId: null,
            number: '1234567890123456',
            countryCode: 'ID',
            identityType: IdentityType::KTP
        )
    ];

    $this->syncIdentities($identities);

    $this->assertDatabaseHas('identities', [
        'person_id' => $person->id,
        'is_primary' => true
    ]);
});

it('marks first phone as primary when none exist', function () {
    $person = Person::factory()->create();
    $this->setPerson($person);

    $phones = [
        new PhoneDTO(
            personId: null,
            number: '081234567890',
            countryCode: 'ID',
        )
    ];

    $this->syncPhones($phones);

    $this->assertDatabaseHas('phones', [
        'person_id' => $person->id,
        'is_primary' => true
    ]);
});

it('marks first email as primary when none exist', function () {
    $person = Person::factory()->create();
    $this->setPerson($person);

    $emails = [
        new EmailDTO(
            personId: null,
            email: 'test@example.com'
        )
    ];

    $this->syncEmails($emails);

    $this->assertDatabaseHas('emails', [
        'person_id' => $person->id,
        'is_primary' => true
    ]);
});
