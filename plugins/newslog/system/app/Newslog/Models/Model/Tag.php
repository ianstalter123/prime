<?php
/**
 * Tag
 *
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/23/12
 * Time: 3:55 PM
 */
class Newslog_Models_Model_Tag extends Application_Model_Models_Abstract {

    protected $_name      = '';

    protected $_createdAt = '';

    protected $_updatedAt = '';

    public function setName($name) {
        $this->_name = $name;
        return $this;
    }

    public function getName() {
        return $this->_name;
    }

    public function setCreatedAt($createdAt) {
        $this->_createdAt = $createdAt;
        return $this;
    }

    public function getCreatedAt() {
        return $this->_createdAt;
    }

    public function setUpdatedAt($updatedAt) {
        $this->_updatedAt = $updatedAt;
        return $this;
    }

    public function getUpdatedAt() {
        return $this->_updatedAt;
    }


}
