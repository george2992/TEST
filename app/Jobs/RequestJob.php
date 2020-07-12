<?php

namespace App\Jobs;

use App\Eloquent\RequestRecord;
use App\Jobs\Job;
use App\Repositories\RequestsRecordRepository;
use App\Strategies\RequestStrategy;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequestJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $requestRecord;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(RequestRecord $requestRecord)
    {
        $this->requestRecord = $requestRecord;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RequestStrategy $requestStrategy, RequestsRecordRepository $recordRepository)
    {
        $data  = [];

        $response = $requestStrategy->make($data);

        $result = $response->getBody();
        $statusCode = $response->getStatusCode();

        $data = [
            'input' => $data,
            'trace' => $result,
            'http_status' => $statusCode
        ];

        $recordRepository->update($this->requestRecord, $data);

        $group = $this->requestRecord->group->fresh();

        if ($statusCode == 200 || $statusCode == 201) {
            $group->increment('total_successful');
        } else {
            $group->increment('total_errors');
        }

        $processed = $group->total_successful + $group->total_errors;

        if ($processed >= $group->total_request) {
            $group->status = 'COMPLETE';
            $group->save();
        }
    }
}
