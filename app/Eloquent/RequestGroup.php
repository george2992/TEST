<?php

namespace App\Eloquent;

use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Model;

class RequestGroup extends Model
{
    protected $table = 'requests_group';

    protected $fillable = [
    	'name',
    	'total_request',
    	'total_successful',
    	'total_errors'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (RequestGroup $group) {
            $group->uuid = Uuid::uuid();
        });
    }

    public function records()
    {
        return $this->hasMany(RequestRecord::class, 'uuid_group', 'uuid');
    }
}