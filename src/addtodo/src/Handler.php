<?php
namespace App;
use Predis;

class Todo{
    public function __construct($key, $array){
        $this->Key=$key;
        $this->Task=$array[0];
        $this->completed=filter_var($array[1], FILTER_VALIDATE_BOOLEAN);
    }
}

class Handler
{    
    private function isNullOrEmptyString($str){
        return (!isset($str) || trim($str) === '');
    }

    public function __construct()
    {        
        $options = [
            'parameters' => [
                'password' => '4hOFuAT5R7'
            ],
        ];
        try {            
            $this->redis = new Predis\Client('tcp://todo-db-redis-master.openfaas-fn.svc.cluster.local:6379',  $options);
        }
        catch (Exception $e) {
            echo($e->getMessage());
        }
    }
   
    public function handle($json): void { 
        $key = $json->{'key'};
        $task = $json->{'task'};
        
        if($this->isNullOrEmptyString($key)){            
            echo '{"statusCode": "400", "message": "You must supply a key."}';           
            return;
        } 

        if($this->isNullOrEmptyString($task)){            
            echo '{"statusCode": "400", "message": "You must supply a task you want to do."}';           
            return;
        } 

        $this->redis->hmset($key, array('task' => $task, 'completed' => false));
        $todo = new Todo($key,  array($task, false));   

        echo '{"statusCode":200, "message":' , json_encode($todo) , '}';
    }
}
