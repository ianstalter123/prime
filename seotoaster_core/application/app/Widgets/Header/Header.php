<?php
/**
 * Header widget
 *
 * @author iamne
 */
class Widgets_Header_Header extends Widgets_AbstractContent {

	protected function  _init() {
		$this->_type    = (isset($this->_options[1]) && $this->_options[1] == 'static') ? Application_Model_Models_Container::TYPE_STATICHEADER : Application_Model_Models_Container::TYPE_REGULARHEADER;
		parent::_init();
	}

	protected function  _load() {
		$header      = Application_Model_Mappers_ContainerMapper::getInstance()->findByName($this->_name, $this->_pageId, $this->_type);
		$headerContent = (null === $header) ? '' : $header->getContent();
		if(Tools_Security_Acl::isAllowed($this)) {
			$headerContent .= $this->_addAdminLink($this->_type, (!$headerContent) ? null : $header->getId(), 'Click to edit header', 604, 130);
			if ((bool)Zend_Controller_Action_HelperBroker::getExistingHelper('config')->getConfig('inlineEditor')){
				$headerContent = '<div class="container-wrapper">'.$headerContent.'</div>';
			}
		}
		return $headerContent;
	}

	/**
	 * Overrides abstract class method
	 * For Header and Content widgets we have a different resource id
	 *
	 * @return string ACL Resource id
	 */
	public function  getResourceId() {
		return Tools_Security_Acl::RESOURCE_CONTENT;
	}
}

