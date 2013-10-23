<?php
/**
 * TriggerAction model
 * @author Pavel Kovalyov <pavlo.kovalyov@gmail.com>
 */
class Application_Model_Models_TriggerAction extends Application_Model_Models_Abstract {

	protected $_trigger;

	protected $_template;

	protected $_recipient;

	protected $_message;

    protected $_from    = '';

    protected $_subject = '';


	public function setMessage($message) {
		$this->_message = $message;
	}

	public function getMessage() {
		return $this->_message;
	}

	public function setRecipient($recipient) {
		$this->_recipient = $recipient;
	}

	public function getRecipient() {
		return $this->_recipient;
	}

	public function setTemplate($templateName) {
		$this->_template = $templateName;
	}

	public function getTemplate() {
		return $this->_template;
	}

	public function setTrigger($triggerName) {
		$this->_trigger = $triggerName;
	}

	public function getTrigger() {
		return $this->_trigger;
	}

    public function setFrom($from) {
        $this->_from = $from;
        return $this;
    }

    public function getFrom() {
        return $this->_from;
    }

    public function setSubject($subject) {
        $this->_subject = $subject;
        return $this;
    }

    public function getSubject() {
        return $this->_subject;
    }

}
