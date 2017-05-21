<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Response;
use App\Repositories\Transformers\UserTransformer;
use App\Repositories\ActivationRepository;
use Validator;

class UserController extends ApiController
{   
    protected $user_transformer;
    protected $activation_repo;
    protected $limit = 200;

    public function __construct(UserTransformer $user_transformer, ActivationRepository $activation_repo)
    {
        $this->user_transformer = $user_transformer;
        $this->activation_repo = $activation_repo;
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
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
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
            $user->first_name   = $request->input('first_name');
            $user->last_name    = $request->input('last_name');
            $user->email        = $request->input('email');
            $user->password   = \Hash::make($request->input('password'));
            $user->save();

            $this->activation_repo->createActivation($user);

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

    public function activateUser($token)
    {
        if ($user = $this->activation_repo->activateUser($token)) {
            return $this->respond([
                'message' => 'User has been activated!'
            ]);
        }
        
        return $this->setStatusCode(404)->respondWithError('Token does not exist');
     }

 }
