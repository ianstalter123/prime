<?php


class Invoicetopdf_Models_Mapper_InvoicetopdfSettingsMapper extends Application_Model_Mappers_Abstract {

	protected $_dbTable = 'Invoicetopdf_Models_Dbtables_InvoicetopdfSettingDbtable';

	protected $_model   = 'Invoicetopdf_Models_Models_InvoicetopdfSettingModel';

	public function save($config) {
		if (!is_array($config) || empty ($config)){
			throw new Exceptions_SeotoasterPluginException('Given parameter should be non empty array');
		}
		
		array_walk($config, function($value, $key, $dbTable){
			$dbTable->updateParam($key, $value);
		}, $this->getDbTable());
		
		
	}

    
	public function getConfigParams() {
		return $this->getDbTable()->selectConfig();
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

