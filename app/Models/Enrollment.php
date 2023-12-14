<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    const STATUS_CANCELLED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_PENDING = 3;

    protected $fillable = ['status'];
}
