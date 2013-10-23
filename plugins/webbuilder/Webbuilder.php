<?php

class Webbuilder extends Tools_Plugins_Abstract {

    const VIEWS_POSTFIX   = '.webbuilder.phtml';

    /**
     * Actions access list
     *
     * @var array
     */
    protected $_securedActions = array(
        Tools_Security_Acl::ROLE_USER => array('textonly', 'imageonly', 'featuredonly', 'galleryonly', 'imageonly')
    );

    /**
     * Seotoaster config helper
     *
     * @var Helpers_Action_Config
     */
    protected $_configHelper = null;

    /**
     * Zend layout instance to render all the plugin's screens
     *
     * @var Zend_Layout
     */
    protected $_layout       = null;

    protected function _init() {
        // initialize layout
        $this->_layout = new Zend_Layout();
        $this->_layout->setLayoutPath(__DIR__ . '/system/views/');

        // set proper view script pathes
        if(($scriptPaths = Zend_Layout::getMvcInstance()->getView()->getScriptPaths()) !== false) {
            $this->_view->setScriptPath($scriptPaths);
        }
        $this->_view->addScriptPath(__DIR__ . '/system/views/');

        // initialize helpers
        $this->_configHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('config');
    }

    /**
     * Text only edit screen
     *
     */
    public function textonlyAction() {
        $containerName = filter_var($this->_request->getParam('container'), FILTER_SANITIZE_STRING);
        $pageId        = filter_var($this->_request->getParam('pageId'), FILTER_SANITIZE_NUMBER_INT);
        $container     = Application_Model_Mappers_ContainerMapper::getInstance()->findByName($containerName, $pageId);

        // assign view variables
        $this->_view->content       = ($container instanceof Application_Model_Models_Container) ? $container->getContent() : '';
        $this->_view->pageId        = $pageId;
        $this->_view->containerName = $containerName;
        $this->_view->currentTheme  = $this->_configHelper->getConfig('currentTheme');

        // render
        $this->_show();
    }

    /**
     * Featured only screen
     *
     */
    public function featuredonlyAction() {
        $containerName = filter_var($this->_request->getParam('container'), FILTER_SANITIZE_STRING);
        $pageId        = filter_var($this->_request->getParam('pageId'), FILTER_SANITIZE_NUMBER_INT);
        $container     = Application_Model_Mappers_ContainerMapper::getInstance()->findByName($containerName, $pageId);

        // assign view variables
        $this->_view->content       = ($container instanceof Application_Model_Models_Container) ? explode(':', $container->getContent()) : '';
        $this->_view->pageId        = $pageId;
        $this->_view->containerName = $containerName;
        $this->_view->areas         = Application_Model_Mappers_FeaturedareaMapper::getInstance()->fetchAll();

        // render
        $this->_show();
    }

    /**
     * Gallery only screen
     *
     */
    public function galleryonlyAction() {
        $containerName = filter_var($this->_request->getParam('containerName'), FILTER_SANITIZE_STRING);
        $pageId        = filter_var($this->_request->getParam('pageId'), FILTER_SANITIZE_NUMBER_INT);
        $container     = Application_Model_Mappers_ContainerMapper::getInstance()->findByName($containerName, $pageId);

        $content       = ($container instanceof Application_Model_Models_Container) ? explode(':', $container->getContent()) : '';

        // assign view variables
        if(is_array($content) && !empty($content)) {
            $this->_view->galleryName = $content[0];
            $this->_view->thumbs      = $content[1];
            $this->_view->crop        = $content[2];
            $this->_view->caption     = $content[3];
        }

        $this->_view->pageId        = $pageId;
        $this->_view->containerName = $containerName;
        $this->_view->listofFolders = Tools_Filesystem_Tools::scanDirectoryForDirs($this->_websiteHelper->getPath() . $this->_websiteHelper->getMedia());;

        // render
        $this->_show();
    }

    public function imageonlyAction() {
        $containerName = filter_var($this->_request->getParam('containerName'), FILTER_SANITIZE_STRING);
        $pageId        = filter_var($this->_request->getParam('pid'), FILTER_SANITIZE_NUMBER_INT);
        $container     = Application_Model_Mappers_ContainerMapper::getInstance()->findByName($containerName, $pageId);
        $ioData        = array();

        if($container instanceof Application_Model_Models_Container) {
            $ioData = Zend_Json::decode($container->getContent());
        }

        $mediaFolders = Tools_Filesystem_Tools::scanDirectoryForDirs($this->_websiteHelper->getPath() . 'media/');
        if(!empty($mediaFolders)) {
            foreach($mediaFolders as $key => $mediaFolder) {
                $mediaSubFolder = $this->_websiteHelper->getPath() . 'media/' . $mediaFolder . '/small/';
                if(!is_dir($mediaSubFolder)) {
                    continue;
                }
                if((boolean)Tools_Filesystem_Tools::scanDirectory($mediaSubFolder)) {
                    continue;
                }
                unset($mediaFolders[$key]);
            }
        }
        asort($mediaFolders);

        if(is_array($ioData) && !empty($ioData)) {
            foreach($ioData as $key => $value) {
                $this->_view->$key = $value;
            }
        }

        $this->_view->folders       = $mediaFolders;
        $this->_view->description   = isset($ioData['description']) ? $ioData['description'] : '';
        $this->_view->pageId        = $pageId;
        $this->_view->containerName = $containerName;

        // render
        $this->_show();
    }

    public static function exportWebsiteData() {
        $media     = array();
        $dbAdapter = Zend_Registry::get('dbAdapter');
        $wbContent = $dbAdapter->fetchCol("SELECT `content` FROM `container` WHERE `name` LIKE 'wb_%';");
        $ioPattern = '~{"folder":"([\w\-]*)","image":"([\w\-]*\.jpg|png|jpeg|gif)".*}~';
        $goPattern = '~^([\w]*):[:\d]*~';
        $duPattern = '~(wb_[\w]*\.jpg|png|jpeg|gif)~';
        if(is_array($wbContent) && !empty($wbContent)) {
            foreach($wbContent as $key => $wbItem) {
                if(preg_match($ioPattern, $wbItem)) {
                    $media[] = preg_replace($ioPattern, 'media/$1/original/$2', $wbItem);
                }
                if(preg_match($goPattern, $wbItem)) {
                    $media = array_merge($media, glob(preg_replace($goPattern, 'media/$1/original/*', $wbItem)));
                }
                if(preg_match($duPattern, $wbItem)) {
                    $media = array_merge($media, glob(preg_replace($duPattern, 'media/*/original/$1', $wbItem)));
                }

            }
        }

        return array('media' => $media);
    }

    /**
     * Render a proper view script
     *
     * If $screenViewScript not passed, generates view script file name automatically using the action name and VIEWS_POSTFIX
     * @param string $screenViewScript
     */
    private function _show($screenViewScript = '') {
        if(!$screenViewScript) {
            $trace  = debug_backtrace(false);
            $screenViewScript = str_ireplace('Action', self::VIEWS_POSTFIX, $trace[1]['function']);
        }
        $this->_layout->content = $this->_view->render($screenViewScript);
        echo $this->_layout->render();
    }
}

