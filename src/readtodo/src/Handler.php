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
            $this->redis = new Predis\Client('tcp://todo-db-redis-slave.openfaas-fn.svc.cluster.local:6379',  $options);
        }
        catch (Exception $e) {
            echo($e->getMessage());
        }
    }  

    public function handle($key): void {

        if($this->isNullOrEmptyString($key)){            
            echo '{"statusCode": "400", "message": "You must supply a key"}';           
            return;
        }

        $data=$this->redis->hGetAll($key);      
        if(empty($data)){
            echo '{"statusCode":404, "message":"Not found"}';
            return;
        }
        $todo = new Todo($key, $data);       
        echo '{"statusCode":200, "message":' , json_encode($todo) , '}';
    }
}
