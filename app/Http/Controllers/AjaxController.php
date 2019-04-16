<?php

namespace App\Http\Controllers;

use Auth;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function WeekUrl(Request $request)
    {
        $client = new Client();
        $response = $client->get(env('API_HOST') . '/api/liveimage?order=desc&aggregate=week&limit=5000');
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
        $response = $client->get(env('API_HOST') . '/api/liveimage?order=desc&aggregate=month&limit=5000');
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
        $response = $client->get(env('API_HOST') . '/api/liveimage?order=asc&aggregate=week&from=2019-03-25T00:00:00&to=2019-04-15T00:00:00');
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
        $response = $client->get(env('API_HOST') . '/api/liveimage?order=asc&aggregate=month&from=2019-01-01T00:00:00&to=2019-04-15T00:00:00');
        if ($response->getStatusCode() === 200) {
            $decodedResponse = json_decode($response->getBody()->getContents(), true);
            if ($decodedResponse['success'] && $decodedResponse['status'] = 200) {
                return $decodedResponse;
            }
        }

        return null;
    }

    public function tenantRequest(Request $request) {
        $client = new Client();
        $response = $client->post(env('API_HOST') . '/api/tenants/request', [
            'form_params' => [
                'email' => Auth::user()->email,
                'requested' => 'true',
            ],
        ]);

        if ($response->getStatusCode() === 200) {
            $decodedResponse = json_decode($response->getBody()->getContents(), true);
            if ($decodedResponse['success'] && $decodedResponse['status'] = 200) {
                return $decodedResponse;
            }
        }

        return null;
    }
}
