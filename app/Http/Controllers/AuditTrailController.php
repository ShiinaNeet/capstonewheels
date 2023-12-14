<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use App\Models\AuditTrail;
// use Illuminate\Support\Facades\DB;

class AuditTrailController extends Controller
{
    public function get()
    {
        $query = AuditTrail::leftJoin('users', 'audit_trails.action_user_id', '=', 'users.id')
            // ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->select('users.email')
            // ->addSelect(DB::raw("CONCAT(user_details.lastname, ', ', user_details.firstname) AS name"))
            ->addSelect('audit_trails.category', 'audit_trails.action_type', 'audit_trails.action_description', 'audit_trails.created_at')
            ->orderBy('audit_trails.created_at', 'DESC')->get();
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }
}
