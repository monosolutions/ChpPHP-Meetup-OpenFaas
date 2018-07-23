using Newtonsoft.Json;
using StackExchange.Redis;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Function
{
    public  class Todo
    {
        [JsonProperty("key")]
        public string Key { get; set; }

        [JsonProperty("task")]
        public string Task { get; set; }

        [JsonProperty("completed")]
        public bool Completed { get; set; }
    }

    public class FunctionHandler
    {
        private readonly IServer server;
        private readonly IDatabase redis;

        public FunctionHandler()
        {
            var redisConnection = ConnectionMultiplexer.Connect("todo-db-redis-slave.openfaas-fn.svc.cluster.local:6379,password=4hOFuAT5R7");
            var config = redisConnection.GetEndPoints()[0];
            server = redisConnection.GetServer(config);
            redis = redisConnection.GetDatabase();
        }
        public void Handle(string input) {
            List<RedisKey> keys = new List<RedisKey>();
            try{
                 keys = server.Keys(0, "*").ToList();           
            }catch(Exception ex){
                Console.WriteLine(ex.Message);
                return;
            }

            var todos = keys.Select(key=> {
                var todo = redis.HashGetAll(key);
                var task = todo.SingleOrDefault(t => t.Name.Equals("task"));
                var completed =  todo.SingleOrDefault(t => t.Name.Equals("completed"));               
                
                return new Todo
                {
                    Key = key,
                    Task = task != null ? task.Value.ToString() : "Empty",
                    Completed = completed != null ? completed.Value == "1" : false
                };
            });

            Console.WriteLine(JsonConvert.SerializeObject(todos));

        }
    }
}
