<?php

class RestService
{

  /*
   * This will be or service, we'll handle everything here
   */
	public function handle($request)
	{
    //First we authenticate
		//if (!auth_attempt_script_login("administrator", "root")) {
		//	throw new HTTPException(401, "Invalid credentials", array(
		//		'WWW-Authenticate: Basic realm="Mantis REST API"'));
		//}
    
    //FIXME:
    //We should check for permissions... GOD-POWERS

    //Now we get the type of WebHook
    $JSON = json_decode($request->body);
    
    if( $JSON->webhookEvent == 'jira:issue_created' ){
      return $this->createIssue($JSON);
    }else if( $JSON->webhookEvent == 'jira:issue_deleted' ){
      return $this->deleteIssue($JSON);
    }else if( $JSON->webhookEvent == 'jira:issue_updated' ){
      return $this->updateIssue($JSON);
    }else if( $JSON->webhookEvent == 'jira:worklog_updated' ){
      return $this->updateWorklog($JSON);
    }
    
    //Shouldn't reach here
    $resp = new Response();
    $resp->status = 500;
		$resp->body = "webhookEvent not recognized";
    return $resp;
	}
  
    
  
  function createIssue($JSON){
    $resp = new Response();

    $issue = $JSON->issue;
    $bugID = $issue->key;
    
    $t_bug_data = new BugData;
   
    $t_bug_data->project_id = plugin_config_get('project_id');
    
        
    $t_bug_data->summary = $this->getValue($issue->fields, 'summary', '');
    $t_bug_data->description = $this->getValue($issue->fields, 'description', '');


    $priority = $this->getValue($issue->fields, 'priority', 'Minor');
    switch($priority){
      case 'Minor':
        $t_bug_data->priority = 1;
        break;
      case 'Major':
        $t_bug_data->priority = 2;
        break;
    }
          


    //Other Values
    //$t_bug_data->reporter_id            = auth_get_current_user_id();
    //$t_bug_data->build                  = gpc_get_string( 'build', '' );
    //$t_bug_data->platform               = gpc_get_string( 'platform', '' );
    //$t_bug_data->os                     = gpc_get_string( 'os', '' );
    //$t_bug_data->os_build               = gpc_get_string( 'os_build', '' );
    //$t_bug_data->version                = gpc_get_string( 'product_version', '' );
    //$t_bug_data->profile_id             = gpc_get_int( 'profile_id', 0 );
    //$t_bug_data->handler_id             = gpc_get_int( 'handler_id', 0 );
    //$t_bug_data->view_state             = gpc_get_int( 'view_state', config_get( 'default_bug_view_status' ) );
    //$t_bug_data->category_id            = gpc_get_int( 'category_id', 0 );
    //$t_bug_data->reproducibility        = gpc_get_int( 'reproducibility', config_get( 'default_bug_reproducibility' ) );
    //$t_bug_data->severity               = gpc_get_int( 'severity', config_get( 'default_bug_severity' ) );
    //$t_bug_data->projection             = gpc_get_int( 'projection', config_get( 'default_bug_projection' ) );
    //$t_bug_data->eta                    = gpc_get_int( 'eta', config_get( 'default_bug_eta' ) );
    //$t_bug_data->resolution             = gpc_get_string('resolution', config_get( 'default_bug_resolution' ) );
    //$t_bug_data->status                 = gpc_get_string( 'status', config_get( 'bug_submit_status' ) );
              
   
    //$t_bug_data->steps_to_reproduce     = gpc_get_string( 'steps_to_reproduce', config_get( 'default_bug_steps_to_reproduce' ) );
    //$t_bug_data->additional_information = gpc_get_string( 'additional_info', config_get ( 'default_bug_additional_info' ) );
    //$t_bug_data->due_date               = gpc_get_string( 'due_date', '');
    
    
    $t_bug_data->create();
    
    return $resp;
  }
 
  function deleteIssue($JSON){
    $resp = new Response();
    $resp->status = 200;
		$resp->body = "deleteIssue";
    
    
     return $resp;
  }
  
  function updateIssue($JSON){
    $resp = new Response();
   
		
    $issue = $JSON->issue;
    $bugID = $issue->key;
    
    if( !bug_exists( $bugID )){
      $resp->status = 404;
      $resp->body = "Bug does not exist: ".$bugID;
      return $resp;
    }
    
    $bugRef = bug_get( $bugID );
    //Maintaining a ref to the old status 
    $t_old_bug_status = $t_bug_data->status;   

    
    foreach($JSON->changelog->items as $item){
      
      switch ($item->field) {
        case 'summary':
          $bugRef->summary = $item->toString;
          break;
        case 'description':
          $bugRef->description = $item->toString;
          break;
        case 'issuetype':
          break;
        default:
          break;
      }
    }

    
    if( $t_old_bug_status != $t_bug_data->status ){
      $bugRef->update( true, false );
    }

    $resp->status = 200;
    return $resp;
  }
  
  function updateWorklog($request){
    $resp = new Response();
    $resp->status = 200;
		$resp->body = "updateWorklog";
     return $resp;
  }
  
  
  
  
  
  function getValue($Object, $Propertie, $Default){
    if (property_exists($Object, $Propertie)){
      return $Object->$Propertie;
    }else{
      return $Default;
    } 
  }
  
  /*
  function checkPermissions(){
    	if ( !(
			   access_has_bug_level( access_get_status_threshold( $f_new_status, bug_get_field( $f_bug_id, 'project_id' ) ), $f_bug_id )
			|| access_has_bug_level( config_get( 'update_bug_threshold' ) , $f_bug_id )
			|| (   bug_is_user_reporter( $f_bug_id, $t_user )
				&& access_has_bug_level( config_get( 'report_bug_threshold' ), $f_bug_id, $t_user )
				&& (   ON == config_get( 'allow_reporter_reopen' )
					|| ON == config_get( 'allow_reporter_close' )
				   )
			   )
		  )
	) {
		access_denied();
	}
  }
  */
}
