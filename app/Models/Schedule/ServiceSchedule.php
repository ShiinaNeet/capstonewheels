<?php

namespace App\Models\Schedule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceSchedule extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 0;
    const STATUS_COMPLETE = 1;
    const STATUS_CANCELLED = 2;

    const STATUS = ['active', 'complete', 'cancelled'];

    protected $fillable = ['status'];
}
