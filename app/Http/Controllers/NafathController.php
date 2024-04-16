<?php

namespace App\Http\Controllers;

use App\Models\Nafath;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\KEY;



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
        $service  = "RequestDigitalServicesEnrollment";



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

         $validator = Validator::make($request->all(), [
            'national_id' => 'required|min:10|max:10|exists:nafath,national_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try{

            $nafath = Nafath::where('national_id', $request->input('national_id') )->first();
            $body = [
                'nationalId' => $nafath->national_id,
                "transId" => $nafath->trans_id,
                "random" => $nafath->random_token
            ];
            $end_point = "stg/api/v1/mfa/request/status";

            $status =  $this->networkCall(json_encode($body), "POST", $end_point);

            return response()->json([
                    'data' => $status,
                    'message' => "Status Retrieved successfully"
                ], 200);


        dd("requestStatus", $nafath);
        }
        catch (\Throwable $e) {
                return response()->json(['error' => $e->getMessage()], 500);
        }


    }


    public function getJwtByNationalId(Request $request){

         $validator = Validator::make($request->all(), [
            'national_id' => 'required|min:10|max:10|exists:nafath,national_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try{

            $jwt = Nafath::where('national_id', $request->input('national_id') )->first();


            return response()->json([
                    'data' => $jwt,
                    'message' => "Nafath Details Retrieved successfully"
                ], 200);

        }
        catch (\Throwable $e) {
                return response()->json(['error' => $e->getMessage()], 500);
        }


    }

    public function getJwk(Request $request){

        try{
            $end_point = "stg/api/v1/mfa/jwk";
            $response =  $this->networkCall(null, "GET", $end_point);

            return response()->json([
                'data' => $response,
                'message' => "Jwk retrieved successfully"
            ], 200);


        }
        catch (\Throwable $e) {
                return response()->json(['error' => $e->getMessage()], 500);
        }


    }


    public function nafathCallBackURL(Request $request){
         \Log::channel('nafath')->info("Hit coming");
        $encoded_data = json_encode($request->all());
        \Log::channel('nafath')->info("Data: ". $encoded_data);
        return;

        // $encoded_data = json_encode($request->all());
        // $data = $request->all();
        // $token = $data['token'];
        // $requestId = $data['requestId'];
        // $transId = $data['transId'];
        // $nafath_data = (json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))));
        // $user = user::where('transid', $transId)->where('requestid', $requestId)->first();

        // if(!empty($user)){
        //     $user->nafath = json_encode($nafath_data);
        //     $user->name = $nafath_data->englishFirstName. ' '. $nafath_data->englishLastName;
        //     $user->dob = Carbon::createFromFormat('m-d-Y', $nafath_data->dateOfBirthG)->format('Y-m-d');
        //     $user->save();
        // }else{

        // }

        // $data = '{"token":"eyJraWQiOiJlbG0iLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiIxMDY3MzA1NDU2IiwiZmF0aGVyTmFtZSI6ItmF2K3ZhdivIiwiZ2VuZGVyIjoiTSIsImRhdGVPZkJpcnRoRyI6IjA0LTExLTE5OTAiLCJ0cmFuc0lkIjoiYjUwYTRlYTYtM2M0Yy00YTc1LTlhOGQtNzk2Zjc0YTJkNWU4IiwiZGF0ZU9mQmlydGhIIjoiMTctMDQtMTQxMSIsImdyYW5kRmF0aGVyTmFtZSI6ItmF2LXYt9mB2YkiLCJpc3MiOiJodHRwczpcL1wvbmFmYXRoLmFwaS5lbG0uc2EiLCJuYXRpb25hbGl0eUNvZGUiOiIxMTMiLCJuaW4iOiIxMDY3MzA1NDU2IiwiZW5nbGlzaFRoaXJkTmFtZSI6Ik1VU1RBRkEiLCJpZFZlcnNpb25OdW1iZXIiOjYsImZhbWlseU5hbWUiOiLYp9mE2KzZh9mG2YoiLCJsb2dJZCI6MTkyMzQ2NjQ0MywiZXhwIjoxNjg3NTk5ODYzLCJpZElzc3VlUGxhY2UiOiLYp9mE2K_Ysdi52YrYqSIsImlhdCI6MTY4NzU5OTcxMywianRpIjoiNjQ5NmJhMjYxMTE3OSIsIm5hdGlvbmFsQWRkcmVzcyI6W3sic3RyZWV0TmFtZSI6IkFobWFkIFNhbWVoIEFsIEtoYWxkaSIsImNpdHkiOiJKRUREQUgiLCJhZGRpdGlvbmFsTnVtYmVyIjoiNDA5NiIsImRpc3RyaWN0IjoiQWwgTXVyamFuIERpc3QuIiwidW5pdE51bWJlciI6IjUiLCJpc1ByaW1hcnlBZGRyZXNzIjoiZmFsc2UiLCJidWlsZGluZ051bWJlciI6IjY3MjciLCJwb3N0Q29kZSI6IjIzNzE1IiwibG9jYXRpb25Db29yZGluYXRlcyI6IjM5LjEwNjIyMDA3IDIxLjY5NzI0ODE4In0seyJzdHJlZXROYW1lIjoiUHJpbmNlIFNhdWQgQmluICBNdWhhbW1hZCBCaW4gIFNhdWQiLCJjaXR5IjoiUklZQURIIiwiYWRkaXRpb25hbE51bWJlciI6Ijc5MjIiLCJkaXN0cmljdCI6IktpbmcgRmFoZCBEaXN0LiIsInVuaXROdW1iZXIiOiI1NSIsImlzUHJpbWFyeUFkZHJlc3MiOiJmYWxzZSIsImJ1aWxkaW5nTnVtYmVyIjoiMzE4OSIsInBvc3RDb2RlIjoiMTIyNzQiLCJsb2NhdGlvbkNvb3JkaW5hdGVzIjoiNDYuNjcwMjc3MzggMjQuNzQ2NDU3NjYifV0sImlkSXNzdWVEYXRlIjoiMjctMTItMTQ0MSIsImlkRXhwaXJ5RGF0ZUciOiIyMi0wNi0yMDI1IiwiZW5nbGlzaExhc3ROYW1lIjoiQUxKVUhBTkkiLCJlbmdsaXNoRmlyc3ROYW1lIjoiQkFERVIiLCJpZEV4cGlyeURhdGUiOiIyNi0xMi0xNDQ2IiwiYXVkIjoiaHR0cHM6XC9cL2JhY2tlbmQuZG5hbmVlci5jb21cL2FwaVwvbmFmYXRoLWNhbGxiYWNrIiwiZmlyc3ROYW1lIjoi2KjYr9ixIiwibmJmIjoxNjg3NTk5NzEzLCJQZXJzb25JZCI6MTA2NzMwNTQ1NiwibmF0aW9uYWxpdHkiOiJTQVUiLCJTZXJ2aWNlTmFtZSI6Ik9wZW5BY2NvdW50Iiwiandrc191cmkiOiJodHRwczpcL1wvbmFmYXRoLmFwaS5lbG0uc2FcL2FwaVwvdjFcL21mYVwvandrIiwiaWRJc3N1ZURhdGVHIjoiMTctMDgtMjAyMCIsImVuZ2xpc2hTZWNvbmROYW1lIjoiTU9IQU1NRUQiLCJzdGF0dXMiOiJDT01QTEVURUQifQ.oAadSrcoMBMveeRy4EdEehzWviSoNBFm9OP1lqJ5ydwo_f2MA91wgcwFa8ZC0c8JRMy70IiL79gjj01RAKLfYIn6jdvVdSGlnU0V20LqZeSjq4J9mRjTD8ySjhOhYKeKNfXqSwoLAALCsx8Im_wy6S5KS6ZNsnfzS6tkw56_HxQC5TmUhsCbDtu3KekS4D5j0aQnMJKZndP1wpCHeCj9e8nt41ztuCNSiBSyXQ8fwvoJxDW3DR_30wMfw4qOVu_5qvVTqWo_mVq0uTunlVFGzs96XT6zv0vITzJROdHqmkPN7nrZMoxHHP7T1GvX2hIbPt4ahc6SYcq_k4o0_qzrxQ","transId":"b50a4ea6-3c4c-4a75-9a8d-796f74a2d5e8","requestId":"6496ba2611179"}';



        // return (json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $data)[1])))));



    }

}
