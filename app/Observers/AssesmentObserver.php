<?php

namespace App\Observers;

use App\Models\Assesment;
use App\Models\HistoryActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AssesmentObserver
{
    private $created_by,$role;

    public function __construct()
    {
        if(Auth::check()){
            $this->created_by=Auth::user()->id;
            $this->role = Auth::user()->roleaktif->role->code;
        }
    }
    /**
     * Handle the Assesment "created" event.
     */
    public function created(Assesment $assesment): void
    {
        HistoryActivity::create([
            'module' => 'Assesment',
            'pk' => $assesment->id,
            'before' => $assesment,
            'action' => 'create',
            'created_by' => $this->created_by,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'create_by_role' => $this->role,
        ]);
    }

    /**
     * Handle the Assesment "updated" event.
     */
    public function updated(Assesment $assesment): void
    {
        HistoryActivity::create([
            'module' => 'Assesment',
            'pk' => $assesment->id,
            'before' => $assesment->getOriginal(),
            'after' => $assesment->getChanges(),
            'action' => 'update',
            'created_by' => $this->created_by,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'create_by_role' => $this->role,
        ]);
    }

    /**
     * Handle the Assesment "deleted" event.
     */
    public function deleted(Assesment $assesment): void
    {
        HistoryActivity::create([
            'module' => 'Assesment',
            'pk' => $assesment->id,
            'before' => $assesment->getOriginal(),
            'action' => 'delete',
            'created_by' => $this->created_by,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'create_by_role' => $this->role,
        ]);
    }

    /**
     * Handle the Assesment "restored" event.
     */
    public function restored(Assesment $assesment): void
    {
        //
    }

    /**
     * Handle the Assesment "force deleted" event.
     */
    public function forceDeleted(Assesment $assesment): void
    {
        //
    }
}
