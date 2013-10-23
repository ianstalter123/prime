<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iamne
 * Date: 10/2/12
 * Time: 4:49 PM
 * To change this template use File | Settings | File Templates.
 */
class Newslog_Models_Model_PingService extends Application_Model_Models_Abstract {

    const SERVICE_STATUS_ENABLED  = 'enabled';

    const SERVICE_STATUS_DISABLED = 'disabled';

    protected $_status  = self::SERVICE_STATUS_DISABLED;

    protected $_url     = '';

    protected $_isDefault = false;

    public function setStatus($status) {
        $this->_status = $status;
        return $this;
    }

    public function getStatus() {
        return $this->_status;
    }

    public function setUrl($url) {
        $this->_url = $url;
        return $this;
    }

    public function getUrl() {
        return $this->_url;
    }

    public function setIsDefault($isDefault) {
        $this->_isDefault = $isDefault;
        return $this;
    }

    public function getIsDefault() {
        return (boolean)$this->_isDefault;
    }


}
