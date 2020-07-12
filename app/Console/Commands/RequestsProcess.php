<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Eloquent\RequestGroup;
use App\Jobs\RequestJob;
use Illuminate\Console\Command;
use App\Repositories\RequestsRecordRepository;
use App\Repositories\RequestsGroupRepository;
use Symfony\Component\Console\Helper\ProgressBar;

class RequestsProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requets:process {requests=100000}';
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

        ProgressBar::setFormatDefinition('progress', '%message% %current%/%max% <info>%num%</info>');
        $progress = new ProgressBar($this->getOutput());
        $progress->setFormat('progress');
        $progress->setMessage('Calculado...');
        $progress->start($requests);
        
        do {
            $progress->setMessage($requests, 'num');
            $progress->advance(1);

            $record = $this->recordRepository->store($group, $data);

            $job = (new RequestJob($record));
            dispatch($job);

            $requests--;
            $progress->advance(0);
        } while ($requests > 0);

        $progress->setMessage('Terminado');
        $progress->setMessage('', 'num');
        $progress->finish();
        $this->line('');
    }
}
