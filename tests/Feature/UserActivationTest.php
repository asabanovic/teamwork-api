<?php

namespace Tests\Feature;

use Tests\ApiTest;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Repositories\ActivationRepository;
use Illuminate\Database\Connection;

class UserActivationTest extends ApiTest
{	
	protected $activation_repo;

	public function __construct()
	{	
		$this->activation_repo = new ActivationRepository();
	}

    /**  @test */
    public function it_activates_a_new_user_by_via_token_url()
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

       	$link = $this->activation_repo->getActivationLinkByUserId(1);
       	
       	$response = $this->get($link);
       	 
       	$response->assertStatus(200)
       			->assertJsonFragment([
	                'message' => 'User has been activated!'
	            ]);

	    $this->assertDatabaseMissing('user_activations', [
	        'user_id' => 1
	    ]);
    }

    /**  @test */
    public function it_creates_a_new_token_in_database_when_creating_a_new_user()
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

	    $this->assertDatabaseHas('user_activations', [
	        'user_id' => 1
	    ]);
    }
}
