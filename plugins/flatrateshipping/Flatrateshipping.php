<?php
/**
 * Flat Rate shipping calculator
 *
 * @author Seotoaster core team <pavel.k@seosamba.com>
 */

class Flatrateshipping extends Tools_Shipping_Plugin {

	/**
	 * List of action that should be allowed to specific roles
	 *
	 * By default all of actions of your plugin are available to the guest user
	 * @var array
	 */
	protected $_securedActions = array(
		Tools_Security_Acl::ROLE_SUPERADMIN => array(
            'config'
        )
	);

	/**
	 * Init method.
	 *
	 * Use this method to init your plugin's data and variables
	 * Use this method to init specific helpers, view, etc...
	 */
	protected function _init() {
		parent::_init();
		$this->_view->setScriptPath(__DIR__ . '/system/views/');
	}

	/**
	 * Secured action.
	 *
	 * Will be available to the superadmin only
	 */
	public function configAction() {
		if (Tools_Security_Acl::isAllowed(Tools_Security_Acl::RESOURCE_PLUGINS)){
			$form = new FlatRateShipping_Forms_Config();
			if ($this->_request->isPost()){
				if ($form->isValid($this->_request->getParams())){
					$config = array(
						'name'      => strtolower(__CLASS__),
						'config'    => $form->getValues()
					);
					if (Models_Mapper_ShippingConfigMapper::getInstance()->save($config)){
						$this->_jsonHelper->direct(array('done' => 'true'));
					}
				}
			} else {
				$config = Models_Mapper_ShippingConfigMapper::getInstance()->find(strtolower(__CLASS__));
				if (!empty($config['config'])){
					$form->populate($config['config']);
				}
			}

			$this->_view->form = $form;
			$this->_view->shoppingConfig = Models_Mapper_ShoppingConfig::getInstance()->getConfigParams();
			echo $this->_view->render('config.phtml');
		}
	}

	public function calculateAction($noJson = false){
		if (sizeof(Tools_ShoppingCart::getInstance()->getContent()) === 0) {
			throw new Exceptions_SeotoasterException('Cart is empty');
		}

		$pluginSettings = Models_Mapper_ShippingConfigMapper::getInstance()->find(strtolower(get_called_class()));
		if (!$pluginSettings || !isset($pluginSettings['config'])) {
			throw new Exceptions_SeotoasterPluginException(__CLASS__.' Error: plugin is not configured');
		}
//		$this->_storeRates(null);

		$origination = Tools_Misc::clenupAddress($this->_shoppingConfig);
		$destination = Tools_ShoppingCart::getAddressById(Tools_ShoppingCart::getInstance()->getAddressKey(Models_Model_Customer::ADDRESS_TYPE_SHIPPING));
		$deliveryType = ($origination['country'] === $destination['country']) ?
				Forms_Shipping_FreeShipping::DESTINATION_NATIONAL : Forms_Shipping_FreeShipping::DESTINATION_INTERNATIONAL;

		switch ($pluginSettings['config']['units']){
			case FlatRateShipping_Forms_Config::COMPARE_BY_AMOUNT:
				$comparator = Tools_ShoppingCart::getInstance()->calculateCartPrice();
				break;
			case FlatRateShipping_Forms_Config::COMPARE_BY_WEIGHT:
				$comparator = Tools_ShoppingCart::getInstance()->calculateCartWeight();
				break;
		}

		$method = array();
		if (!empty($pluginSettings['config']['title'])){
			$method['type'] = $pluginSettings['config']['title'];
		}
		if ($comparator < $pluginSettings['config']['value1']){
			$method['price'] = $pluginSettings['config'][$deliveryType.'1'];
		} elseif ($comparator < $pluginSettings['config']['value2']) {
			$method['price'] = $pluginSettings['config'][$deliveryType.'2'];
		} elseif ($comparator > $pluginSettings['config']['value3']) {
			$method['price'] = $pluginSettings['config'][$deliveryType.'3'];
		}

		$this->_storeRates(array($method));
        if($method['price'] == ''){
	        $response = array('error' => 'Unfortunately, we can\'t ship to this location. Please contact us to make other arrangements.');
            return ($noJson === true ? $response : $this->_jsonHelper->direct(array($response)));
        }
		if ($noJson === true){
			return $method;
		}
		$method['price'] = $this->_view->currency($method['price']);

		$this->_jsonHelper->direct(array($method));
	}


}
