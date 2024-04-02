<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;


class NafathController extends NetworkLayer
{
    //
    public function createRequest(Request $request){

        // dd("createRequest");

        //  if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }
        $nationalId = $request->input('national_id');
        $locale = $request->input('locale', 'ar');
        $requestId = Str::uuid()->toString(); //$request->input('request_id', uniqid());
        // $requestId = 'd5e189a6-c551-43bb-b893-bd70e92c1fef';
        // $callbackurl = env('SITE_URL')."/api/nafath-callback";

        $body = [
                'nationalId' => $nationalId,
                'service' => "Login"
            //
        ];
        // dd(json_encode($body));
        $end_point = "stg/api/v1/mfa/request?local=$locale&requestId=".$requestId;
        // dd($end_point);
        return $this->networkCall(json_encode($body), "POST", $end_point);




    }

    public function requestStatus(Request $request){
        dd("requestStatus");
    }
}
