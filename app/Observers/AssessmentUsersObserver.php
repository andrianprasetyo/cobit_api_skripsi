<?php

namespace App\Observers;

use App\Models\AssessmentUsers;
use App\Models\HistoryActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AssessmentUsersObserver
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
     * Handle the AssessmentUsers "created" event.
     */
    public function created(AssessmentUsers $assessmentUsers): void
    {
        HistoryActivity::create([
            'module' => 'AssessmentUsers',
            'pk' => $assessmentUsers->id,
            'before' => $assessmentUsers,
            'action' => 'create',
            'created_by' => $this->created_by,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'create_by_role' => $this->role,
        ]);
    }

    /**
     * Handle the AssessmentUsers "updated" event.
     */
    public function updated(AssessmentUsers $assessmentUsers): void
    {
        HistoryActivity::create([
            'module' => 'AssessmentUsers',
            'pk' => $assessmentUsers->id,
            'before' => $assessmentUsers->getOriginal(),
            'after' => $assessmentUsers->getChanges(),
            'action' => 'update',
            'created_by' => $this->created_by,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'create_by_role' => $this->role,
        ]);
    }

    /**
     * Handle the AssessmentUsers "deleted" event.
     */
    public function deleted(AssessmentUsers $assessmentUsers): void
    {
        HistoryActivity::create([
            'module' => 'AssessmentUsers',
            'pk' => $assessmentUsers->id,
            'before' => $assessmentUsers->getOriginal(),
            'action' => 'delete',
            'created_by' => $this->created_by,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'create_by_role' => $this->role,
        ]);
    }

    /**
     * Handle the AssessmentUsers "restored" event.
     */
    public function restored(AssessmentUsers $assessmentUsers): void
    {
        //
    }

    /**
     * Handle the AssessmentUsers "force deleted" event.
     */
    public function forceDeleted(AssessmentUsers $assessmentUsers): void
    {
        //
    }
}
