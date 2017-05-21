<?php

namespace App\Repositories\Transformers;

class TaskTransformer extends Transformer
{
    public function transform($task)
    {    
       return [
	       'name' 		  => $task['name'],
	       'description'  => $task['description'],
	       'completed'    => (boolean)$task['completed']
       ];
   }
}