<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\QboToken;
use Illuminate\Http\Request;
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
     * Instantiate a new QuickbooksController instance.
     */
    public function __construct()
    {
        $this->headers = array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'APP-ID' => 'app-id',
            'APP-KEY' => 'app-key'
        );
        $this->client = new Client([
            'base_uri' => 'https://nafath.api.elm.sa/', // env('QBO_BASE_URL', null),
            'http_errors' => true
        ]);

    }

    public function networkCall($body = null, $method = null, $route = null)
    {


        $searchRequest = new GuzzleRequest($method,
            $route,
            $this->headers,
            $body
        );
                // dd("hamza", $searchRequest);

        $response = $this->client->send($searchRequest);

        return $response->getBody()->getContents();
        // $statusCode = $response->getStatusCode();

        // $decodedResponse = $statusCode == Constants::Unauthorized401 ? $response->getStatusCode() : json_decode( $responseContent, true );



        // return $decodedResponse;
    }


}

