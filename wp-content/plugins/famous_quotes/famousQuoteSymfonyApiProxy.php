<?php

class famousQuoteSymfonyApiProxy
{
	const API_URL = 'http://pavel.bootcamp.architechlabs.com:8000';

	public $is_error;
	public $error_message;
	public $json;

	private function send_request($verb, $uri, $headers = array(), $data = array())
	{
		/*
			$post_data = "".
				//form data
				"x_first_name=".urlencode($post_form["cc_first_name"]).
				"&x_last_name=".urlencode($post_form["cc_last_name"]).
				"&x_card_num=".urlencode($post_form["cc_number"]).
				"&x_card_code=".urlencode($post_form["cc_cvv2"]).
				"&x_exp_date=".urlencode($post_form["cc_expiration_month"]."/".$post_form["cc_expiration_year"]). //"MM/YYYY" format used
		*/
		//curl_setopt($c, CURLOPT_HEADER, 0);

		$c = curl_init(self::API_URL . $uri);
		curl_setopt($c, CURLOPT_VERBOSE, 0);

		switch ($verb)
		{
			case 'GET':
				break;
			case 'POST':
				curl_setopt($c, CURLOPT_POST, 1);
				curl_setopt($c, CURLOPT_POSTFIELDS, $post_data);
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

		if ($response->is_error)
		{
			return false;
		}
		else
		{
			return $this->json;
		}
	}
}