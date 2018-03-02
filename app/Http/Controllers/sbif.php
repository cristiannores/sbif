<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\ClientException;

class sbif extends Controller {

    public function index() {



        return view('welcome');
    }

    public function getUf(Request $request) {
        return response()->json($this->getDataUF($request));
    }

    public function export(Request $request) {
        
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );


        $columns = array('Valor', 'Fecha');

        $data = $this->getDataUF($request);
        if (isset($data->UFs)) {
            $data = $data->UFs;
        } else {
            $data = array()
                    
                    
        }

        $callback = function() use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $data) {
                fputcsv($file, array($data->Valor, $data->Fecha));
            }
            fclose($file);
        };
        return \Illuminate\Support\Facades\Response::stream($callback, 200, $headers);
    }

    public function getDataUF($request) {
        
        $apiKey = "d8093171162117c0c6e8da895b00978d4e2b6a0e";

        $urlUF = "http://api.sbif.cl/api-sbifv3/recursos_api/uf/";

        if ($request->selector == "no") {
            $params = $request->year . "/" . $request->month;
        } else {
            $params = $request->year . "/" . $request->month . "/dias/" . $request->day;
        }

        $header = "?apikey=$apiKey&formato=json";


        $client = new \GuzzleHttp\Client();

        try {
            $res = $client->request('GET', $urlUF . $params . $header);
        } catch (ClientException $e) {

            return array();
        }
        if ($res->getStatusCode() == 200) {

            return json_decode($res->getBody());
        } else {
            return array();
        }
    }

}
