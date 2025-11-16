<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialization_id')->nullable()->constrained('specializations')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('initials')->nullable();
            $table->string('title')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->text('education')->nullable();
            $table->text('experience')->nullable();
            $table->string('license_no')->nullable();
            $table->date('license_validity')->nullable();
            $table->string('dpjp_code')->nullable();
            $table->string('fhir_code')->nullable();
            $table->tinyInteger('status')->default(1); // 0: inactive, 1: active
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
