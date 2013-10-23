<?php

class Application_Model_Models_User extends Application_Model_Models_Abstract implements Zend_Acl_Role_Interface {

    protected $_email        = '';

	protected $_password     = '';

	protected $_roleId       = '';

	protected $_fullName     = '';

	protected $_lastLogin    = null;

	protected $_regDate      = '';

	protected $_ipaddress    = '';

	protected $_referer      = '';

    protected $_gplusProfile = '';

    public function setGplusProfile($gplusProfile) {
        $this->_gplusProfile = $gplusProfile;
        return $this;
    }

    public function getGplusProfile() {
        return $this->_gplusProfile;
    }

	public function getRoleId() {
		return ($this->_roleId) ? $this->_roleId : Tools_Security_Acl::ROLE_GUEST;
	}

	public function setRoleId($roleId) {
		$this->_roleId = $roleId;
		return $this;
	}

	public function getEmail() {
		return $this->_email;
	}

	public function setEmail($email) {
		$this->_email = $email;
		return $this;
	}

	public function getPassword() {
		return $this->_password;
	}

	public function setPassword($password) {
		$this->_password = $password;
		return $this;
	}

	public function getFullName() {
		return $this->_fullName;
	}

	public function setFullName($fullName) {
		$this->_fullName = $fullName;
		return $this;
	}

	public function getLastLogin() {
		return $this->_lastLogin;
	}

	public function setLastLogin($lastLogin) {
		$this->_lastLogin = $lastLogin;
		return $this;
	}

	public function getRegDate() {
		return $this->_regDate;
	}

	public function setRegDate($regDate) {
		$this->_regDate = $regDate;
		return $this;
	}

	public function getIpaddress() {
		return $this->_ipaddress;
	}

	public function setIpaddress($ipaddress) {
		$this->_ipaddress = $ipaddress;
		return $this;
	}

	public function setReferer($referer) {
		$this->_referer = $referer;
		return $this;
	}

	public function getReferer() {
		return $this->_referer;
	}


}

