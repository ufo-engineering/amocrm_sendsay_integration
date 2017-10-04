<?php

namespace App\Helpers;
use Dotzero\LaravelAmoCrm\Facades\AmoCrm;
use App\Models\Lead;

class AmoHelper
{
    private static $allowed_pipelines = [
        '999999' =>'Pipeline name'
      ];

    private static $fields = [
        'phone' =>'999999',
        'email' =>'9999991',
        'type' =>'9999992',
        'lead_id' =>'9999993',
        'lead_source' =>'9999994',
      ];

    private static $statuses = [
        'empty'    => '',
        '99999999' => 'Contact status name'
      ];

    public static function parseContacts($contacts_data)
    {

    }

    public static function parseLeads($leads_data)
    {
      $result = [];
      foreach ($leads_data as $lead) {
          $lead_full = AmoCrm::getClient()->lead->apiList(['id'=>$lead['id']])[0];
        if(isset(self::$allowed_pipelines[$lead_full['pipeline_id']]) && !empty($lead_full['main_contact_id'])){
            $contact = AmoCrm::getClient()->contact->apiList(['id'=>$lead_full['main_contact_id']])[0];
            $result[] = ['lead'=>$lead_full, 'contact'=>$contact];
            $lead = new Lead;
            $lead->addLead($lead_full);
        }
      }
      return $result;
    }

    public static function deleteLeads($leads_data){
      $result = [];
      foreach ($leads_data as $lead) {
          $lead_model = new Lead;
          $lead_data = $lead_model->findLead($lead['id']);
        if(!empty($lead_data)){
            $contact = AmoCrm::getClient()->contact->apiList(['id'=>$lead_data[2]])[0];
            $lead_full = ['id'=>$lead_data[0], 'pipeline_id'=>$lead_data[1], 'status_id'=>'empty','price'=>'0'];
            $result[] = ['lead'=>$lead_full, 'contact'=>$contact];
        }
      }
      return $result;
    }

    public static function getCustomField($amo_model, $field='email'){
      if(!isset($amo_model['custom_fields']))
          return '';
      foreach ($amo_model['custom_fields'] as $key => $value) {
          if($value['id']==self::$fields[$field]){
              return  empty($value['values'][0]['value']) ? '' : $value['values'][0]['value'];
          }
      }
      return '';
    }

    public static function getStatuses(){
      return self::$statuses;
    }

    public static function getPipelines(){
      return self::$allowed_pipelines;
    }



}
