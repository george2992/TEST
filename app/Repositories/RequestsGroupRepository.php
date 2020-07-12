<?php
namespace App\Repositories;

use App\Eloquent\RequestGroup;
use Illuminate\Support\Facades\DB;

class RequestsGroupRepository
{
    public function store(array $data)
    {
        $group = DB::transaction(function () use ($data) {
            $group = new RequestGroup();
            $group->fill($data);
            $group->save();

            return $group;
        });

        return $group;
    }
}
