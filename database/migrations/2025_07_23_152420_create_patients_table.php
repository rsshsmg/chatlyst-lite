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
        Schema::create('patients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('person_id')->constrained()->restrictOnDelete();
            $table->string('patient_code', 12)->nullable();
            $table->string('ref_patient_code', 12)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('patient_guardian', function (Blueprint $table) {
            $table->foreignUuid('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('person_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('relation_type');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->primary(['patient_id', 'person_id']);
        });

        Schema::create('patient_guarantor', function (Blueprint $table) {
            // $table->uuid('id')->primary();

            $table->foreignUuid('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guarantor_id')->constrained()->cascadeOnDelete();

            $table->string('member_number');
            $table->boolean('is_primary')->default(false);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
        Schema::dropIfExists('patient_guardian');
        Schema::dropIfExists('patient_guarantors');
    }
};
