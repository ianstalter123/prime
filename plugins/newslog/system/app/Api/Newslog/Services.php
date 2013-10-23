<?php
/**
 *
 */
class Api_Newslog_Services extends Api_Service_Abstract {


    protected $_accessList = array(
        Tools_Security_Acl::ROLE_GUEST => array(
            'allow' => array('get', 'post', 'put')
        ),
        Tools_Security_Acl::ROLE_ADMIN => array(
            'allow' => array('get', 'post', 'put', 'delete')
        ),
        Tools_Security_Acl::ROLE_SUPERADMIN => array(
            'allow' => array('get', 'post', 'put', 'delete')
        )
    );

    protected $_pingServiceMapper = null;

    public function init() {
        $this->_pingServiceMapper = Newslog_Models_Mapper_PingServicesMapper::getInstance();
    }

    public function getAction() {
        $services = $this->_pingServiceMapper->fetchAll();
        return array_map(function($service) {
            return array(
                'id'        => $service->getId(),
                'url'       => $service->getUrl(),
                'status'    => $service->getStatus(),
                'isDefault' => $service->getIsDefault()
            );
        }, $services);
    }

    public function postAction() {
        $serviceData = Zend_Json::decode($this->_request->getRawBody());
        $service     = $this->_pingServiceMapper->save(
            new Newslog_Models_Model_PingService($serviceData));
        return $service->toArray();
    }

    public function putAction() {
        $id          = filter_var($this->_request->getParam('id'), FILTER_SANITIZE_NUMBER_INT);
        $serviceData = Zend_Json::decode($this->_request->getRawBody());
        if(!$id) {
            $this->_error();
        }
        $service = $this->_pingServiceMapper->find($id);
        if(!$service instanceof Newslog_Models_Model_PingService) {
            $this->_error('Service not found', self::REST_STATUS_NOT_FOUND);
        }
        return $this->_pingServiceMapper->save($service->setOptions($serviceData));
    }

    public function deleteAction() {
        $ids = array_filter(filter_var_array(explode(',', $this->_request->getParam('id')), FILTER_VALIDATE_INT));
        if(!empty($ids)) {
            $serviceMapper = Newslog_Models_Mapper_PingServicesMapper::getInstance();
            $services       =$serviceMapper->find($ids);
            if(is_array($services)) {
                foreach($services as $service) {
                    $serviceMapper->delete($service);
                }
            } else {
                $serviceMapper->delete($services);
            }
            return array('status' => 'removed');
        }
    }


}
