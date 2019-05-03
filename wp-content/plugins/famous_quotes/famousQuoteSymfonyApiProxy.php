<?php

class famousQuoteSymfonyApiProxy
{
	const API_URL = 'http://pavel.bootcamp.architechlabs.com:8000';

	private $api_key;

	public $is_error;
	public $error_message;
	public $json;

	function __construct($api_key = null)
	{
		$this->api_key = $api_key;
	}

	private function send_request($verb, $uri, $data = array())
	{

		if (count($data) > 0)
		{
			$post_data = '';
			foreach ($data as $key => $val)
			{
				$post_data .= ($post_data != '' ? '&' : '');
				$post_data .= ($key . '=' . urlencode($val));
			}
		}

		$c = curl_init(self::API_URL . $uri);
		curl_setopt($c, CURLOPT_VERBOSE, 0);

		switch ($verb)
		{
			case 'GET':
				break;
			case 'POST':
				curl_setopt($c, CURLOPT_POST, 1);
				if ($post_data)
				{
					curl_setopt($c, CURLOPT_POSTFIELDS, $post_data);
				}
				break;
			case 'DELETE':
				curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
			default:
				throw new Exception('curl method not implemented');
		}
		
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);

		@set_time_limit(1000);

		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 1);

		if ($this->api_key)
		{
			curl_setopt($c, CURLOPT_HTTPHEADER, array('X-APIKEY: ' . $this->api_key));
		}

		$buffer = curl_exec($c);

		if (curl_errno($c) > 0)
		{
			$this->is_error = true;
			$this->error_message = curl_error($c);
			return false;
		}

		if (!$buffer)
		{
			$this->is_error = true;
			$this->error_message = 'Received empty response';
			return false;
		}

		try {
			$result = json_decode($buffer, true);
		}
		catch (Exception $e) 
		{
			$this->is_error = true;
			$this->error_message = $e->getMessage();
			return false;
		}

		if (isset($result['status']) && $result['status'] == 'OK')
		{
			$this->is_error = false;
			$this->json = $result;
			return true;
		}
		else
		{
			$this->is_error = true;
			$this->error_message = isset($result['message']) ? $result['message'] : $buffer;
			return false;
		}
		
	}

	public function newApiKey()
	{
		$response = $this->send_request('GET', '/key');
		return $response->is_error ? false : $this->json;
	}

	public function getQuotes()
	{
		$response = $this->send_request('GET', '/quote');
		return $response->is_error ? false : $this->json;
	}

	public function addQuote($author, $text)
	{
		$response = $this->send_request('POST', '/quote', array('author' => $author, 'text' => $text));
		return $response->is_error ? false : $this->json;
	}	
}