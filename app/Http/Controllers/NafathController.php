<?php

namespace App\Http\Controllers;

use App\Models\Nafath;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;



class NafathController extends NetworkLayer
{
    //
    public function createRequest(Request $request){

        $validator = Validator::make($request->all(), [
            'national_id' => 'required|min:10|max:10', //|unique:nafath
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try{
        $nationalId = $request->input('national_id');
        $locale = 'en';
        $requestId = Str::uuid()->toString();
        $end_point = "stg/api/v1/mfa/request?local=$locale&requestId=".$requestId;
        $service  = "openAccount";



        $body = [
                'nationalId' => $nationalId,
                'service' => $service
            //
        ];

        $response =  $this->networkCall(json_encode($body), "POST", $end_point);

        $isTransId = property_exists($response, 'transId');
        var_dump($isTransId, $response);
        if($isTransId){
            $nafath = new Nafath();
            $nafath->national_id = $nationalId;
            $nafath->service = $service;
            $nafath->locale = $locale;
            $nafath->request_id = $requestId;
            $nafath->trans_id = $response->transId;
            $nafath->random_token = $response->random;
            $nafath->save();


            return response()->json([
                        'data' => Nafath::where('trans_id', $response->transId)->get(),
                        'message' => "TransId and Random generated successfully"
                    ], 200);

        }

        return $response;

        }
        catch (\Throwable $e) {
                return response()->json(['error' => $e->getMessage()], 500);
        }



    }

    public function requestStatus(Request $request){
        dd("requestStatus");
    }
}
