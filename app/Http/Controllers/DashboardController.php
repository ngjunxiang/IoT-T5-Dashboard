<?php

namespace App\Http\Controllers;

use Auth;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use DateTime;

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
        $percentage = $this->predictHourly($liveImages);
        $data = ['latest' => $latest,'percentage'=>$percentage];
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

    public function predictHourly($data)
    {
        $today = date('D');
        $result = array();
        $counter = array();
        foreach ($data as $item) {
            $Rawdate = $item["created_at"];
            $date = strtotime($Rawdate);
            $day = date('D', $date);
            if($day == $today)
            {
                $hour = date('H', $date);
                if(!array_key_exists($hour, $result)){
                    $hourly= array($hour=>$item['numPeopleDetected']);
                    $newCounter = array($hour=>1);
                    $result+=$hourly;
                    $counter+=$newCounter;
                }
                else {
                    $numPpl = $result[$hour];
                    if($item['numPeopleDetected'] >0)
                        $counter[$hour] +=1; 
                    $result[$hour]= $item['numPeopleDetected']+$numPpl;
                }
            }
            
        }
        foreach($counter as $key=>$value){
            $result[$key]= round(($result[$key]/$counter[$key])/85*100);
        }
        return $result;
        
    }
   
}
