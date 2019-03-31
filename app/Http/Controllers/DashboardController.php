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
        $weeklyBreakDown = $this->getDaily();
        $data = ['latest' => $latest, 'weeklyBreakdown'=>$weeklyBreakDown];
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
    public function getDaily()
    {
        $client = new Client();
        $mon =0;
        $tue =0;
        $wed =0;
        $thu =0;
        $fri =0;
        $sat =0;
        $sun =0;
        $total = 0;
        $response = $client->get(env('API_HOST') . '/api/liveimage?order=desc&aggregate=week');
        if ($response->getStatusCode() === 200) {
            $decodedResponse = json_decode($response->getBody()->getContents(), true);
            if ($decodedResponse['success'] && $decodedResponse['status'] = 200) {
                $decodedArray =  $decodedResponse['images'];
                foreach ($decodedArray as $key => $value) {
                   $array = $decodedArray [$key];
                   foreach ($array as $key1 => $value1) {
                    $date =  $value1["created_at"];
                    //$numPpl = 0 ; 
                    $numPpl =$value1["numPeopleDetected"];
                    $timestamp = strtotime($date);
                    $day = date('D', $timestamp);
                    if($numPpl>0 ){
                        if($day=="Fri"){
                            $fri = $fri+$numPpl;
                            //$fricount++;
                        }
                        else if($day=="Thu"){
                            $thu = $thu+$numPpl;
                            //$thucount++;
                        }
                        else if($day=="Wed"){
                            $wed = $wed+$numPpl; 
                            //$wedcount++;
                        }
                        else if($day=="Tue"){
                            $tue = $tue+$numPpl; 
                            //$tuecount++;
                        }
                        else if($day=="Mon"){
                            $mon = $mon+$numPpl;
                            //$moncount++;
                        }
                        else if($day=="Sun"){
                            $sun = $sun+$numPpl;
                            //$suncount++;
                        }
                        else{
                            $sat = $sat+$numPpl;
                            //$satcount++;
                        }
                        $total += $numPpl;
                    }
                  }
                  break;

                }
               $array =["mon"=> $mon/$total*100,"tue"=>$tue/$total*100, "wed"=> $wed/$total*100, "thu"=>$thu/$total*100,"fri"=>$fri/$total*100,"sat"=>$sat/$total*100,"sun"=>$sun/$total*100];
               return $array;
            }
        }

        return null;
    }
   
}
