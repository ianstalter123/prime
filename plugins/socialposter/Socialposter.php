<?php
/**
 * Seotoaster 2.0 plugin bootstrap.
 *
 * @todo Add more comments
 * @author Seotoaster core team <core.team@seotoaster.com>
 */

class Socialposter extends Tools_Plugins_Abstract {

	/**
	 * List of action that should be allowed to specific roles
	 *
	 * By default all of actions of your plugin are available to the guest user
	 * @var array
	 */
	const SEOSAMBA_SOCIAL_URL = 'https://mojo.seosamba.com/social/';


	protected $_securedActions = array(
		Tools_Security_Acl::ROLE_SUPERADMIN => array(
            'secured'
        )
	);

	/**
	 * Init method.
	 *
	 * Use this method to init your plugin's data and variables
	 * Use this method to init specific helpers, view, etc...
	 */
	private $_websiteConfig = '';
	
	protected function _init() {
		$this->_websiteConfig = Zend_Controller_Action_HelperBroker::getStaticHelper('config')->getConfig();
		$this->_view->setScriptPath(__DIR__ . '/system/views/');
	}
	
	public function postToNetworksAction(){
        if(Tools_Security_Acl::isAllowed(Tools_Security_Acl::RESOURCE_PLUGINS)) {
            //$this->_view->appsData   = Models_Mapper_PosterConf::getInstance()->getConf();
            $this->_view->websiteUrl = $this->_seotoasterData['websiteUrl'];
            echo $this->_view->render('postToNetworks.phtml');
        }
    }
	
	public function postMessageAction() {
		if(Tools_Security_Acl::isAllowed(Tools_Security_Acl::RESOURCE_PLUGINS)) {
			$data['socialPost'] = array(
                'post_link'        => filter_var($this->_requestedParams['post_link'], FILTER_SANITIZE_URL),
                'post_description' => filter_var($this->_requestedParams['post_description'], FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES),
                'post_message'     => filter_var((strip_tags($this->_requestedParams['post_message'])), FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES)
            );
			$data['socialNetworks'] = $this->_requestedParams['networks'];
			$this->_processResponse($this->_apiCall('post', 'socialPostMessage', $data));
		}
	}
	
	private function _processResponse($response) {
		if($response != null) {
			if(isset($response['done'])) {
				if($response['done'] == true) {
					$this->_responseHelper->success($response['message']);
				}
				else {
					$this->_responseHelper->fail($response['message']);
				}
			}
			else {
				$this->_responseHelper->fail($response);
			}
		}
		else {
			$this->_responseHelper->fail('Unexpected error.');
		}
	}

	private function _apiCall($methodType, $methodName,$data = null) {
        if(isset($this->_websiteConfig['sambaToken']) && (isset($this->_websiteConfig['websiteId'])) ) {
			$url = parse_url($this->_websiteUrl);
			$data['websiteUrl'] = $url['host'];
			$data['websiteId'] = $this->_websiteConfig['websiteId'];
			$data['sambaToken'] = $this->_websiteConfig['sambaToken'];
			$seosambaRequest = Tools_Factory_PluginFactory::createPlugin('api',array(), array('websiteUrl' => $this->_websiteUrl));
			return $seosambaRequest::request($methodType, $methodName, $data);
		}
	}
	/**
	 * Main entry point
	 *
	 * @param array $requestedParams
	 * @return mixed $dispatcherResult
	 */
	/*public function run($requestedParams = array()) {
		$dispatcherResult = parent::run($requestedParams);
		return ($dispatcherResult) ? $dispatcherResult : '';
	}*/

	/**
	 * System hook to allow your plugin do some stuff before toaster controller starts
	 *
	 */
	/*public function beforeController() {

	}*/

	/**
	 * System hook to allow your plugin do some stuff after toaster controller finish its work
	 *
	 */
    public function registerSocialposterAction() {
        $this->_view->registrationHeaderMessage = $this->_translator->translate('Socialposter warning');
        echo $this->_view->render('postToNetworks.phtml');
    }

	private function _setSocialposterConnectLink() {
		if(Tools_Security_Acl::isAllowed(Tools_Security_Acl::RESOURCE_CONTENT)) {
			$configHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('config');
			$config = $configHelper->getConfig();
			if( isset($config['websiteId']) && ($config['websiteId'] != '') ) {
                $this->_view->socialConnectLink = self::SEOSAMBA_SOCIAL_URL . '?w=' . $config['websiteId'];
			}
            else {
                $this->_view->dataUrl = $this->_websiteUrl . 'plugin/socialposter/run/registerSocialposter';
            }
            $this->_injectContent($this->_view->render('socialposterConnectLink.phtml'));
		}
    }
	
	public function afterController() {
		echo $this->_setSocialposterConnectLink();
	}
}
