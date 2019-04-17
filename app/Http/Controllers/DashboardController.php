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
        $percentage = $this->predictHourly($liveImages);
        $data = ['latest' => $latest, 'percentage' => $percentage];
        if (Auth::user()->hasRole('manager')) {
            return view('manager_overview')->with($data);
        } else {
            return view('tenant_overview')->with($data);
        }

    }

    public function getMembers(Request $request)
    {

        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');

        $search = (isset($filter['value'])) ? $filter['value'] : false;

        $total_members = $this->getLiveImagesCount(); // get your total no of data;
        $members = $this->getLiveImagesPaginated($start, $length); //supply start and length of the table data
        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_members,
            'recordsFiltered' => $total_members,
            'data' => $members,
        );


        echo json_encode($data);
    }

    public function records(Request $request)
    {
        return view('records');
    }

    public function getLiveImagesCount()
    {
        $client = new Client();
        $response = $client->get(env('API_HOST') . '/api/liveimage/count');
        if ($response->getStatusCode() === 200) {
            $decodedResponse = json_decode($response->getBody()->getContents(), true);
            if ($decodedResponse['success'] && $decodedResponse['status'] = 200) {
                return $decodedResponse['count'];
            }
        }

        return null;
    }

    public function getLiveImagesPaginated($index, $limit)
    {
        $client = new Client();
        $response = $client->get(env('API_HOST') . '/api/liveimage/paginate?order=desc&index=' . $index . '&limit=' . $limit);
        if ($response->getStatusCode() === 200) {
            $decodedResponse = json_decode($response->getBody()->getContents(), true);
            if ($decodedResponse['success'] && $decodedResponse['status'] = 200) {
                return $decodedResponse['images'];
            }
        }

        return null;
    }

    public function getLiveImages()
    {
        $client = new Client();
        $response = $client->get(env('API_HOST') . '/api/liveimage?order=desc&limit=5000');
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
        $hourlyResult = array("09" => [], "10" => [], "09" => [], "11" => [], "12" => [], "13" => [], "14" => [], "15" => [], "16" => [], "17" => [], "18" => [], "19" => [], "20" => [], "21" => [], "22" => [], "23" => [], "00" => []);
        $result = $hourlyResult;
        $counter = array();
        foreach ($data as $item) {
            $Rawdate = $item["created_at"];
            $date = strtotime($Rawdate);
            $day = date('D', $date);
            if ($day == $today) {
                $hour = date('H', $date);
                if (array_key_exists($hour, $hourlyResult)) {
                    $hourly = $hourlyResult[$hour];
                    array_push($hourly, $item['numPeopleDetected']);
                    $hourlyResult[$hour] = $hourly;
                }
            }

        }
        foreach ($hourlyResult as $key => $value) {
            //echo json_encode($hourlyResult[$key]);
            $result[$key] = round($this->predict($hourlyResult[$key]) / 85 * 100);
        }
        return $result;

    }
    public function predict($result)
    {
        $client = new Client([
            'headers' => ['Content-Type' => 'application/json'],
        ]);
        $response = $client->post("http://159.89.204.164:8081/predict", ['body' => json_encode(
            [
                "data" => $result,
            ]
        )]);
        $data = $response->getBody();
        $json = json_decode($data, true);
        return $json['data'];
    }

}
