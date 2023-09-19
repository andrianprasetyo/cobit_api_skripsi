<?php

namespace App\Observers;

use App\Models\QuisionerHasil;
use App\Models\HistoryActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class QuisionerHasilObserver
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
     * Handle the QuisionerHasil "created" event.
     */
    public function created(QuisionerHasil $quisionerHasil): void
    {
        $des = null;
        if (!$this->created_by) {
            $des = 'jawaban kuisioner responden';
        }

        HistoryActivity::create([
            'module' => 'QuisionerHasil',
            'pk' => $quisionerHasil->id,
            'before' => $quisionerHasil,
            'action' => 'create',
            'created_by' => $this->created_by,
            'create_by_role' => $this->role,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'description' => $des
        ]);
    }

    /**
     * Handle the QuisionerHasil "updated" event.
     */
    public function updated(QuisionerHasil $quisionerHasil): void
    {
        HistoryActivity::create([
            'module' => 'AssessmentQuisioner',
            'pk' => $quisionerHasil->id,
            'before' => $quisionerHasil,
            'after' => $quisionerHasil->getChanges(),
            'action' => 'update',
            'created_by' => $this->created_by,
            'create_by_role' => $this->role,
            'path' => \Request::path(),
            'method' => \Request::method()
        ]);
    }

    /**
     * Handle the QuisionerHasil "deleted" event.
     */
    public function deleted(QuisionerHasil $quisionerHasil): void
    {
        //
    }

    /**
     * Handle the QuisionerHasil "restored" event.
     */
    public function restored(QuisionerHasil $quisionerHasil): void
    {
        //
    }

    /**
     * Handle the QuisionerHasil "force deleted" event.
     */
    public function forceDeleted(QuisionerHasil $quisionerHasil): void
    {
        //
    }
}
