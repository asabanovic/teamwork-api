<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use Response;
use App\Repositories\Transformers\TaskTransformer;
use Validator;

class TaskController extends ApiController
{
    protected $task_transformer;
    protected $limit = 200;

    public function __construct(TaskTransformer $task_transformer)
    {
        $this->task_transformer = $task_transformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit') ?: $this->limit;

        $tasks = Task::paginate($limit);
         
        return $this->respond([
            'data' => $this->task_transformer->transformCollection($tasks->all()),
            'paginator' => [
                'total_count' => $tasks->total(),
                'current_page' => $tasks->currentPage(),
                'total_pages' => $tasks->lastPage()
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array('name' => 'required');
        $inputs = array(
            'name' => $request->input('name')
        );

        $validator = Validator::make($inputs, $rules);

        if($validator->fails()) {
            return $this->setStatusCode(422)->respondWithError('Unprocessable Entity');
        }

        try {

            Task::insert([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'completed' => $request->input('completed') ? (boolean) $request->input('completed') : false,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);

            return $this->setStatusCode(201)->respond([
                'message' => 'New task created!'
            ]);

        } catch (\Exception $e) {
            return $this->setStatusCode(422)->respondWithError('Unprocessable Entity');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::find($id);

        if ($task) {
            return $this->respond([
                'data' => $this->task_transformer->transform($task->toArray())    
            ]);
        }

        return $this->setStatusCode(404)->respondNotFound('Task does not exist!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   
        $task = Task::find($id);
        
        if (!$task) {
            return $this->setStatusCode(404)->respondNotFound('Task does not exist!');
        }

        $rules = array('name' => 'required');
        $inputs = array(
            'name' => $request->input('name')
        );

        $validator = Validator::make($inputs, $rules);

        if($validator->fails()) {
            return $this->setStatusCode(422)->respondWithError('Unprocessable Entity');
        }
        //dd($request->input('completed')? true : false);
        $task->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'completed' => (boolean)$request->input('completed'),
            ]);
        try {

            

            return $this->setStatusCode(201)->respond([
                'message' => 'Task updated!'
            ]);

        } catch (\Exception $e) {
            return $this->setStatusCode(422)->respondWithError('Unprocessable Entity');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::find($id);

        if ($task) {
            $task->users()->detach();
            $task->delete();
            return $this->respond([
                'message' => 'Task deleted!'    
            ]);
        }

        return $this->setStatusCode(404)->respondNotFound('Task does not exist!');
    }
}
