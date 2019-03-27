<?php

namespace App\Http\Controllers;

use Auth;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

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
        if (Auth::user()->hasRole('manager')) {
            return view('manager_overview')->with($data);
        } else {
            return view('tenant_overview')->with($data);
        }

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
