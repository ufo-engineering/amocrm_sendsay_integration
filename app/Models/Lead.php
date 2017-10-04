<?php

namespace App\Models;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;

class Lead
{
    private $config;
    private $lexer;
    private $interpreter;
    private $csv_file; //csv file with lead_id, pipeline_id, main_contact_id

    public function __construct(){
      $this->config = new LexerConfig();
      $this->lexer = new Lexer($this->config);
      $this->interpreter = new Interpreter();
      $this->csv_file = storage_path('csv').'/leads.csv';
    }

    public function findLead($lead_data){
        $result = [];
        $this->interpreter->addObserver(function(array $row) use (&$lead_data,&$result) {
            if(is_array($lead_data)){
                if($row[0] == $lead_data['id'] && $row[1] == $lead_data['pipeline_id'] && $row[2] == $lead_data['main_contact_id']){
                  $result = $row;
                }
            }else{
                if($row[0] == $lead_data){
                  $result = $row;
                }
            }
        });
        $this->lexer->parse($this->csv_file, $this->interpreter);
        return $result;
    }


    public function addLead($lead_data, $force = false){
        if($force || empty($this->findLead($lead_data))){
            file_put_contents($this->csv_file,$lead_data['id'].",".$lead_data['pipeline_id'].",".$lead_data['main_contact_id']."\n", FILE_APPEND | LOCK_EX);
        }
    }

}
