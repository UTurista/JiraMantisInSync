<?php 
require_once( 'http/request.class.php' );
require_once( 'http/response.class.php' );

$response = new Response();

if ( plugin_config_get('installed') != 0){
  //Already Installed, ignoring
  $response->status = 204;

}else{
  
  $request = new Request();
  $request->populate_from_server();

  $payload = json_decode ( $request->body );

  if( $payload->clientKey && $payload->sharedSecret){
    //Installing
    plugin_config_set( 'token', $payload );
    plugin_config_set( 'installed', 1 );
    $response->status = 200;
  }else{
    //Bad content -  missing information (Client and/or sharedSecret)
    $response->status = 204;
  }
}

$response->send();