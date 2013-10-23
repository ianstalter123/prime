<?php


class Invoicetopdf_Models_Dbtables_InvoicetopdfSettingDbtable extends Zend_Db_Table_Abstract {

	protected $_name = 'plugin_invoicetopdf_settings';
    
    public function updateParam($name, $value) {	
		if ($value === null) {
			return false;
		}
		$rowset = $this->find($name);
		$row = $rowset->current();
		if ($row === null) {
			$row = $this->createRow( array(
				'name'	=> $name,
				'value' => $value
			));			
		} else {
			$row->value = $value;
		}

		return $row->save();
	}

	public function selectConfig() {
		return $this->getAdapter()->fetchPairs($this->select()->from($this->_name));
	}
}

