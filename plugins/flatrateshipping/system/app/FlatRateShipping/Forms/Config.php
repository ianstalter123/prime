<?php
/**
 * FlatRateShipping_Forms_Config.php
 * @author Pavel Kovalyov <pavlo.kovalyov@gmail.com>
 */

class FlatRateShipping_Forms_Config extends Zend_Form {

	const COMPARE_BY_AMOUNT = 'amount';

	const COMPARE_BY_WEIGHT = 'weight';

	public function init(){

		$this->setDecorators(array('Form', 'FormElements'));
		$this->setElementDecorators(array(
			array('Label', array('class' => 'mt5px')),
			'ViewHelper'
		));

		$this->addElement('text', 'title', array(
			'label' => 'Custom title',
			'placeholder' => 'e.g., Delivery by our courier (3-5 buis. days)',
		));

		$this->addElement('select', 'units', array(
			'label' => 'Units',
			'value' => 'amount',
			'multiOptions' => array(
				self::COMPARE_BY_AMOUNT => 'total amount',
				self::COMPARE_BY_WEIGHT => 'order weight'
			)
		));

		//amount limit
		$this->addElement('text', 'value1', array(
			'label'     => 'up to',
		));
		$this->addElement('text', 'value2', array(
			'label'     => 'up to',
		));
		$this->addElement('text', 'value3', array(
			'label'     => 'over',
		));

		//national
		$this->addElement('text', 'national1', array());
		$this->addElement('text', 'national2', array());
		$this->addElement('text', 'national3', array());

		//international
		$this->addElement('text', 'international1', array());
		$this->addElement('text', 'international2', array());
		$this->addElement('text', 'international3', array());
	}
}
