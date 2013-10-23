<?php
/**
 * Webbuilder featured area only API
 *
 * @author Eugene I. Nezhuta <eugene@seotoaster.com>
 * User: Eugene I. Nezhuta <eugene@seotoaster.com>
 * Date: 4/23/13
 * Time: 12:34 PM
 */

class Api_Webbuilder_Fo extends Api_Service_Abstract {

    protected $_accessList  = array(
        Tools_Security_Acl::ROLE_USER       => array('allow' => array('get', 'post', 'put', 'delete')),
        Tools_Security_Acl::ROLE_SUPERADMIN => array('allow' => array('get', 'post', 'put', 'delete')),
        Tools_Security_Acl::ROLE_ADMIN      => array('allow' => array('get', 'post', 'put', 'delete'))
    );

    public function postAction() {
        $pageId   = filter_var($this->_request->getParam('pageId'), FILTER_SANITIZE_NUMBER_INT);
        $name     = filter_var($this->_request->getParam('containerName'), FILTER_SANITIZE_STRING);

        // featured area specific fields
        $areaName         = filter_var($this->_request->getParam('areaName'), FILTER_SANITIZE_STRING);
        $pagesToShow      = filter_var($this->_request->getParam('maxRes'), FILTER_SANITIZE_NUMBER_INT);
        $descriptionLimit = filter_var($this->_request->getParam('maxChar'), FILTER_SANITIZE_NUMBER_INT);
        $useImage         = filter_var($this->_request->getParam('useImg'), FILTER_SANITIZE_NUMBER_INT);
        $random           = filter_var($this->_request->getParam('rand'), FILTER_SANITIZE_NUMBER_INT);

        $content          = $areaName  . ':' . $pagesToShow . ':' . $descriptionLimit;

        if($useImage) {
            $cropImage = $this->_request->getParam('cropImg');
            $content   .= ($cropImage) ? ':imgc' : ':img';
        }

        if($random) {
            $content .= ':' . $random;
        }

        $mapper    = Application_Model_Mappers_ContainerMapper::getInstance();
        $container = $mapper->findByName($name, $pageId);
        if(!$container instanceof Application_Model_Models_Container) {
            $container = new Application_Model_Models_Container();
            $container->setPageId($pageId)
                ->setContainerType((!$pageId) ? Application_Model_Models_Container::TYPE_STATICCONTENT :Application_Model_Models_Container::TYPE_REGULARCONTENT)
                ->setName($name);
        }
        $container->setContent($content);

        try {
            return array('error' => false, 'responseText' => $mapper->save($container));
        } catch (Exception $e) {
            return $this->_error($e->getMessage());
        }
    }

    public function getAction() {}
    public function putAction() {}
    public function deleteAction() {}


}