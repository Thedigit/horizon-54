<?php

namespace Thedigit\Horizon\Listeners;

use Cake\Chronos\Chronos;
use Thedigit\Horizon\Contracts\JobRepository;
use Thedigit\Horizon\Events\MasterSupervisorLooped;

class TrimRecentJobs
{
    /**
     * The last time the recent jobs were trimmed.
     *
     * @var \Cake\Chronos\Chronos
     */
    public $lastTrimmed;

    /**
     * How many minutes to wait in between each trim.
     *
     * @var int
     */
    public $frequency = 5;

    /**
     * Handle the event.
     *
     * @param  \Thedigit\Horizon\Events\MasterSupervisorLooped  $event
     * @return void
     */
    public function handle(MasterSupervisorLooped $event)
    {
        if (! isset($this->lastTrimmed)) {
            $this->frequency = max(1, intdiv(
                config('horizon.trim.recent', 60), 12
            ));

            $this->lastTrimmed = Chronos::now()->subMinutes($this->frequency + 1);
        }

        if ($this->lastTrimmed->lte(Chronos::now()->subMinutes($this->frequency))) {
            app(JobRepository::class)->trimRecentJobs();

            $this->lastTrimmed = Chronos::now();
        }
    }
}
