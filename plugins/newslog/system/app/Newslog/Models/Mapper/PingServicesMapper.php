<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iamne
 * Date: 10/2/12
 * Time: 4:48 PM
 * To change this template use File | Settings | File Templates.
 */
class Newslog_Models_Mapper_PingServicesMapper extends Application_Model_Mappers_Abstract {

    protected $_dbTable = 'Newslog_Models_DbTable_PingService';

    protected $_model   = 'Newslog_Models_Model_PingService';

    public function save($model) {
        if(!$model instanceof $this->_model) {
            $model = new $this->_model($model);
        }
        $data = array(
            'url'    => $model->getUrl(),
            'status' => $model->getStatus()
        );
        if($model->getId()) {
            $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $model->getId());
            $this->getDbTable()->update($data, $where);
        } else {
            $id = $this->getDbTable()->insert($data);
            if($id) {
                $model->setId($id);
            } else {
                throw new Exceptions_NewslogException('Can not save the model!');
            }
        }

        $model->notifyObservers();

        return $model;
    }

    public function fetchActive() {
        $where   = $this->getDbTable()->getAdapter()->quoteInto('status=?', Newslog_Models_Model_PingService::SERVICE_STATUS_ENABLED);
        return $this->fetchAll($where);
    }

    public function delete(Newslog_Models_Model_PingService $pingService) {
        $where        = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $pingService->getId());
        $deleteResult = $this->getDbTable()->delete($where);
        $pingService->notifyObservers();
        return $deleteResult;
    }

}
