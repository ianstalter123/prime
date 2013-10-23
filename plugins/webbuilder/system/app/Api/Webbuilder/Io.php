<?php
/**
 * Webbuilder image only API
 *
 * @author Eugene I. Nezhuta <eugene@seotoaster.com>
 * User: Eugene I. Nezhuta <eugene@seotoaster.com>
 * Date: 4/29/13
 * Time: 5:04 PM
 */

class Api_Webbuilder_Io extends Api_Service_Abstract {

    const DEFAULT_MEDIA_SUBFOLDER = 'small';

    private $_websiteHelper       = null;

    /**
     * Container mapper
     *
     * @var Application_Model_Mappers_ContainerMapper
     */
    private $_mapper              = null;

    protected $_accessList        = array(
        Tools_Security_Acl::ROLE_USER       => array('allow' => array('get', 'post', 'put', 'delete')),
        Tools_Security_Acl::ROLE_SUPERADMIN => array('allow' => array('get', 'post', 'put', 'delete')),
        Tools_Security_Acl::ROLE_ADMIN      => array('allow' => array('get', 'post', 'put', 'delete'))
    );

    public function init() {
        $this->_websiteHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('website');
        $this->_mapper        = Application_Model_Mappers_ContainerMapper::getInstance();
    }

    public function getAction() {
        $folder = filter_var($this->_request->getParam('folder'), FILTER_SANITIZE_STRING);
        if(!$folder) {
            $this->_error();
        }
        $folderPath = $this->_websiteHelper->getPath() . $this->_websiteHelper->getMedia() . $folder . DIRECTORY_SEPARATOR;
        if(!is_dir($folderPath)) {
            $this->_error();
        }
        $images = Tools_Filesystem_Tools::scanDirectory($folderPath . self::DEFAULT_MEDIA_SUBFOLDER);
        if(is_array($images) && !empty($images)) {
            $images = array_map(function($image) {
                return pathinfo($image);
            }, $images);
        }
        return $images;
    }

    public function postAction() {
        $ioData = array(
            'folder'      => filter_var($this->_request->getParam('folder'), FILTER_SANITIZE_STRING),
            'image'       => filter_var($this->_request->getParam('image'), FILTER_SANITIZE_STRING),
            'description' => filter_var($this->_request->getParam('description'), FILTER_SANITIZE_STRING),
            'linkedTo'    => filter_var($this->_request->getParam('linkedto'), FILTER_SANITIZE_STRING),
            'externalUrl' => filter_var($this->_request->getParam('externalUrl', ''), FILTER_SANITIZE_URL)
        );

        $containerName = filter_var($this->_request->getParam('container'), FILTER_SANITIZE_STRING);
        $pageId         = filter_var($this->_request->getParam('pid'), FILTER_SANITIZE_NUMBER_INT);

        $container      = $this->_mapper->findByName($containerName, $pageId);
        if(!$container instanceof Application_Model_Models_Container) {
            $container = new Application_Model_Models_Container();
            $container->setName($containerName)
                ->setPageId($pageId)
                ->setContainerType(($pageId) ? Application_Model_Models_Container::TYPE_REGULARCONTENT : Application_Model_Models_Container::TYPE_STATICCONTENT);
        }
        $container->setContent(Zend_Json::encode($ioData));
        return $this->_mapper->save($container);
    }

    public function deleteAction() {
        $ioData    = Zend_Json::decode($this->_request->getRawBody());
        if(empty($ioData) || !isset($ioData['container']) || !isset($ioData['pid'])) {
            $this->_error();
        }
        $container = $this->_mapper->findByName($ioData['container'], $ioData['pid']);
        if(!$container instanceof Application_Model_Models_Container) {
            $this->_error();
        }
        $this->_mapper->delete($container);
    }


    public function putAction() {}

}