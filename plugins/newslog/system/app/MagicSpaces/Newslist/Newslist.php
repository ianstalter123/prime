<?php
/**
 * Newslist magic space. Allows to output news list with custom layout
 *
 * Optionally magicspace can receive options, they are:
 * 1. 'asc'/'desc' - list order direction
 * 2. N - here N is an integer value which tell the magic space how many news items you want to see in results
 * You can pass option in random order
 */
class MagicSpaces_Newslist_Newslist extends Tools_MagicSpaces_Abstract {

    /**
     * Widget name that will be used in the replacement
     *
     */
    const NEWS_WIDGET_NAME    = 'news';

    /**
     * News list order direction. Descending by default.
     *
     * @var string
     */
    protected $_orderDirection = 'DESC';

    /**
     * How many news items should be in news list. Leave null to get everything.
     *
     * @var mixed integer|null
     */
    protected $_limit         = null;

    /**
     * Main magic space entry point
     *
     * 1. Get the html version of the current page's template
     * 2. Modify news widget by adding the appropriate page id to each of its occurrences and the parse it
     * 3.
     * 4. Output the news list and ..... wait for it ...... profit!
     *
     * @return null|string
     */
    protected function _run() {
        $tmpContent     = $this->_content;
        $this->_content = $this->_getCurrentTemplateContent();
        $spaceContent   = $this->_parse();
        $this->_content = $tmpContent;
        $content        = '';

        if(!$spaceContent) {
            $spaceContent = $this->_parse();
        }

        $this->_parseParams();
        $news = Newslog_Models_Mapper_NewsMapper::getInstance()->fetchAll(null, array('created_at ' . $this->_orderDirection), $this->_limit, 0);

        if(!is_array($news) || empty($news)) {
            return 'You don\'t have news yet.';
        }

        $this->_spaceContent = $spaceContent;
        foreach($news as $newsItem) {
            if(Tools_Security_Acl::isAllowed(Tools_Security_Acl::RESOURCE_ADMINPANEL)) {
                $spaceContent = '<input type="hidden" class="news-list-hidden news-list-hidden-' . $newsItem->getId() . '" />' . $this->_spaceContent;
            }
            $content     .= preg_replace('~{\$' . self::NEWS_WIDGET_NAME . ':(.+)}~uU', '{$' . self::NEWS_WIDGET_NAME . ':' . $newsItem->getPageId() . ':$1}', $spaceContent);
        }
        $parser = new Tools_Content_Parser($content, array());
        return  $parser->parseSimple();
    }

    /**
     * Parse magic space parameters $_params and init appropriate properties
     *
     */
    private function _parseParams() {
        if(!is_array($this->_params)) {
            return false;
        }
        foreach($this->_params as $param) {
            $param = strtolower($param);
            if(is_string($param) && ($param == 'asc' || $param == 'desc')) {
                $this->_orderDirection = $param;
                continue;
            }
            $this->_limit = intval($param);
        }
    }

    /**
     * Get the html (not parsed) version of the current template
     *
     * @return bool|string
     */
    private function _getCurrentTemplateContent() {
        $page    = Application_Model_Mappers_PageMapper::getInstance()->find($this->_toasterData['id']);
        $tempate = Application_Model_Mappers_TemplateMapper::getInstance()->find($page->getTemplateId());
        if(!$tempate instanceof Application_Model_Models_Template) {
            return false;
        }
        return $tempate->getContent();
    }
}
