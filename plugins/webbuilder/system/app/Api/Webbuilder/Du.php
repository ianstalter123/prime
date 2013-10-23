<?php
/**
 * Webbuilder direct upload API
 *
 * @author Eugene I. Nezhuta <eugene@seotoaster.com>
 * User: Eugene I. Nezhuta <eugene@seotoaster.com>
 * Date: 4/19/13
 * Time: 4:53 PM
 */

class Api_Webbuilder_Du extends Api_Service_Abstract {

    private $_websiteHelper = null;

    private $_debugMode    = false;

    protected $_accessList  = array(
        Tools_Security_Acl::ROLE_USER       => array('allow' => array('get', 'post', 'put', 'delete')),
        Tools_Security_Acl::ROLE_SUPERADMIN => array('allow' => array('get', 'post', 'put', 'delete')),
        Tools_Security_Acl::ROLE_ADMIN      => array('allow' => array('get', 'post', 'put', 'delete'))
    );

    public function init() {
        $this->_websiteHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('website');
        $this->_debugMode     = Tools_System_Tools::debugMode();
    }

    /**
     * Upload an image
     *
     */
    public function postAction() {
        $imageData           = $this->_request->getParams();
        $imageName           = $imageData['imageName'];
        $pathToDirectory     = $this->_websiteHelper->getPath() . 'media' . DIRECTORY_SEPARATOR . $imageData['folderName'] . DIRECTORY_SEPARATOR;
        $thumbnailsDirectory = $pathToDirectory .'thumbnails'. DIRECTORY_SEPARATOR;
        $cropDirectory       = $pathToDirectory .'crop'. DIRECTORY_SEPARATOR;

        if(!is_dir($thumbnailsDirectory)) {
            @mkdir($thumbnailsDirectory);
        }
        if(!is_dir($cropDirectory)) {
            @mkdir($cropDirectory);
        }

        $useCrop   = true;
        $thumbSize = Widgets_Directupload_Directupload::DEFAULT_THUMB_SIZE;

        Tools_Image_Tools::resize($pathToDirectory . 'original' . DIRECTORY_SEPARATOR . $imageName, $thumbSize, !($useCrop), $thumbnailsDirectory, $useCrop);
        Tools_Image_Tools::resize($pathToDirectory . 'original' . DIRECTORY_SEPARATOR . $imageName, $thumbSize, !($useCrop), $cropDirectory, $useCrop);

        $mapper    = Application_Model_Mappers_ContainerMapper::getInstance();
        $container = $mapper->findByName($imageData['containerName']);
        if(!$container instanceof Application_Model_Models_Container) {
            $container = new Application_Model_Models_Container();
            $container->setPageId($imageData['pageId'])
                ->setName($imageData['containerName']);
        }
        $container->setContent($imageName);
        $mapper->save($container);
    }

    /**
     * Removing image
     *
     */
    public function deleteAction() {
        $data       = Zend_Json::decode($this->_request->getRawBody());
        $folderPath	= realpath($this->_websiteHelper->getPath() . $this->_websiteHelper->getMedia() . $data['folderName']);

        if(!isset($data['imageName']) || !isset($data['folderName'])) {
            $this->_error('Folder name or image name not specified');
        }

        // get an image name from the image path and image info
        $data['imageName'] = basename($data['imageName']);
        $info              = pathinfo($data['imageName']);

        try {
            // removing image from filesystem
            if(($result = Tools_Image_Tools::removeImageFromFilesystem($data['imageName'], $data['folderName'])) !== true) {
                $this->_error($result);
            }

            // removing the container
            $mapper    = Application_Model_Mappers_ContainerMapper::getInstance();
            $container = $mapper->findByName($info['filename']);
            if($container instanceof Application_Model_Models_Container) {
                $mapper->delete($container);
            }

            //cleaning up the file system if needed
            $folderContent = Tools_Filesystem_Tools::scanDirectory($folderPath, false, true);
            if(empty($folderContent)) {
                try {
                    Tools_Filesystem_Tools::deleteDir($folderPath);
                } catch (Exception $e) {
                    $this->_debugMode && error_log($e->getMessage());
                    $this->_error($e->getMessage());
                }
            }
        } catch (Exceptions_SeotoasterException $e) {
            error_log($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            $this->_error($e->getMessage());
        }
    }

    public function getAction() {}
    public function putAction() {}
}