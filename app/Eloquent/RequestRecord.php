<?php

namespace App\Eloquent;

use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Model;

class RequestRecord extends Model
{
    protected $table = 'requests_record';

    protected $fillable = [
    	'input',
    	'trace',
        'http_status',
        'attempts'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (RequestRecord $record) {
            $record->uuid = Uuid::uuid();
        });
    }

    public function setInputAttribute($value)
    {
        $this->attributes['input'] = json_encode($value);
    }

    public function getInputAttribute($value)
    {
        return json_decode($value, true);
    }
    
    public function group()
    {
        return $this->belongsTo(RequestGroup::class, 'uuid_group', 'uuid');
    }
}