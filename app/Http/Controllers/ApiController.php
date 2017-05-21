<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Repositories\Transformers\UserTransformer;

class ApiController extends Controller
{
	protected $status_code = 200;

	public function getStatusCode()
	{
		return $this->status_code;
	}

	public function setStatusCode($code)
	{
		$this->status_code = $code;

		return $this;
	}

	public function respondNotFound($message = 'Not Found!')
	{
		return $this->setStatusCode(404)->respondWithError($message);
	}

	public function respondInternalError($message = 'Internal Error!')
	{
		return $this->setStatusCode(500)->respondWithError($message);
	}

	public function respondWithError($message = 'Something went wrong')
	{
		return $this->respond([
			'errors' => [
				'message' => $message,
				'status_code' => $this->getStatusCode()
			]
		]);
	}

	public function respond($data, $headers = [])
	{
		return Response::json($data, $this->getStatusCode(), $headers);
	}
}