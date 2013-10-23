<?php
/**
 *
 */
class Newslog_Tools_Feed {

    const TYPE_ALL      = 'all';

    const TYPE_FULL     = 'full';

    /**
     * @var Newslog_Tools_Feed
     */
    private static $_instance = null;

    private $_news      = array();

    private $_feedTpye  = self::TYPE_FULL;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance() {
        if(self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function generate(array $news, $feedType = false) {
        $this->_news = $news;
        if(!$feedType) {
            // generate full xml feed
            $this->_generate();

            // generate all xml feed
            $this->_feedTpye = self::TYPE_ALL;
            $this->_generate();

            return true;
        }
        $this->_feedTpye = $feedType;
        return $this->_generate();
    }

    private function _generate() {
        $websiteHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('website');
        $miscConfig    = Zend_Registry::get('misc');
        $view          = new Zend_View(array(
            'scriptPath' => $websiteHelper->getPath() . $miscConfig['pluginsPath'] . 'newslog/system/views/'
        ));

        $view->news          = $this->_news;
        $view->feedType      = $this->_feedTpye;
        $view->websiteUrl    = $websiteHelper->getUrl();
        $view->websitePath   = $websiteHelper->getPath();
        $view->newsConfig    = Newslog_Models_Mapper_ConfigurationMapper::getInstance()->fetchConfigParams();
        $view->websiteConfig = Application_Model_Mappers_ConfigMapper::getInstance()->getConfig();

        try {
            Tools_Filesystem_Tools::saveFile($websiteHelper->getPath() . $miscConfig['feedsPath'] . $this->_feedTpye . '.xml', $view->render('feed.phtml'));
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
        return true;
    }

}
