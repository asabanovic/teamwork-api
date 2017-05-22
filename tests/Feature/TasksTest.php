<?php

namespace Tests\Feature;

use Tests\ApiTest;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Task;
use Faker\Factory;

class TasksTest extends ApiTest
{
	/**  @test */
	public function it_creates_one_task()
	{
		$response = $this->ping('api/v1/tasks', 'POST', [
			'name' => 'My task',
			'description'  => 'My task description',
			'completed'      => false,
			]);

		$response->assertStatus(201)
		->assertJsonFragment([
			'message' => 'New task created!'
			]);

		$this->assertDatabaseHas('tasks', [
			'name' => 'My task'
			]);
	}

	/**  @test */
	public function it_deletes_one_task()
	{
		$this->make('App\Task', ['name' => 'My new task']);

		$this->assertDatabaseHas('tasks', [
			'name' => 'My new task'
			]);

		$response = $this->ping('api/v1/tasks/1', 'DELETE');
		$response->assertStatus(200)
		->assertJsonFragment([
			'message' => 'Task deleted!'
			]);


		$this->assertDatabaseMissing('tasks', [
			'name' => 'My new task'
			]);

		$response = $this->ping('api/v1/tasks/1', 'DELETE');
		$response->assertStatus(404)
		->assertJsonFragment([
			'message' => 'Task does not exist!'
			]);
	}

	/**  @test */
	public function it_fetches_tasks()
	{
		$this->times(3)->make('App\Task', ['name' => 'First task']);
		$response = $this->ping('api/v1/tasks');
		$response->assertStatus(200)
		->assertJsonFragment([
			'name' => 'First task'
			]);

		$this->assertDatabaseHas('tasks', [
			'name' => 'First task'
			]);
	}


	/**  @test */
	public function it_fetches_one_task()
	{
		$this->times(1)->make('App\Task', ['name' => 'Second task']);
		$response = $this->ping('api/v1/tasks/1');
		$response->assertStatus(200)
		->assertJsonFragment([
			'name' => 'Second task'
			]);	            
	}

	/**  @test */
	public function it_updates_one_task()
	{
		$this->times(1)->make('App\Task', ['name' => 'Second task']);
		$response = $this->ping('api/v1/tasks/1', 'PUT', ['name' => 'Second task', 'completed' => 'true']);
		$response->assertStatus(201)
		->assertJsonFragment([
			'message' => 'Task updated!'
			]);	  

		$this->assertDatabaseHas('tasks', [
			'name' => 'Second task', 
			'completed' => true
			]);
	}


}
