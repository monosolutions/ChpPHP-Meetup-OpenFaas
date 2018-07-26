/*jshint unused:false */

(function (exports) {

	'use strict';

	var STORAGE_KEY = 'todos-vuejs';

	exports.todoStorage = {
		getAll: async function () {
			let url = 'http://localhost:31112/function/readtodos';
				
			let response = await fetch(url, {
				mode: "no-cors", // no-cors, cors, *same-origin
				cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
				credentials: "include", // include, same-origin, *omit
				headers: {
					"Access-Control-Allow-Origin":"*",
					"Access-Control-Allow-Credentials":"true",
				},			
				method: 'POST'
			  })
			
			console.log(response);
			let todos = await response.data;		
			
			let mapped = todos.map(todo => {
				return {
					id: todo.key,
					title: todo.task,
					completed: todo.completed
				}
			});
			console.log(mapped);
			return mapped || [];
		},
		save: function (todos) {
			localStorage.setItem(STORAGE_KEY, JSON.stringify(todos));
		}
	};

})(window);