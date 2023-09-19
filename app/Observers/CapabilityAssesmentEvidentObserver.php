<?php

namespace App\Observers;

use App\Models\CapabilityAssesmentEvident;
use App\Models\HistoryActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CapabilityAssesmentEvidentObserver
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
     * Handle the CapabilityAssesmentEvident "created" event.
     */
    public function created(CapabilityAssesmentEvident $capabilityAssesmentEvident): void
    {
        HistoryActivity::create([
            'module' => 'CapabilityAssesmentEvident',
            'pk' => $capabilityAssesmentEvident->id,
            'before' => $capabilityAssesmentEvident,
            'action' => 'create',
            'created_by' => $this->created_by,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'create_by_role' => $this->role,
        ]);
    }

    /**
     * Handle the CapabilityAssesmentEvident "updated" event.
     */
    public function updated(CapabilityAssesmentEvident $capabilityAssesmentEvident): void
    {
        HistoryActivity::create([
            'module' => 'CapabilityAssesmentEvident',
            'pk' => $capabilityAssesmentEvident->id,
            'before' => $capabilityAssesmentEvident->getOriginal(),
            'after' => $capabilityAssesmentEvident->getChanges(),
            'action' => 'update',
            'created_by' => $this->created_by,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'create_by_role' => $this->role,
        ]);
    }

    /**
     * Handle the CapabilityAssesmentEvident "deleted" event.
     */
    public function deleted(CapabilityAssesmentEvident $capabilityAssesmentEvident): void
    {
        HistoryActivity::create([
            'module' => 'CapabilityAssesmentEvident',
            'pk' => $capabilityAssesmentEvident->id,
            'before' => $capabilityAssesmentEvident->getOriginal(),
            'action' => 'delete',
            'created_by' => $this->created_by,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'create_by_role' => $this->role,
        ]);
    }

    /**
     * Handle the CapabilityAssesmentEvident "restored" event.
     */
    public function restored(CapabilityAssesmentEvident $capabilityAssesmentEvident): void
    {
        //
    }

    /**
     * Handle the CapabilityAssesmentEvident "force deleted" event.
     */
    public function forceDeleted(CapabilityAssesmentEvident $capabilityAssesmentEvident): void
    {
        //
    }
}
