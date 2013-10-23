<?php


class Dashboard_Models_Dbtables_DashboardThemeDbtable extends Zend_Db_Table_Abstract {

	protected $_name = 'plugin_dashboard_theme';

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

