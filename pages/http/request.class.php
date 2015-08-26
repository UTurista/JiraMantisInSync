<?php

class Request
{
	/**
	 *	An HTTP request.
	 */
	public function populate($url, $method, $username, $password, $body=NULL, $type_expected='text/x-json')
	{
		$this->url = $url;
		$this->method = $method;
		$this->username = $username;
		$this->password = $password;
		$this->type_expected = $type_expected;
		$this->body = $body;

		//$parsed_url = parse_url($this->url);
		//$this->host = $parsed_url['host'];
		//$this->path = $parsed_url['path'];
		//$this->query = $parsed_url['query'];
	}

	public function populate_from_server()
	{
		/**
		 * 	Sets the Request's variables based on the incoming HTTP request.
		 */
		$this->url = $_SERVER['REQUEST_URI'];
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->username = $_SERVER['PHP_AUTH_USER'];
		$this->password = $_SERVER['PHP_AUTH_PW'];
		$this->body = file_get_contents('php://input');

		//$this->_extrapolate_from_url();

		$headers = getallheaders();
		$type = array_key_exists('Accept', $headers)
						? $headers['Accept']
						: 'text/x-json';
		if ($type == 'text/x-json' || $type == 'application/json') {
			$this->type_expected = $type;
		} else {
			throw new HTTPException(406, "Unacceptable content type: $type.  This resource is available in the following content types:

text/x-json
application/json");
		}
	}
}
