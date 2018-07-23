<?php
namespace App;
use Predis;

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

    public function handle($key): void {
        
        if($this->isNullOrEmptyString($key)){            
            echo '{"statusCode": "400", "message": "You must supply a key"}';           
            return;
        }

        try{
            $this->redis->del($key);    
        }
        catch(Exception $e){
            echo '{"statusCode":200, "message":"', $e ,'"}';
            return;
        }
         
        echo '{"statusCode":200, "message":"Deleted - ',$key,'"}';
    }
}
