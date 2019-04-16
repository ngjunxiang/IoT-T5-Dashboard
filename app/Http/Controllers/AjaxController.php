<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function WeekUrl(Request $request)
    {
        $client = new Client();
        $response = $client->get(env('API_HOST') . '/api/liveimage?order=desc&aggregate=week');
        if ($response->getStatusCode() === 200) {
            $decodedResponse = json_decode($response->getBody()->getContents(), true);
            if ($decodedResponse['success'] && $decodedResponse['status'] = 200) {
                return $decodedResponse;
            }
        }

        return null;
    }

    public function MonthUrl(Request $request)
    {
        $client = new Client();
        $response = $client->get(env('API_HOST') . '/api/liveimage?order=desc&aggregate=month');
        if ($response->getStatusCode() === 200) {
            $decodedResponse = json_decode($response->getBody()->getContents(), true);
            if ($decodedResponse['success'] && $decodedResponse['status'] = 200) {
                return $decodedResponse;
            }
        }

        return null;
    }

    public function WeekUrlAsc(Request $request)
    {
        $client = new Client();
        $response = $client->get(env('API_HOST') . '/api/liveimage?order=asc&aggregate=week');
        if ($response->getStatusCode() === 200) {
            $decodedResponse = json_decode($response->getBody()->getContents(), true);
            if ($decodedResponse['success'] && $decodedResponse['status'] = 200) {
                return $decodedResponse;
            }
        }

        return null;
    }

    public function MonthUrlAsc(Request $request)
    {
        $client = new Client();
        $response = $client->get(env('API_HOST') . '/api/liveimage?order=asc&aggregate=month');
        if ($response->getStatusCode() === 200) {
            $decodedResponse = json_decode($response->getBody()->getContents(), true);
            if ($decodedResponse['success'] && $decodedResponse['status'] = 200) {
                return $decodedResponse;
            }
        }

        return null;
    }
}
