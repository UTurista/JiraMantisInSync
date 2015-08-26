<?php
require_once( 'http/httpexception.class.php' );
require_once( 'http/request.class.php' );
require_once( 'http/response.class.php' );
require_once( 'http/restservice.class.php' );

try {
  $request = new Request();
  $request->populate_from_server();

  $service = new RestService();

  $resp = $service->handle($request);
  $resp->send();
} catch (HttpException $e) {
  $e->resp->send();
}
