<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;

class DashboardController extends Controller
{
    public function index()
    {
        $liveImages = $this->getLiveImages();
        return view('home');
    }

    public function getLiveImages()
    {
        $client = new Client();
        $response = $client->get(env('API_HOST') . '/api/liveimage/');
        if ($response->getStatusCode() === 200) {
            $decodedResponse = json_decode($response->getBody()->getContents(), true);
            if ($decodedResponse['success'] && $decodedResponse['status'] = 200) {
                return $decodedResponse['images'];
            }
        }

        return null;
    }
}
