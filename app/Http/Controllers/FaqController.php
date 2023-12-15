<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use App\Models\AuditTrail;
use App\Models\Faq;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class FaqController extends Controller
{
    public function delete(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $query = Faq::find($request->id);
        if ($query->forceDelete()) {
            $rs = SharedFunctions::success_msg("Help deleted");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_HELP, AuditTrail::ACTION_DELETE, "Help " . $query->question . " deleted"
            );
        }
        return response()->json($rs);
    }

    public function disable(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        
        $query = Faq::find($request->id);

        if ($query->delete()) {
            $rs = SharedFunctions::success_msg('Faq disabled');
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_HELP, AuditTrail::ACTION_UPDATE, "Faq " . $query->question . " disabled"
            );
        }

       
        return response()->json($rs);
    }
    public function enable(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        
        Faq::withTrashed()->find($request->id)->restore();

        $rs = SharedFunctions::success_msg('Faq activated');
        return response()->json($rs);
    }

    public function get()
    {
        $query = Faq::withTrashed()
            ->orderBy('created_at', 'DESC')
            ->get();
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function save(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'question' => 'required|max:120',
            'answer' => 'required|max:255'
        ]);
        $new_help = false;
        if (isset($request->id)) $help = Faq::find($request->id);
        else { $help = new Faq(); $new_help = true; }
        $help->question = $request->question;
        $help->answer = $request->answer;
        if ($help->save()) {
            $rs = SharedFunctions::success_msg("Help saved");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_HELP, $new_help ? AuditTrail::ACTION_CREATE : AuditTrail::ACTION_UPDATE, "Help " . $request->question . " saved"
            );
        }
        return response()->json($rs);
    }
}
