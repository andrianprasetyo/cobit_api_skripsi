<?php

namespace App\Observers;

use App\Models\QuisionerGrupJawaban;
use App\Models\QuisionerJawaban;

class QuisionerGrupJawabanObserver
{
    /**
     * Handle the QuisionerGrupJawaban "created" event.
     */
    public function created(QuisionerGrupJawaban $quisionerGrupJawaban): void
    {
        //
    }

    /**
     * Handle the QuisionerGrupJawaban "updated" event.
     */
    public function updated(QuisionerGrupJawaban $quisionerGrupJawaban): void
    {
        //
    }

    /**
     * Handle the QuisionerGrupJawaban "deleted" event.
     */
    public function deleted(QuisionerGrupJawaban $quisionerGrupJawaban): void
    {
        QuisionerJawaban::where('quisioner_grup_jawaban_id',$quisionerGrupJawaban->id)->delete();
    }

    /**
     * Handle the QuisionerGrupJawaban "restored" event.
     */
    public function restored(QuisionerGrupJawaban $quisionerGrupJawaban): void
    {
        //
    }

    /**
     * Handle the QuisionerGrupJawaban "force deleted" event.
     */
    public function forceDeleted(QuisionerGrupJawaban $quisionerGrupJawaban): void
    {
        //
    }
}
