<?php
/**
 * News model
 *
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/19/12
 * Time: 6:17 PM
 */
class Newslog_Models_Model_News extends Application_Model_Models_Abstract {

    const TYPE_INTERNAL    = 'internal';

    const TYPE_EXTERNAL    = 'external';

    const PR_TAG           = 'PR';

    protected $_pageId     = 0;

    protected $_title      = '';

    protected $_teaser     = '';

    protected $_content    = '';

    protected $_broadcast  = false;

    protected $_published  = false;

    protected $_featured   = false;

    protected $_archived   = false;

    protected $_metaData   = '';

    protected $_type       = self::TYPE_INTERNAL;

    protected $_createdAt  = '';

    protected $_updatedAt  = '';

    protected $_tags       = null;

    protected $_externalId = null;

    protected $_userId     = null;

    public function setUserId($userId) {
        $this->_userId = $userId;
        return $this;
    }

    public function getUserId() {
        return $this->_userId;
    }

    public function setExternalId($externalId) {
        $this->_externalId = $externalId;
        return $this;
    }

    public function getExternalId() {
        return $this->_externalId;
    }

    public function setArchived($archived) {
        $this->_archived = $archived;
        return $this;
    }

    public function getArchived() {
        return $this->_archived;
    }

    public function setBroadcast($broadcast) {
        $this->_broadcast = $broadcast;
        return $this;
    }

    public function getBroadcast() {
        return $this->_broadcast;
    }

    public function setContent($content) {
        $this->_content = $content;
        return $this;
    }

    public function getContent() {
        return $this->_content;
    }

    public function setFeatured($featured) {
        $this->_featured = $featured;
        return $this;
    }

    public function getFeatured() {
        return $this->_featured;
    }

    public function setPageId($pageId) {
        $this->_pageId = $pageId;
        return $this;
    }

    public function getPageId() {
        return $this->_pageId;
    }

    public function setPublished($published) {
        $this->_published = $published;
        return $this;
    }

    public function getPublished() {
        return $this->_published;
    }

    public function setTeaser($teaser) {
        $this->_teaser = $teaser;
        return $this;
    }

    public function getTeaser() {
        return $this->_teaser;
    }

    /**
     * Set news item meta information. Should be json encoded string or an array
     *
     * If array passed as $metaData then it will be automatically json encoded
     * @param string|array $metaData
     * @return Newslog_Models_Model_News
     */
    public function setMetaData($metaData) {
        $this->_metaData = is_array($metaData) ? Zend_Json::encode($metaData) : $metaData;
        return $this;
    }

    /**
     * Get news item meta information. By default meta is a json decoded string
     *
     * @param bool $decode Pass true as decode value to get meta info as an array.
     * @return mixed|string Json encoded string or array
     */
    public function getMetaData($decode = false) {
        return ($decode) ? Zend_Json::decode($this->_metaData) : $this->_metaData;
    }

    public function getMetaDataValue($key) {
        $metaData = Zend_Json::decode($this->_metaData);
        return (isset($metaData[$key])) ? $metaData[$key] : null;
    }

    public function setType($type) {
        $this->_type = $type;
        return $this;
    }

    public function getType() {
        return $this->_type;
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

    public function setTags($tags) {
        $this->_tags = $tags;
        return $this;
    }

    public function getTags() {
        return $this->_tags;
    }

    public function setTitle($title) {
        $this->_title = $title;
        return $this;
    }

    public function getTitle() {
        return $this->_title;
    }

    public function isPressRelease() {
        if(!$this->_tags || empty($this->_tags)) {
            return false;
        }
        return in_array(self::PR_TAG, array_map(function($tag) {return $tag['name'];}, $this->_tags));
    }

    public function getPage() {
        return $this->_getPage();
    }

    private function _getPage() {
        if(!$this->_pageId) {
            return null;
        }
        return Application_Model_Mappers_PageMapper::getInstance()->find($this->_pageId);
    }
}