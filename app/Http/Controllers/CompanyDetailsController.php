<?php

namespace App\Http\Controllers;

use App\Libraries\SharedFunctions;
use App\Models\CompanyDetails;
use Illuminate\Http\Request;

class CompanyDetailsController extends Controller
{
    public function save(Request $request)
    {
        $rs = SharedFunctions::default_msg();
        $this->validate($request, [
            'vision' => 'required',
            'mission' => 'required',
            'contact_number' => 'required',
            'email' => 'required|max:120|regex:/(.+)@(.+)\.(.+)/i',
            'business_hours' => 'required',
            'terms' => 'required',
        ]);

        $companyDetails = CompanyDetails::findOrNew(1);
        $companyDetails->id = 1;
        $companyDetails->vision = $request->vision;
        $companyDetails->mission = $request->mission;
        $companyDetails->contact_number = $request->contact_number;
        $companyDetails->email = $request->email;
        $companyDetails->business_hours = $request->business_hours;
        $companyDetails->terms = $request->terms;
        $companyDetails->save();

        $rs = SharedFunctions::success_msg("Company details saved");
        return response()->json($rs);
    }

    public function get()
    {
        $companyDetails = CompanyDetails::where('id', '=', 1)->get();
        $rs = SharedFunctions::success_msg();
        $rs['result'] = $companyDetails;
        return response()->json($rs);
    }

}
