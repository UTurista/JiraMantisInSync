<?php

class Request
{
	/**
	 *	An HTTP request.
	 */
	public function populate($url, $method, $username, $password, $body=NULL)
	{
		$this->url = $url;
		$this->method = $method;
		$this->username = $username;
		$this->password = $password;
		$this->body = $body;

	}

	public function populate_from_server()
	{
		/**
		 * 	Sets the Request's variables based on the incoming HTTP request.
		 */
    $this->populate($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], file_get_contents('php://input'));
	}
}
