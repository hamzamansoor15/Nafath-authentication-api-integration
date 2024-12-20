<?php

namespace App\Http\Controllers;

use Config;
use GuzzleHttp\Client;
use App\Utilities\Constants;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NetworkLayer extends Controller
{
    //


    /**
     * .
     */
    protected $headers;


    /**
     * Instantiate a new NetworkController instance.
     */
    public function __construct()
    {
        $appId = null;
        $appKey = null;
        $baseUrl = null;


        if(env('APP_ENV') == 'Production'){
            $appId = Config::get('services.nafath.prod_app_id');
            $appKey = Config::get('services.nafath.prod_app_key');
            $baseUrl = Config::get('services.nafath.prod_base_url');


        }else{
            $appId = Config::get('services.nafath.stage_app_id');
            $appKey = Config::get('services.nafath.prod_app_key');
            $baseUrl = Config::get('services.nafath.stage_base_url');

        }

        $this->headers = array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'APP-ID' => $appId,
            'APP-KEY' => $appKey
        );
        $this->client = new Client([
            'base_uri' => $baseUrl, //'https://nafath.api.elm.sa/',
            'http_errors' => true
        ]);

    }

    public function networkCall($body = null, $method = null, $route = null)
    {
        // dd("client", $this->client);
        // dd("body", $body, $method, $route);


        try{
            $searchRequest = new GuzzleRequest($method,
            $route,
            $this->headers,
            $body
        );
        // dd("searchRequest", $searchRequest);

        $response = $this->client->send($searchRequest);

        $responseContent = $response->getBody()->getContents();

        $responseContentDecoded = json_decode($responseContent);

        return $responseContentDecoded;

        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }


    }


}

