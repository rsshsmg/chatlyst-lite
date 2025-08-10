<?php

namespace App\Observers;

use App\Models\Address;

class AddressObserver
{
    public function saving(Address $address): void
    {
        // Cek apakah orang ini sudah punya alamat
        $existingPrimary = Address::where('person_id', $address->person_id)
            ->where('is_primary', true)
            ->first();

        // Jika belum ada alamat sebelumnya, jadikan otomatis primary
        if (!$existingPrimary) {
            $address->is_primary = true;
        }
    }

    public function saved(Address $address): void
    {
        // Jika alamat baru diset sebagai primary
        if ($address->is_primary) {
            // Nonaktifkan is_primary pada alamat lain milik orang yang sama
            Address::where('person_id', $address->person_id)
                ->where('id', '!=', $address->id)
                ->where('is_primary', true)
                ->update(['is_primary' => false]);
        }
    }

    /**
     * Handle the Address "created" event.
     */
    public function created(Address $address): void
    {
        //
    }

    /**
     * Handle the Address "updated" event.
     */
    public function updated(Address $address): void
    {
        //
    }

    /**
     * Handle the Address "deleted" event.
     */
    public function deleted(Address $address): void
    {
        //
    }

    /**
     * Handle the Address "restored" event.
     */
    public function restored(Address $address): void
    {
        //
    }

    /**
     * Handle the Address "force deleted" event.
     */
    public function forceDeleted(Address $address): void
    {
        //
    }
}
