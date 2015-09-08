<?php
require_once( 'http/httpexception.class.php' );
require_once( 'http/request.class.php' );
require_once( 'http/response.class.php' );
require_once( 'http/restservice.class.php' );
require_once( 'JiraJWT.class.php' );

try {
 
  if ( JiraJWT::isValid( JiraJWT::getJWT() , plugin_config_get('token')) ){
    
    try{
      $request = new Request();
      $request->populate_from_server();

      $service = new RestService();

      $resp = $service->handle($request);
      $resp->send();
    } catch (HttpException $e) {
      $e->resp->send();
    }
  }else{
    //Invalid JWT - Prevent acess
    $response->status = 403;
    $response->send();
  }
  
  
  
 

