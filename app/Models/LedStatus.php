<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedStatus extends Model
{
    use HasFactory;

    protected $table = 'led_status';

    protected $fillable = [
        'led1',
        'led2',
        'led3',
        'led4',
    ];
}
