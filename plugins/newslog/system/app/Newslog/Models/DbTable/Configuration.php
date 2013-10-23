<?php
/**
 * Configuration
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/20/12
 * Time: 3:29 PM
 */
class Newslog_Models_DbTable_Configuration extends Zend_Db_Table_Abstract {

    protected $_name = 'plugin_newslog_configuration';

    public function updateParam($name, $value) {
        if ($value === null) {
            return false;
        }
        $rowset = $this->find($name);
        $row    = $rowset->current();
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
        return $this->getAdapter()->fetchPairs($this->select());
    }
}
