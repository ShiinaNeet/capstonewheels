<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    use HasFactory;

    const USER_SYSTEM     = NULL;
    const USER_ADMIN      = User::TYPE_ADMIN - 2;      // 1
    const USER_SECRETARY  = User::TYPE_SECRETARY;      // 2
    const USER_INSTRUCTOR = User::TYPE_INSTRUCTOR + 2; // 3
    const USER_STUDENT    = User::TYPE_STUDENT + 4;    // 4

    const CATEGORY_ACCOUNT     = 0;
    const CATEGORY_NEWS        = 1;
    const CATEGORY_HELP        = 2;
    const CATEGORY_SERVICE     = 3;
    const CATEGORY_SCHEDULE    = 4;
    const CATEGORY_REQUIREMENT = 5;
    const CATEGORY_ROOM        = 6;
    const CATEGORY_VEHICLE     = 7;
    const CATEGORY_DATABASE    = 8;
    const CATEGORY_ENROLLMENT  = 9;
    const CATEGORY_PAYMENT     = 10;
    const CATEGORY_REPORT      = 11;
    const CATEGORY_INQUIRY     = 12;

    const ACTION_CREATE = 0;
    const ACTION_UPDATE = 1;
    const ACTION_DELETE = 2;
    const ACTION_NOTICE = 3;
}
