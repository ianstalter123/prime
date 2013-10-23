<?php

/**
 * Settings
 *
 * @author Pavel Kovalyov <pavlo.kovalyov@gmail.com>
 */
class Forms_GeneralSettings extends Zend_Form {

	public function init() {
		$this->setLegend('Store info')
			->setDecorators(array('Form', 'FormElements'));
		
		
		$this->addElement('text', 'company', array(
			'label' => 'Store front name'
		));
		
		$this->addElement('text', 'email', array(
			'label' => 'Store front e-mail'
		));
		
		$this->addElement('text', 'address1', array(
			'label' => 'Address 1'
		));
		
		$this->addElement('text', 'address2', array(
			'label' => 'Address 2'
		));
		
		$this->addElement('select', 'country', array(
			'label' => 'Country',
            'disableTranslator' => 'true',
			'multiOptions' => Tools_Geo::getCountries(true)
		));
		
		$this->addElement('text', 'city', array(
			'label' => 'City'
		));
		
		$this->addElement('select', 'state', array(
			'label' => 'State/Province/Region',
            'disableTranslator' => 'true',
			'multiOptions' => Tools_Geo::getState(null, true)
		));
		
		$this->addElement('text', 'zip', array(
			'label' => 'Zip/Postal Code'
		));

		$this->addElement('text', 'phone', array(
			'label' => 'Phone'
		));
	}

	public function setDefault($name, $value) {
		switch ($name) {
			case 'state':
				$list = Tools_Geo::getState($this->getElement('country')->getValue(), true);
				if (empty ($list) || !array_key_exists($value, $list)) {
					return $this;
				}
				$this->getElement($name)->setMultiOptions($list);
				break;
			case 'country':
				$this->getElement('state')->setMultiOptions(Tools_Geo::getState($value));
				break;
		}
		parent::setDefault($name, $value);
	}
}