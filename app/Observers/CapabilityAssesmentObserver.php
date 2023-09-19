<?php

namespace App\Observers;

use App\Models\CapabilityAssesment;
use App\Models\HistoryActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CapabilityAssesmentObserver
{

    private $created_by, $role;

    public function __construct()
    {
        if (Auth::check()) {
            $this->created_by = Auth::user()->id;
            $this->role = Auth::user()->roleaktif->role->code;
        }
    }
    /**
     * Handle the CapabilityAssesment "created" event.
     */
    public function created(CapabilityAssesment $capabilityAssesment): void
    {
        HistoryActivity::create([
            'module' => 'CapabilityAssesment',
            'pk' => $capabilityAssesment->id,
            'before' => $capabilityAssesment,
            'action' => 'create',
            'created_by' => $this->created_by,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'create_by_role' => $this->role,
        ]);
    }

    /**
     * Handle the CapabilityAssesment "updated" event.
     */
    public function updated(CapabilityAssesment $capabilityAssesment): void
    {
        HistoryActivity::create([
            'module' => 'CapabilityAssesment',
            'pk' => $capabilityAssesment->id,
            'before' => $capabilityAssesment->getOriginal(),
            'after' => $capabilityAssesment->getChanges(),
            'action' => 'update',
            'created_by' => $this->created_by,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'create_by_role' => $this->role,
        ]);
    }

    /**
     * Handle the CapabilityAssesment "deleted" event.
     */
    public function deleted(CapabilityAssesment $capabilityAssesment): void
    {
        HistoryActivity::create([
            'module' => 'CapabilityAssesment',
            'pk' => $capabilityAssesment->id,
            'before' => $capabilityAssesment->getOriginal(),
            'action' => 'delete',
            'created_by' => $this->created_by,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'create_by_role' => $this->role,
        ]);
    }

    /**
     * Handle the CapabilityAssesment "restored" event.
     */
    public function restored(CapabilityAssesment $capabilityAssesment): void
    {
        //
    }

    /**
     * Handle the CapabilityAssesment "force deleted" event.
     */
    public function forceDeleted(CapabilityAssesment $capabilityAssesment): void
    {
        //
    }
}
