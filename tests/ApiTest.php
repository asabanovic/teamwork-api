<?php

namespace Tests;

use Faker\Factory;

class ApiTest extends TestCase
{
	/**
	 * @param Faker/Factory $fake
	 */
    protected $fake;

    /**
	 * @param int $times
	 */
	protected $times = 1;

	public function __construct()
	{
		$this->fake = Factory::create();
	}

	/**
	 * Make HTTP calls
	 *
	 * @param string $uri
	 * @param string $method
	 * @param array $parameters
	 * @return string Json
	 */
	public function ping($uri, $method = 'GET', array $parameters = [])
    {
    	return $this->json($method, $uri, $parameters);
    }

    /**
     * Define how many times to create a model
     *
     * @param int $count
     * @return int
     */
	public function times($count)
    {
    	$this->times = $count;

    	return $this;
    }

    /**
     * Instantiate a model for a number of times
     *
     * @param strign $model
     * @param array $fields
     * @return mixed
     */
    public function make($model, $fields = array())
	{	
		return factory($model, $this->times)->create($fields);
	}
	
}