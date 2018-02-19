<?php

namespace Vzool\Horizon\Console;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Vzool\Horizon\MasterSupervisor;
use Vzool\Horizon\Contracts\MasterSupervisorRepository;

class TerminateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'horizon:terminate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Terminate the master supervisor so it can be restarted';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $masters = resolve(MasterSupervisorRepository::class)->all();

        $masters = collect($masters)->filter(function ($master) {
            return Str::startsWith($master->name, MasterSupervisor::basename());
        })->all();

        foreach (array_pluck($masters, 'pid') as $processId) {
            $this->info("Sending TERM Signal To Process: {$processId}");

            posix_kill($processId, SIGTERM);
        }
    }
}
