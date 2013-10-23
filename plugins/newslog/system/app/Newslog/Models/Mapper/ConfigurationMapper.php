<?php
/**
 * ConfigMapper
 *
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/20/12
 * Time: 3:25 PM
 */
class Newslog_Models_Mapper_ConfigurationMapper extends Application_Model_Mappers_Abstract {

    protected $_dbTable = 'Newslog_Models_DbTable_Configuration';

    public function save($model) {
        $adapter = $this->getDbTable()->getAdapter();
        foreach($model as $name => $value) {
            if($name == 'folder') {
                $value = preg_replace('/[^A-Za-z0-9]/', '-', $value);
            }
            $data = array(
                'value' => $value
            );
            $where = $adapter->quoteInto('name=?', $name);
            $param = $this->fetchConfigParam($name);
            if($param !== null) {
                $result = $this->getDbTable()->update($data, $where);
            } else {
                $data['name'] = $name;
                $this->getDbTable()->insert($data);
            }
        }
        return $model;
    }

    /**
     * Select all configuration parameters
     *
     * @return array
     */
    public function fetchConfigParams() {
        return $this->getDbTable()->selectConfig();
    }

    public function fetchConfigParam($name) {
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
