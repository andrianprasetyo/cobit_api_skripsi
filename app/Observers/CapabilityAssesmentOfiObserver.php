<?php

namespace App\Observers;

use App\Models\CapabilityAssesmentOfi;
use App\Models\HistoryActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CapabilityAssesmentOfiObserver
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
     * Handle the CapabilityAssesmentOfi "created" event.
     */
    public function created(CapabilityAssesmentOfi $capabilityAssesmentOfi): void
    {
        HistoryActivity::create([
            'module' => 'CapabilityAssesmentOfi',
            'pk' => $capabilityAssesmentOfi->id,
            'before' => $capabilityAssesmentOfi,
            'action' => 'create',
            'created_by' => $this->created_by,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'create_by_role' => $this->role,
        ]);
    }

    /**
     * Handle the CapabilityAssesmentOfi "updated" event.
     */
    public function updated(CapabilityAssesmentOfi $capabilityAssesmentOfi): void
    {
        HistoryActivity::create([
            'module' => 'CapabilityAssesmentOfi',
            'pk' => $capabilityAssesmentOfi->id,
            'before' => $capabilityAssesmentOfi->getOriginal(),
            'after' => $capabilityAssesmentOfi->getChanges(),
            'action' => 'update',
            'created_by' => $this->created_by,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'create_by_role' => $this->role,
        ]);
    }

    /**
     * Handle the CapabilityAssesmentOfi "deleted" event.
     */
    public function deleted(CapabilityAssesmentOfi $capabilityAssesmentOfi): void
    {
        HistoryActivity::create([
            'module' => 'CapabilityAssesmentOfi',
            'pk' => $capabilityAssesmentOfi->id,
            'before' => $capabilityAssesmentOfi->getOriginal(),
            'action' => 'delete',
            'created_by' => $this->created_by,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'create_by_role' => $this->role,
        ]);
    }

    /**
     * Handle the CapabilityAssesmentOfi "restored" event.
     */
    public function restored(CapabilityAssesmentOfi $capabilityAssesmentOfi): void
    {
        //
    }

    /**
     * Handle the CapabilityAssesmentOfi "force deleted" event.
     */
    public function forceDeleted(CapabilityAssesmentOfi $capabilityAssesmentOfi): void
    {
        //
    }
}
