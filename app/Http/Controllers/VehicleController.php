<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use App\Models\AuditTrail;
use App\Models\Service;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function delete(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $query = Vehicle::find($request->id);
        $check = Service::where('vehicle_id', $request->id)->count();
        if ($check > 0) {
            $rs = SharedFunctions::prompt_msg("The vehicle is in active service and cannot be deleted");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_VEHICLE, AuditTrail::ACTION_NOTICE, "Vehicle " . $query->name . " deleted but failed due to active service(s)"
            );
            goto end;
        }

        if ($query->delete()) {
            $rs = SharedFunctions::success_msg("Vehicle deleted");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_VEHICLE, AuditTrail::ACTION_DELETE, "Vehicle " . $query->name . " deleted"
            );
        }
        end: return response()->json($rs);
    }

    public function get()
    {
        $query = Vehicle::orderBy('created_at', 'DESC')->get();
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function save(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'max:255',
            'capacity' => 'required|min:1|max:10',
            'transmission' => 'required'
        ]);
        $new_vehicle = false;
        if (isset($request->id)) $vehicle = Vehicle::find($request->id);
        else { $vehicle = new Vehicle(); $new_vehicle = true; }
        $vehicle->name = $request->name;
        $vehicle->description = $request->description;
        $vehicle->capacity = $request->capacity;
        $vehicle->transmission = $request->transmission;
        if ($vehicle->save()) {
            $rs = SharedFunctions::success_msg("Vehicle saved");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_VEHICLE, $new_vehicle ? AuditTrail::ACTION_CREATE : AuditTrail::ACTION_UPDATE, "Vehicle " . $request->name . " saved"
            );
        }
        return response()->json($rs);
    }
}
