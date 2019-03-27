<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
        return redirect()->route('overview');
    }

    public function overview()
    {
        $liveImages = $this->getLiveImages();
        $latest = end($liveImages);
        $data = ['latest' => $latest];
        return view('overview')->with($data);
    }

    public function records(Request $request)
    {
        $liveImages = $this->getLiveImages();

        $data = ['liveImages' => $liveImages];
        return view('records')->with($data);
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
