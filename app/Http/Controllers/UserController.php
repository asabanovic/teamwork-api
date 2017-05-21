<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Response;
use App\Repositories\Transformers\UserTransformer;
use Validator;

class UserController extends ApiController
{   
    protected $user_transformer;
    protected $limit = 200;

    public function __construct(UserTransformer $user_transformer)
    {
        $this->user_transformer = $user_transformer;
        // \DB::table('users')->insert([
        //     'first_name' => 'Adnan',
        //     'last_name' => 'Sabanovic',
        //     'email' => 'adnanxteam@gmail.com',
        //     'password' => \Hash::make('admin')
        //     ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $limit = $request->input('limit') ?: $this->limit;

        $users = User::paginate($limit);
         
        return $this->respond([
            'data' => $this->user_transformer->transformCollection($users->all()),
            'paginator' => [
                'total_count' => $users->total(),
                'current_page' => $users->currentPage(),
                'total_pages' => $users->lastPage()
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
        $rules = array('email' => 'required|email', 'password' => 'required');
        $inputs = array(
            'email' => $request->input('email'),
            'password' => $request->input('password')
        );

        $validator = Validator::make($inputs, $rules);

        if($validator->fails()) {
            return $this->setStatusCode(422)->respondWithError('Unprocessable Entity');
        }

        try {

            User::insert([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'password' => \Hash::make($request->input('password')),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);

            return $this->setStatusCode(201)->respond([
                'message' => 'New user created!'
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
        $user = User::find($id);

        if ($user) {
            return $this->respond([
                'data' => $this->user_transformer->transform($user->toArray())    
            ]);
        }

        return $this->setStatusCode(404)->respondNotFound('User does not exist!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->tasks()->detach();
            $user->delete();
            return $this->respond([
                'message' => 'User deleted!'    
            ]);
        }

        return $this->setStatusCode(404)->respondNotFound('User does not exist!');
    }

 }
