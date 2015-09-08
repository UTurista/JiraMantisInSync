<?php 
require_once( 'http/request.class.php' );
require_once( 'http/response.class.php' );
require_once( 'JiraJWT.class.php' );



$response = new Response();


if ( JiraJWT::isValid( JiraJWT::getJWT() , plugin_config_get('token')) ){
  
  plugin_config_set( 'installed', 0 );
  plugin_config_set( 'token', '' );
  $response->status = 200;

}else{
  $response->status = 403;
}
 
  $response->send();
  return;
