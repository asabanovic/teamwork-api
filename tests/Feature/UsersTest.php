<?php

namespace Tests\Feature;

use Tests\ApiTest;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use Faker\Factory;

class UsersTest extends ApiTest
{
  
    /**  @test */
    public function it_creates_one_user()
    {
       	$response = $this->ping('api/v1/users', 'POST', [
       		'first_name' => 'Adnan',
       		'last_name'  => 'Sabanovic',
       		'email'      => 'adnanxteam@gmail.com',
       		'password'	 => 'secret'
       	]);
       	$response->assertStatus(201)
       			->assertJsonFragment([
	                'message' => 'New user created!'
	            ]);

	    $this->assertDatabaseHas('users', [
	        'email' => 'adnanxteam@gmail.com'
	    ]);
    }

    /**  @test */
    public function it_deletes_one_user()
    {
    	$this->make('App\User', ['email' => 'adnanxteam@gmail.com']);

    	$this->assertDatabaseHas('users', [
	        'email' => 'adnanxteam@gmail.com'
	    ]);

       	$response = $this->ping('api/v1/users/1', 'DELETE');
       	$response->assertStatus(200)
       			->assertJsonFragment([
	                'message' => 'User deleted!'
	            ]);


	    $this->assertDatabaseMissing('users', [
	        'email' => 'adnanxteam@gmail.com'
	    ]);

	    $response = $this->ping('api/v1/users/1', 'DELETE');
       	$response->assertStatus(404)
       			->assertJsonFragment([
	                'message' => 'User does not exist!'
	            ]);
    }

    /**  @test */
    public function it_fetches_users()
    {
        $this->times(3)->make('App\User', ['first_name' => 'Adnan']);
       	$response = $this->ping('api/v1/users');
       	$response->assertStatus(200)
       			->assertJsonFragment([
	                'first_name' => 'Adnan'
	            ]);

	    $this->assertDatabaseHas('users', [
	        'first_name' => 'Adnan'
	    ]);
    }

    /**  @test */
    public function it_fetches_one_user()
    {
        $this->times(1)->make('App\User', ['first_name' => 'Adnan']);
       	$response = $this->ping('api/v1/users/1');
       	$response->assertStatus(200)
       			->assertJsonFragment([
	                'first_name' => 'Adnan'
	            ]);	            
    }


}
