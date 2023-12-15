<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function get(Request $request)
    {
        $notifs = DB::table('notifications')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'DESC')
            ->limit($request->limit)
            ->get();
        $rs = SharedFunctions::success_msg("");
        $rs['result'] = $notifs;
        return response()->json($rs);
    }

    public function mark_as_read()
    {
        DB::table('notifications')->where('user_id', Auth::id())
            ->whereNull('read_at')->orderBy('created_at')
            ->update(['read_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $rs = SharedFunctions::success_msg("");
        return response()->json($rs);
    }
    public function mark_single_notif_as_read(Request $request)
    {
        DB::table('notifications')->where('user_id', Auth::id())
            ->whereNull('read_at')->orderBy('created_at')
            ->where('id',$request->id)
            ->update(['read_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        $rs = SharedFunctions::success_msg("");
        return response()->json($rs);
    }
}
