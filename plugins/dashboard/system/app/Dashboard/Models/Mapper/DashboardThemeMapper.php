<?php


class Dashboard_Models_Mapper_DashboardThemeMapper extends Application_Model_Mappers_Abstract {

	protected $_dbTable = 'Dashboard_Models_Dbtables_DashboardThemeDbtable';

	protected $_model   = 'Dashboard_Models_Models_DashboardThemeModel';

	public function save($plugin) {
		if (!is_array($plugin) || empty ($plugin)){
			throw new Exceptions_SeotoasterPluginException('Given parameter should be non empty array');
		}
		
		array_walk($plugin, function($value, $key, $dbTable){
			$dbTable->updateParam($key, $value);
		}, $this->getDbTable());
				
	}
    
    public function deleteHtmlFile($htmlFile){
        $where = $this->getDbTable()->getAdapter()->quoteInto("name=?", $htmlFile);
		$this->getDbTable()->delete($where);
    } 
    
    
    public function getThemeContent() {
		return $this->getDbTable()->selectConfig();
	}
    
    public function getHtmlContent($htmlFile){
       	$where = 'name="'.$htmlFile.'"';
        $properties = $this->fetchAll($where);
        if(!empty($properties)){
           return $properties[0]->getValue();
        }
		return $properties;
    
    }

    public function getConfigParam($name) {
		if (!$name) {
			return null;
		}
		
		$row = $this->getDbTable()->find($name);
		if ($row = $row->current()){
			return $row->value;
		}
		return null;
	}
    
}

