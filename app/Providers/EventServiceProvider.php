<?php

namespace App\Providers;

use App\Models\Assesment;
use App\Models\AssessmentQuisioner;
use App\Models\AssessmentUsers;
use App\Models\CapabilityAssesment;
use App\Models\CapabilityAssesmentEvident;
use App\Models\CapabilityAssesmentOfi;
use App\Models\QuisionerGrupJawaban;
use App\Observers\AssesmentObserver;
use App\Observers\AssessmentQuisionerObserver;
use App\Observers\AssessmentUsersObserver;
use App\Observers\CapabilityAssesmentEvidentObserver;
use App\Observers\CapabilityAssesmentObserver;
use App\Observers\CapabilityAssesmentOfiObserver;
use App\Observers\QuisionerGrupJawabanObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        QuisionerGrupJawaban::observe(QuisionerGrupJawabanObserver::class);
        Assesment::observe(AssesmentObserver::class);
        CapabilityAssesment::observe(CapabilityAssesmentObserver::class);
        CapabilityAssesmentEvident::observe(CapabilityAssesmentEvidentObserver::class);
        CapabilityAssesmentOfi::observe(CapabilityAssesmentOfiObserver::class);
        AssessmentQuisioner::observe(AssessmentQuisionerObserver::class);
        AssessmentUsers::observe(AssessmentUsersObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
