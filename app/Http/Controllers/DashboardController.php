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
        $hourlyResult = array("09"=>[],"10"=>[],"09"=>[],"11"=>[],"12"=>[],"13"=>[],"14"=>[],"15"=>[],"16"=>[],"17"=>[],"18"=>[],"19"=>[],"20"=>[],"21"=>[],"22"=>[],"23"=>[],"00"=>[]);
        $result = $hourlyResult;
        $counter = array();
        foreach ($data as $item) {
            $Rawdate = $item["created_at"];
            $date = strtotime($Rawdate);
            $day = date('D', $date);
            if($day == $today)
            {
                $hour = date('H', $date);
                if(array_key_exists($hour, $hourlyResult)){
                    $hourly = $hourlyResult[$hour];
                    array_push($hourly,$item['numPeopleDetected']);
                    $hourlyResult[$hour] = $hourly;
                }
            }
            
        }
        //echo json_encode($hourlyResult);
        foreach ($hourlyResult  as $key => $value)
        {
            $result[$key] = round($this->predict($hourlyResult[$key])/85*100);
        }
        //echo json_encode($result);
        return $result;
       
    }
    function predict($result) {

       
        $client = new Client([
            'headers' => [ 'Content-Type' => 'application/json' ]
        ]);
        $response = $client->post("http://159.89.204.164:8081/predict",['body' => json_encode(
            [
                "data"=>$result
            ]
        )]);
        $data =  $response->getBody();
        $json =json_decode($data, true);
        return $json['data'];
    }
   
}
