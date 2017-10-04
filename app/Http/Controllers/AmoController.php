<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\AmoHelper;
use App\Libs\Sendsay;
use Dotzero\LaravelAmoCrm\Facades\AmoCrm;
use Log;
use App\Models\Lead;

class AmoController extends Controller
{

    public function index(Request $request)
    {
        if(empty($request->all()))
             return 'empty request';
        $this->parse_request_array($request->all());
    }

    private function parse_request_array($request_data){
        foreach ($request_data as $key=>$row) {
          switch ($key) {
            case 'contacts':
            break;
            case 'leads':
              reset($row);
              $action = key($row);
              if(!isset($action))
                  Log::info('Undefined Action: '.print_r($row, true));
              if($action == 'delete'){
                  $result = AmoHelper::deleteLeads($row[$action]);
              }else{
                  $result = AmoHelper::parseLeads($row[$action]);
              }
              break;
            default:
              # code...
              break;
          }
          $this->sendToSendsay($result);
        }
    }

    private function sendToSendsay($data){
        $statuses = AmoHelper::getStatuses();
        $pipelines = AmoHelper::getPipelines();
        $ss = new Sendsay(env('SENDSAY_LOGIN', ''),env('SENDSAY_SUBLOGIN', ''),env('SENDSAY_PASSWORD', ''));
        foreach ($data as $row) {
          $data = [];
          if(empty($row['contact']) || empty($row['lead']))
              continue;
          $names = explode(' ', $row['contact']['name']);
          if(empty($names)){
            continue;
          }

          $email = AmoHelper::getCustomField($row['contact'],'email');
          $data['anketa_subscriber']['name'] = $names[0];
          if(isset($names[1]))
              $data['anketa_subscriber']['last_name'] = $names[1];
          $data['anketa_subscriber']['phone'] = AmoHelper::getCustomField($row['contact'],'phone');
          $data['anketa_subscriber']['type_of_contact'] = AmoHelper::getCustomField($row['contact'],'type');
          $data['anketa_subscriber']['company'] = $row['contact']['company_name'];

          if($pipelines[$row['lead']['pipeline_id']] == 'lead_magnet'){
            $data['anketa_subscriber']['lead_source'] = AmoHelper::getCustomField($row['lead'],'lead_source');
            $data['anketa_subscriber']['leads_id'] = AmoHelper::getCustomField($row['lead'],'lead_id');
            $data['-group']['p35'] = '1';
          }else{
            $data['events_2017']['status_'.$pipelines[$row['lead']['pipeline_id']]] = $statuses[$row['lead']['status_id']];
            $data['events_2017']['budget_'.$pipelines[$row['lead']['pipeline_id']]] = $row['lead']['price'];
          }

          $ss->member_set($email,$data);

        }
    }
}
