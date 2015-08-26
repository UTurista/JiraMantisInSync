<?php 

require_once( config_get( 'class_path' ) . 'MantisPlugin.class.php' );

class JiraMantisInSyncPlugin extends MantisPlugin{
  
  /**
	 *  A method that populates the plugin information and minimum requirements.
	 */
	function register() {
		$this->name = 'JiraMantisInSync';
		$this->description = 'Maintains the bugTrackers Jira and Mantis synchronized';
		//$this->page = 'config';

		$this->version = '0.0.0.1';
		$this->requires = array(
			'MantisCore' => '1.2.0',
		);

		$this->author = 'uturista.pt';
		$this->contact = 'vascko@sapo.pt';
		$this->url = '';
	}

  function hooks() {
    return array(
      'EVENT_REPORT_BUG_DATA' => 'onReportBug',
      'EVENT_UPDATE_BUG' => 'onUpdateBug',
      'EVENT_BUGNOTE_DATA' => 'onNoteAdded'
    );
  }


  
  function onReportBug($p_event, $bugData){

    return $bugData;
  }
   
  function onUpdateBug($p_event, $bugData, $bugID){
    
    return $bugData;
  }
  
  function onNoteAdded($p_event, $bugNote, $bugID){
    
    return$BugNote;
  }
}
?>