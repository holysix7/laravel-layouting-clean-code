<?php

namespace App;

use DateTimeInterface;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UserActivity extends Eloquent
{
    // Set connection
    protected $connection = 'mongodb';

    // Set collection
    protected $collection = 'user_activity';

    // Set fillable fields
    protected $fillable = [
        'latitude',
        'longitude',
        'user_agent',
        'ip_address',
        'user_id',
        'merchant_id',
        'submerchant_id',
        'sof_id',
        'action_path'
    ];

    protected $dates = ['created_at'];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d/m/Y H:i:s');
    }
}
