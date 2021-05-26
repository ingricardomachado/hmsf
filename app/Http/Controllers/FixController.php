<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use File;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Models\CarList;
use App\Models\CarMake;
use App\Models\CarModel;
use App\Models\Hacienda;
use App\Models\Province;
use App\Models\Canton;
use App\Models\District;
use App\Models\City;
use App\Models\ProductCatalog;
use App\Models\ServiceCatalog;
use App\User;
use Session;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\HaciendaImport;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Input;
use XmlParser;
use SimpleXMLElement;

class FixController extends Controller
{    
    
    public function add_car_makes_models(){
        $this->add_car_makes();
        $this->add_car_models();
        return "Marcas y modelos registrados exitosamente";
    }
    
    public function add_car_makes()
    {        
        $car_lists=CarList::orderBy('make')
                            ->groupBy('make')
                            ->select('make')->get();
        
        foreach ($car_lists as $car_list) {
            $car_make=new CarMake();
            $car_make->name=strtoupper($car_list->make);
            $car_make->save();
        }
    }

    public function add_car_models()
    {        
        $car_lists=CarList::orderBy('make')->orderBy('model')
                            ->groupBy('make')->groupBy('model')
                            ->select(['make', 'model'])->get();
        
        foreach ($car_lists as $car_list) {
            $car_model=new CarModel();
            $car_model->car_make_id=$this->car_make_id(strtoupper($car_list->make));
            $car_model->name=strtoupper($car_list->model);
            $car_model->save();
        }
    }

    public function car_make_id($make){
        $car_make=CarMake::where('name',$make)->first();
        return $car_make->id;
    }

   public function upload_hacienda_xls(){
      
     //try {
        //2. Se limpia la tabla padron
        Hacienda::truncate();        
        //3. Se importa la data a la tabla customers      
        ini_set('max_execution_time', 1000);
        Excel::import(new HaciendaImport, storage_path('app').'/HACIENDA.xls');

        return "Direcciones cargadas exitosamente... revise";
        
      //} catch (\InvalidArgumentException $e) {
        
        Hacienda::truncate();
        return "Formato invalido en alguna columna";

      //} catch (\Exception $e) {
        
        Hacienda::truncate();
        return "Algo salio mal, chequea que tu archivo tenga la estructura correcta. ". $e->getMessage();    
      
      //}catch (\Error $e) {
        Hacienda::truncate();
        return $e->getMessage();    
      //}
   }

   public function upload_provinces(){
        $rows=Hacienda::whereRaw('LENGTH(codigo) = 3')->get();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Province::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        foreach ($rows as $row) {
            $province=new Province();
            $province->code=$row->codigo;
            $province->name=$row->descripcion;
            $province->save();
        }
        return "Provincias cargadas exitosamente....";
   }

   public function upload_cantones(){
        $rows=Hacienda::whereRaw('LENGTH(codigo) = 7')->get();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Canton::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        foreach ($rows as $row) {
            $codes=explode("-",$row->codigo);
            //echo $codes[0].' '.$row->descripcion.'<br>';
            $canton=new Canton();
            $canton->province_id=$this->province_id($codes[0]);
            $canton->code=$codes[1];
            $canton->name=$row->descripcion;
            $canton->save();
        }
        return "Cantones cargados exitosamente....";
   }

    public function province_id($province_code){
        $province=Province::where('code',$province_code)->first();
        return $province->id;
    }

    public function canton_id($province_id, $canton_code){
        $canton=Canton::where('province_id',$province_id)
                        ->where('code', $canton_code)->first();
        return $canton->id;
    }

    public function district_id($canton_id, $district_code){
        $district=District::where('canton_id',$canton_id)
                        ->where('code', $district_code)->first();
        return $district->id;
    }

   public function upload_districts(){
        $rows=Hacienda::whereRaw('LENGTH(codigo) = 11')->get();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        District::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        foreach ($rows as $row) {
            $codes=explode("-",$row->codigo);
            //echo $codes[2].' '.$row->descripcion.'<br>';
            $district=new District();
            $district->canton_id=$this->canton_id($this->province_id($codes[0]),$codes[1]);
            $district->code=$codes[2];
            $district->name=$row->descripcion;
            $district->save();
        }
        return "Distritos cargados exitosamente....";
   }

   public function upload_cities(){
        $rows=Hacienda::whereRaw('LENGTH(codigo) = 15')->get();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        City::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        foreach ($rows as $row) {
            $codes=explode("-",$row->codigo);
            //echo $codes[3].' '.$row->descripcion.'<br>';
            $city=new City();
            $city->district_id=$this->district_id($this->canton_id($this->province_id($codes[0]),$codes[1]),$codes[2]);
            $city->code=$codes[3];
            $city->name=$row->descripcion;
            $city->save();
        }
        return "Ciudades cargadas exitosamente....";
   }

   public function set_barcode(){
        $products=ProductCatalog::all();
        foreach ($products as $product) {
            $product->barcode=generateRandomString(13);
            $product->save();
        }
        
        $services=ServiceCatalog::all();
        foreach ($services as $service) {
            $service->barcode=generateRandomString(13);
            $service->save();
        }

   }

    function call_api_test_simpleget(){
        $client = new Client();
        $request = $client->get('http://tecyvid2.com/api/countries');
        $response = $request->getBody();
        return $response;
   }


    //Como llamar una API externa con Token que devuelve un Json, y leer el Json retornado
    function call_api_test(){
        $client = new Client();
        $token="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjNlYzczNjBlZGJlMGRiMTJiYWZjZjJlZDgyMTgxZGI4ODU2ZTQ5NTVjOWMyMzQxOTlmMzdiOWZiMGQ1MjExYjViNTFlYWFmYjA2OWFiNWQ0In0.eyJhdWQiOiIxIiwianRpIjoiM2VjNzM2MGVkYmUwZGIxMmJhZmNmMmVkODIxODFkYjg4NTZlNDk1NWM5YzIzNDE5OWYzN2I5ZmIwZDUyMTFiNWI1MWVhYWZiMDY5YWI1ZDQiLCJpYXQiOjE1ODc5NTI3ODEsIm5iZiI6MTU4Nzk1Mjc4MSwiZXhwIjoxNjE5NDg4NzgxLCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.b9GGjZK3ALFhV6HSNlKcD0pkvQmrrI0WHioaaxy_zx-9teonwL1bL5qHgZbZoNzzDhck5FZ6JF39gRrFjvQ9maH5ahyyLWnjPUekRDXsrNjG23zl9GUok0cLBZoJ0gpqgPIbI_IH-bh7RLn7ov0fBZf92WXOhA8UhrJdu1fjDCmIk0wan0Pt3IQFbmVGjlfDLrT1MO1gLIARjbBpE5PGNUGGPxgK2q_Ze8UIO0kApNPlHa9nZAyCuiF7IKRndYUIuFNc7Vxn5IbwwzVrDHt989WNy9ZUG6INlltDYBiZrZAUx9XnsjnMpnyrqOaeTchQ1-UoGtYrxng-rPx3_nCEpsWTjEzjfhwb7gRH_jSHhD4zi7E798o_6zzD5OJEw-eshmDtZhjh89j3Egq7rBB-yX6MDuvugVI2J50J1Qt5beHU5-mjaryaiqdDHbNELpc4ZFTGsNql-f6kyNjyO5_chD4Iyr7XrrQ0U5Ajm3exdllouVwCknmx3dqiHPomxjvf2Xc4vFfgAd-KYsYj0gqzSVgs7g8CTEdQrOfOSXajXSY1WSorovCtQzol8_ioLO5ClS7CJQA_5K319NC5qHSzglFJq-dAA7GJ5ije6vTKLoNGuLQGw3R6SYEJqfLwyFhlNqEohp2gt6XbNXJx0179s9gPFoQfk6qhXc53fCmDcsI";
        $response = $client->request(
            'GET',
            'http://tecyvid2.com/api/country/2',
            ['headers' => 
                [
                    'Authorization' => "Bearer {$token}"
                ]
            ]
        )->getBody();
        //Se decodifica para poder acceder a su informacion
        $jdecode = json_decode($response);
        
        return ''.$jdecode->data->shortname ;
   }


    //Como recibir y leer un XML
    function receive_xml(Request $request){
        //$file=storage_path('app/xmls/xml_sample.xml');
        $file=Input::file('xml');        
        //$xml = XMLParser::load($file);
        //$xml=simplexml_load_string($file);
        $xml = new SimpleXMLElement($file, null, true);
        //dd($xml);
        return $xml->Emisor->Nombre;
        
   }


}
