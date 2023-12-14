<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use App\Models\AuditTrail;
use App\Models\Room;
use App\Models\Service;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function delete(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $query = Room::find($request->id);
        $check = Service::where('room_id', $request->id)->count();
        if ($check > 0) {
            $rs = SharedFunctions::prompt_msg("The room is in active service and cannot be deleted");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_ROOM, AuditTrail::ACTION_NOTICE, "Room " . $query->name . " deleted but failed due to active service(s)"
            );
            goto end;
        }

        if ($query->delete()) {
            $rs = SharedFunctions::success_msg("Room deleted");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_ROOM, AuditTrail::ACTION_DELETE, "Room " . $query->name . " deleted"
            );
        }
        end: return response()->json($rs);
    }

    public function get()
    {
        $query = Room::orderBy('created_at', 'DESC')->get();
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function save(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'name' => 'required|max:120',
            'description' => 'max:255'
        ]);
        $new_room = false;
        if (isset($request->id)) $room = Room::find($request->id);
        else { $room = new Room(); $new_room = true; }
        $room->name = $request->name;
        $room->description = $request->description;
        if ($room->save()) {
            $rs = SharedFunctions::success_msg("Room saved");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_ROOM, $new_room ? AuditTrail::ACTION_CREATE : AuditTrail::ACTION_UPDATE, "Room " . $request->name . " saved"
            );
        }
        return response()->json($rs);
    }
}
