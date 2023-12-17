<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use App\Models\AuditTrail;
use App\Models\Help;
use App\Models\News;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function delete(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $query = Help::find($request->id);
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
        
        $query = Help::find($request->id);

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
        
        Help::withTrashed()->find($request->id)->restore();

        $rs = SharedFunctions::success_msg('Faq enabled');
        return response()->json($rs);
    }

    public function get()
    {
        $query = Help::orderBy('created_at', 'DESC')
            ->whereNull('deleted_at') 
            ->get();

        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function get_sorted($sort_by)
    {
        $query = Help::withTrashed()
            ->orderBy('created_at', 'DESC')
            ->get();
       
        $rs = SharedFunctions::success_msg('Success');
        $rs['result'] = $query;
        return response()->json($rs);
    }

    public function save(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $form = new Request(json_decode($request->form, true));

        $this->validate($form, [
            'title' => 'required|max:120',
            'description' => 'required|max:255'
        ]);
        $new_help = false;
        if (isset($form->id)) $help = Help::find($form->id);
        else { $help = new Help(); $new_help = true; }
        $help->title = $form->title;
        $help->description = $form->description;
        $help->tags = $form->tags;
        if ($help->save()) {
            $rs = SharedFunctions::success_msg("Help saved");
            SharedFunctions::create_audit_log(
                AuditTrail::CATEGORY_HELP, $new_help ? AuditTrail::ACTION_CREATE : AuditTrail::ACTION_UPDATE, "Help " . $request->question . " saved"
            );
        }
        return response()->json($rs);
    }
}
