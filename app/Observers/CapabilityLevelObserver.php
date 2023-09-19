<?php

namespace App\Observers;

use App\Models\CapabilityLevel;

class CapabilityLevelObserver
{
    /**
     * Handle the CapabilityLevel "created" event.
     */
    public function created(CapabilityLevel $capabilityLevel): void
    {
        // $lastSorting=CapabilityLevel::select('urutan')->orderBy('urutan','desc')->first();
        // $capabilityLevel->urutan=$lastSorting? $lastSorting->urutan + 1 : 1;
    }

    /**
     * Handle the CapabilityLevel "updated" event.
     */
    public function updated(CapabilityLevel $capabilityLevel): void
    {
        //
    }

    /**
     * Handle the CapabilityLevel "deleted" event.
     */
    public function deleted(CapabilityLevel $capabilityLevel): void
    {
        //
    }

    /**
     * Handle the CapabilityLevel "restored" event.
     */
    public function restored(CapabilityLevel $capabilityLevel): void
    {
        //
    }

    /**
     * Handle the CapabilityLevel "force deleted" event.
     */
    public function forceDeleted(CapabilityLevel $capabilityLevel): void
    {
        //
    }

    public function creating(CapabilityLevel $capabilityLevel): void
    {
        $lastSorting = CapabilityLevel::select('urutan')->orderBy('urutan', 'desc')->first();
        $capabilityLevel->urutan = $lastSorting ? $lastSorting->urutan + 1 : 1;
    }
}
