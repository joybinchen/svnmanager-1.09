<?php

require_once('svnmanager/global/Security.php');

class EditSelectPage extends TPage
{
	
	public function onLoad($param)
	{
		parent::onLoad($param);
		$userid = $this->User->getId();
				
		if($this->User->IsAdmin())
			$results = $this->Module->Database->Execute("SELECT * FROM repositories ORDER BY name");
		else
			$results = $this->Module->Database->Execute("SELECT * FROM repositories WHERE ownerid=" . makeSqlString($userid));
			
		if($results)
		{
			$data = array();
			while(!$results->EOF)
			{
				$fields = $results->fields;
				$owner = $this->Module->getUserName($fields['ownerid']);
        		$repoID = $fields['id'];
        		$descrResults = $this->Module->Database->Execute("SELECT * FROM repo_descriptions WHERE repo_id=" . makeSqlString($repoID));
        		$descrFields = $descrResults->fields;
        		if ($descrFields['id'])
        		{
          			$description = wordwrap(htmlspecialchars($descrFields['description']), 40, "<br />\n");
          			$description = $descrFields['description'];
        		}
        		else
        		{
          			$description = "No description for " . $fields['name'] . " repository";
		        }
				$data[] = array(
					'id' => $fields['id'],
					'repositoryname' => $fields['name'],
					'owner' => $owner,
          			'description' => $description
				);
			
				$results->MoveNext();
				$this->RepositoryTable->setDataSource($data);				
			}			
			$results->Close();			
		}	

		$this->dataBind();		
	}
	
	public function onSelectRepository($sender, $param)
	{
		$id = $param->parameter;		
		$this->Application->transfer('Repository:EditPage', array('RepositoryID' => $id));
	}

	public function onCancelBtn($sender, $param)
	{		
		$this->Application->transfer('Repository:AdminPage');
	}	

}
?>
