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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->string('phone');
            $table->json('timezones')->nullable();
            $table->char('numeric_code', 3)->nullable();
            $table->char('iso3', 3)->nullable();
            $table->string('nationality')->nullable();
            $table->string('capital')->nullable();
            $table->string('tld')->nullable();
            $table->string('native')->nullable();
            $table->string('region')->nullable();
            $table->string('currency')->nullable();
            $table->string('currency_name')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->string('wikiDataId')->nullable();

            $table->decimal('lat')->nullable();
            $table->decimal('lng')->nullable();

            $table->string('emoji')->nullable();
            $table->string('emojiU')->nullable();
            $table->boolean('flag')->default(0)->nullable();

            $table->boolean('is_activated')->default(1)->nullable();

            $table->timestamps();
        });

        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->nullable()->unique();
            $table->string('name', 100);
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('regencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->nullable()->unique();
            $table->string('name', 100);
            $table->foreignId('province_id')->constrained('provinces')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->nullable()->unique();
            $table->string('name', 100);
            $table->foreignId('regency_id')->constrained('regencies')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('subdistricts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->nullable()->unique();
            $table->string('name', 100);
            $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('subdistricts');
    }
};
