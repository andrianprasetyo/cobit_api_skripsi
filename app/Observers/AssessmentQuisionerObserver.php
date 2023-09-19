<?php

namespace App\Observers;

use App\Models\AssessmentQuisioner;
use App\Models\HistoryActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AssessmentQuisionerObserver
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
     * Handle the AssessmentQuisioner "created" event.
     */
    public function created(AssessmentQuisioner $assessmentQuisioner): void
    {
        $des=null;
        if(!$this->created_by){
            $des='responden kuisioner start';
        }
        HistoryActivity::create([
            'module' => 'AssessmentQuisioner',
            'pk' => $assessmentQuisioner->id,
            'before' => $assessmentQuisioner,
            'action' => 'create',
            'created_by' => $this->created_by,
            'create_by_role' => $this->role,
            'path' => \Request::path(),
            'method' => \Request::method(),
            'description'=>$des
        ]);
    }

    /**
     * Handle the AssessmentQuisioner "updated" event.
     */
    public function updated(AssessmentQuisioner $assessmentQuisioner): void
    {
        HistoryActivity::create([
            'module' => 'AssessmentQuisioner',
            'pk' => $assessmentQuisioner->id,
            'before' => $assessmentQuisioner,
            'after' => $assessmentQuisioner->getChanges(),
            'action' => 'update',
            'created_by' => $this->created_by,
            'create_by_role' => $this->role,
            'path' => \Request::path(),
            'method' => \Request::method()
        ]);
    }

    /**
     * Handle the AssessmentQuisioner "deleted" event.
     */
    public function deleted(AssessmentQuisioner $assessmentQuisioner): void
    {
        HistoryActivity::create([
            'module' => 'AssessmentQuisioner',
            'pk' => $assessmentQuisioner->id,
            'before' => $assessmentQuisioner,
            'action' => 'delete',
            'created_by' => $this->created_by,
            'create_by_role' => $this->role,
            'path' => \Request::path(),
            'method' => \Request::method()
        ]);
    }

    /**
     * Handle the AssessmentQuisioner "restored" event.
     */
    public function restored(AssessmentQuisioner $assessmentQuisioner): void
    {
        //
    }

    /**
     * Handle the AssessmentQuisioner "force deleted" event.
     */
    public function forceDeleted(AssessmentQuisioner $assessmentQuisioner): void
    {
        //
    }
}
