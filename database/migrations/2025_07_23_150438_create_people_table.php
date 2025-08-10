<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('full_name');
            $table->string('nickname')->nullable();
            $table->char('gender', 1);
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('mother_name')->nullable();
            $table->tinyInteger('blood_type')->nullable();
            $table->tinyInteger('religion')->nullable();
            $table->tinyInteger('marital_status')->nullable();
            $table->foreignId('education_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('job_title_id')->nullable()->constrained()->restrictOnDelete();
            $table->char('lang_code', 6)->nullable()->default('id_ID');
            $table->foreignId('ethnicity_code')->nullable();
            $table->boolean('is_foreigner')->default(false);
            $table->string('nationality')->nullable()->default('ID');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('identities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('person_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('identity_type');
            $table->string('number');
            $table->date('issued_at')->nullable();
            $table->date('expired_at')->nullable();
            $table->foreignId('image_id')->nullable();
            $table->string('country_code', 3)->default('ID');
            $table->boolean('is_primary')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['person_id', 'identity_type', 'number']);
            $table->unique(['person_id', 'identity_type']);
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('person_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('address_type');
            $table->text('address');
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subdistrict_id')->constrained()->cascadeOnDelete();
            $table->char('country_code', 3)->nullable();
            $table->string('postal_code')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('phones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('person_id')->constrained()->cascadeOnDelete();
            $table->string('number');
            $table->char('country_code', 3)->default('ID');
            $table->boolean('is_whatsapp')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('emails', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('person_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
        Schema::dropIfExists('identities');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('phones');
        Schema::dropIfExists('emails');
    }
};
