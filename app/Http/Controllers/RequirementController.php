<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use App\Models\AuditTrail;
use App\Models\Requirement;
use Illuminate\Http\Request;

class RequirementController extends Controller
{
    public function delete(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $query = Requirement::find($request->id);
        if ($query->delete()) {
            $rs = SharedFunctions::success_msg("Requirement deleted");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_REQUIREMENT, AuditTrail::ACTION_DELETE, "Requirement " . $request->title . " deleted"
            );
        }
        return response()->json($rs);
    }

    public function get()
    {
        $query = Requirement::orderBy('created_at', 'DESC')->get();
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
        $new_requirement = false;
        if (isset($request->id)) $query = Requirement::find($request->id);
        else { $query = new Requirement(); $new_requirement = true; }
        $query->name = $request->name;
        $query->description = $request->description;
        if ($query->save()) {
            $rs = SharedFunctions::success_msg("Requirement saved");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_REQUIREMENT, $new_requirement ? AuditTrail::ACTION_CREATE : AuditTrail::ACTION_UPDATE, "Requirement " . $request->name . " saved"
            );
        }
        return response()->json($rs);
    }
}
