<?php 

require_once( 'http/response.class.php' );

 file_put_contents("mLog.txt", "\n\JWT...\n", FILE_APPEND );

 //http://localhost/mantisbt-1.2.19/plugin.php?page=JiraMantisInSync/jwt
//http://localhost/mantisbt-1.2.19/plugins/JiraMantisInSync/pages
$response = new Response();
$response->entity_content_type = "application/json";
$response->body = '{
     "name": "JiraMantisInSync",
     "description": "JiraMantisInSync",
     "key": "pt.uturista.jira",
     "baseUrl": "http://localhost/mantisbt-1.2.19/",
     "vendor": {
         "name": "Vasco Loureiro",
         "url": "http://uturista.pt"
     },
     "authentication": {
        "type": "jwt"
      },
      "lifecycle": {
        "installed": "plugin.php?page=JiraMantisInSync/install",
        "uninstalled": "plugin.php?page=JiraMantisInSync/uninstall",
        "enabled": "plugin.php?page=JiraMantisInSync/enable.php"
      },
      "apiVersion": 1
 }';
 
 $response->send();