<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Eloquent\RequestGroup;
use App\Jobs\RequestJob;
use Illuminate\Console\Command;
use App\Repositories\RequestsRecordRepository;
use App\Repositories\RequestsGroupRepository;

class RequestsProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requets:process {requests=10}';
    protected $groupRepository;
    protected $recordRepository;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        RequestsGroupRepository $groupRepository, 
        RequestsRecordRepository $recordRepository
    )
    {
        parent::__construct();
        $this->groupRepository = $groupRepository;
        $this->recordRepository = $recordRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $requests = $this->argument('requests');
        $now = Carbon::now();
        
        $data = [
            'name' => $now->format('Y-m-d'),
            'total_request' => $requests
        ];

        $group = $this->groupRepository->store($data);

        do {
            $record = $this->recordRepository->store($group, $data);

            $job = (new RequestJob($record));
            dispatch($job);

            $requests--;
        } while ($requests > 0);
    }
}
