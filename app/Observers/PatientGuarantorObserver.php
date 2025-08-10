<?php

namespace App\Observers;

use App\Models\PatientGuarantor;

class PatientGuarantorObserver
{
    public function saving(PatientGuarantor $address): void
    {
        // Cek apakah orang ini sudah punya guarantor
        $existingPrimary = PatientGuarantor::where('person_id', $address->person_id)
            ->where('is_primary', true)
            ->first();

        // Jika belum ada guarantor sebelumnya, jadikan otomatis primary
        if (!$existingPrimary) {
            $address->is_primary = true;
        }
    }

    public function saved(PatientGuarantor $address): void
    {
        // Jika guarantor baru diset sebagai primary
        if ($address->is_primary) {
            // Nonaktifkan is_primary pada guarantor lain milik orang yang sama
            PatientGuarantor::where('person_id', $address->person_id)
                ->where('id', '!=', $address->id)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);
        }
    }

    /**
     * Handle the PatientGuarantor "created" event.
     */
    public function created(PatientGuarantor $patientGuarantor): void
    {
        //
    }

    /**
     * Handle the PatientGuarantor "updated" event.
     */
    public function updated(PatientGuarantor $patientGuarantor): void
    {
        //
    }

    /**
     * Handle the PatientGuarantor "deleted" event.
     */
    public function deleted(PatientGuarantor $patientGuarantor): void
    {
        //
    }

    /**
     * Handle the PatientGuarantor "restored" event.
     */
    public function restored(PatientGuarantor $patientGuarantor): void
    {
        //
    }

    /**
     * Handle the PatientGuarantor "force deleted" event.
     */
    public function forceDeleted(PatientGuarantor $patientGuarantor): void
    {
        //
    }
}
