<?php
/**
 *
 */
class Newslog_Tools_Pinger {

    const DEFAULT_PING_ALL    = 'all';

    private $_newsFolder      = null;

    /**
     * @var Newslog_Tools_Distributor
     */
    private static $_instance = null;

    private $_defaultConfig   = array(
        'port'   => '80',
        'method' => 'weblogUpdates.ping'
    );

    private function __construct() {}

    private function __clone() {}

    public static function getInstance() {
        if(self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function init() {
        $this->_init();
        return $this;
    }

    private function _init() {
        $this->_newsFolder = Newslog_Models_Mapper_ConfigurationMapper::getInstance()->fetchConfigParam('folder');
    }

    /**
     * @param Newslog_Models_Model_News $news
     * @param string $service
     * @throws Exceptions_SeotoasterPluginException
     */
    public function ping($news, $service = self::DEFAULT_PING_ALL) {
        if($service === self::DEFAULT_PING_ALL) {
            $services = Newslog_Models_Mapper_PingServicesMapper::getInstance()->fetchActive();
            if(!is_array($services) || empty($services)) {
                throw new Exceptions_SeotoasterPluginException('Newslog plugin ping error: There are no services to ping.');
            }
            foreach($services as $service) {
                $this->_ping($news, $service->getUrl());
            }
        }
    }

    /**
     * @param Newslog_Models_Model_News $news
     * @param string $serviceUrl
     */
    private function _ping($news, $serviceUrl) {
        $websiteHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('website');

        if(!$this->_newsFolder) {
            $this->_init();
        }

        $xmlRpc = new Zend_XmlRpc_Client($serviceUrl);

        $params = array(
            new Zend_XmlRpc_Value_String(parse_url($websiteHelper->getUrl(), PHP_URL_HOST)), // weblog name
            new Zend_XmlRpc_Value_String($websiteHelper->getUrl() . $this->_newsFolder . '/'), //weblog url (news index page url)
            new Zend_XmlRpc_Value_String($websiteHelper->getUrl() . $news->getPage()->getUrl()) // news page url
        );

        $result = $xmlRpc->call($this->_defaultConfig['method'], $params);
    }

}
