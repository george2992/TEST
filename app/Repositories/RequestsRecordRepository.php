<?php
namespace App\Repositories;

use App\Eloquent\RequestGroup;
use App\Eloquent\RequestRecord;
use Illuminate\Support\Facades\DB;

class RequestsRecordRepository
{
    public function store(RequestGroup $group, array $data)
    {
        $record = DB::transaction(function () use ($group, $data) {
            $record = new RequestRecord($data);

            $group->records()->save($record);

            return $record;
        });

        return $record->fresh();
    }

    public function update(RequestRecord $record, array $data)
    {
        $record = DB::transaction(function () use ($record, $data) {
            $record->update($data);

            return $record;
        });

        return $record->fresh();
    }
}
