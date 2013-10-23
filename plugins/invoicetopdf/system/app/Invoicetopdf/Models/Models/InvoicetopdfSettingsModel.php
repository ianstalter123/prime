<?php


class Invoicetopdf_Models_Models_InvoicetopdfSettingModel extends Application_Model_Models_Abstract {

   protected $_name = '';
   protected $_value = '';
      
   public function getName() {
		return $this->_name;
   }
   public function setName($name) {
		$this->_name = $name;
		return $this;
   }
   
   public function getValue() {
		return $this->_value;
   }
   public function setValue($value) {
		$this->_value = $value;
		return $this;
   }

}

