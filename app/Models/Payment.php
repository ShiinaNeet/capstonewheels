<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    const MOP_CASH = 0;
    const MOP_PAYMONGO = 1;

    const STATUS_PENDING = 0;
    const STATUS_VERIFIED = 1;
}
