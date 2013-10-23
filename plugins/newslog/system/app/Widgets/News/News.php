    <?php
/**
 * News
 *
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/26/12
 * Time: 12:30 PM
 */
class Widgets_News_News extends Widgets_Abstract {

    const ORDER_ASC       = 'asc';

    const ORDER_DESC      = 'desc';

    const USE_IMAGE       = 'img';

    protected $_cacheable = false;

    /**
     * If true widget will also put a record to teh error log file
     *
     * @var bool
     */
    private $_debugMode         = false;

    /**
     * @var Simpleblog_Models_Mapper_PostMapper
     */
    protected $_mapper          = null;

    protected $_websiteHelper   = null;

    protected function _init() {
        $this->_view             = new Zend_View(array('scriptPath' => __DIR__ . '/views'));
        $this->_view->setHelperPath(APPLICATION_PATH . '/views/helpers/');
        $this->_view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');

        $this->_websiteHelper    = Zend_Controller_Action_HelperBroker::getStaticHelper('website');
        $this->_view->websiteUrl = $this->_websiteHelper->getUrl();
        $this->_mapper           = Newslog_Models_Mapper_NewsMapper::getInstance();
        $this->_debugMode        = Tools_System_Tools::debugMode();

        //check the first option if it's integer we assume the page id is passed
        if(isset($this->_options[0]) && intval($this->_options[0])) {
            $this->_toasterOptions['id'] = array_shift($this->_options);
        }
    }

    protected function _load() {
        //backward compatibility fix :( This will be removed in next versions of the plugin
        if(isset($this->_options[0]) && $this->_options[0] == 'list') {
            return $this->_renderNewsList();
        }

        if(empty($this->_options)) {
            throw new Exceptions_SimpleblogException('Not enough parameters passed!');
        }
        $option   = strtolower(array_shift($this->_options));
        $renderer = '_render' . ucfirst($option);
        if(method_exists($this, $renderer)) {
            return $this->$renderer();
        }
        return $this->_renderOption($option);
    }

    protected function _renderPreview() {
        $preview = null;
        $page    = $this->_invokeNewsItem()->getPage();
        if($page instanceof Application_Model_Models_Page) {
            $preview = $page->getPreviewImage();
        }
        if(!$preview || !file_exists($this->_websiteHelper->getPreview() . $preview)) {
            return $this->_websiteHelper->getUrl() . 'system/images/noimage.png';
        }
        return  $this->_websiteHelper->getUrl() . $this->_websiteHelper->getPreview() . $preview;
    }

    protected function _renderActions() {
        if(!Tools_Security_Acl::isAllowed(Tools_Security_Acl::RESOURCE_CONTENT)) {
            return '';
        }
        $this->_view->newsId = $this->_invokeNewsItem()->getId();
        return $this->_view->render('actions.news.phtml');
    }

    protected function _renderUrl() {
        $folder = Newslog_Models_Mapper_ConfigurationMapper::getInstance()->fetchConfigParam('folder');
        return $this->_websiteHelper->getUrl() . (($folder) ? $folder . '/' : '') . $this->_invokeNewsItem()->getMetaDataValue('url');
    }

    protected function _renderDate() {
        $format = array_shift($this->_options);
        if(!$format) {
            $format = 'M, j Y H:m';
        }
        return $this->_translator->translate(date($format, strtotime($this->_invokeNewsItem()->getCreatedAt())));
    }

    protected function _renderGplus() {
        $gplusProfile = Newslog_Tools_Misc::getGplusProfile($this->_invokeNewsItem());
        if($gplusProfile) {
            $title = (isset($this->_options[0])) ? $this->_options[0] : $gplusProfile['name'];
            return '<a class="newslog-gplus-profile" href="' . $gplusProfile['url'] . '?rel=author" target="_blank">' . $title . '</a>';
        }
        return '';
    }

    protected function _renderTags() {
        $tags = $this->_invokeNewsItem()->getTags();
        if(!is_array($tags)) {
            return '';
        }
        $this->_view->tags       = $tags;
        $this->_view->tagsLength = sizeof($tags);
        return $this->_view->render('tags.news.phtml');
    }



    /**
     * Common mehtod to get post's values
     *
     * @param string $option
     * @return string
     */
    private function _renderOption($option) {
        $getter = 'get' . ucfirst($option);

        try {
            $newsItem = $this->_invokeNewsItem();
        } catch (Exceptions_SeotoasterPluginException $spe) {
            if($this->_debugMode) {
                error_log($spe->getMessage());
            }
            return $spe->getMessage();
        }

        if(!method_exists($newsItem, $getter)) {
            return 'News widget error: wrong option passed.';
        }
        return $newsItem->$getter();
    }

    private function _invokeNewsItem() {
        if(!isset($this->_toasterOptions['id'])) {
            throw new Exceptions_SeotoasterPluginException('News widget error: Can not determine page id.');
        }
        $page = Application_Model_Mappers_PageMapper::getInstance()->find($this->_toasterOptions['id']);
        if(!$page instanceof Application_Model_Models_Page) {
            throw new Exceptions_SeotoasterPluginException('News widget error: News page cannot be found');
        }
        if(!$page->getExtraOption(Newslog::NEWS_PAGE_OPTION) && !$page->getNews()) {
            throw new Exceptions_SeotoasterPluginException('News widget error: Page passed to the widget is not a news page');
        }
        $newsItem = $this->_mapper->findByPageId($page->getId());
        if(!$newsItem instanceof Newslog_Models_Model_News) {
            throw new Exceptions_SeotoasterPluginException('News widget error: News item connot be found');
        }
        return $newsItem;
    }

    /*
     * ================ NEXT PART OF PLUGIN IS DEPRECATED AND WILL BE REMOVED IN NEXT RELEASES ==================
     * ========================================== YOU'VE BEEN WARNED ============================================
     */

    /**
     * For old newslist widget
     *
     * @deprecated
     * @return array
     */
    protected function _parseOtions() {
        $options = array(
            'order'    => self::ORDER_DESC,
            'limit'    => null,
            'tags'     => array(),
            'useImage' => false
        );
        if(is_array($this->_options)) {
            foreach($this->_options as $option) {
                switch($option) {
                    case self::USE_IMAGE:
                        $options['useImage'] = true;
                    break;
                    case self::ORDER_ASC:
                    case self::ORDER_DESC:
                        $options['order'] = $option;
                    break;
                    default:
                        if(is_string($option) && $this->_isNewsTag($option)) {
                            array_push($options['tags'], $option);
                        } else {
                            $options['limit']= intval($option);
                        }
                    break;
                }
            }
        }
        return $options;
    }

    /**
     * News list renderer
     *
     * @deprecated
     * @return mixed
     */
    protected function _renderNewsList() {
        $options             = $this->_parseOtions();
        $this->_view->folder = Newslog_Models_Mapper_ConfigurationMapper::getInstance()->fetchConfigParam('folder');
        $this->_view->img    = $options['useImage'];
        $this->_view->news   = array_map(function($newsItem){return $newsItem->toArray();}, Newslog_Models_Mapper_NewsMapper::getInstance()->fetchAll(null,
            array('created_at ' . strtoupper($options['order'])),
            $options['limit'],
            null,
            $options['tags']
        ));
        return $this->_view->render('list.news.phtml');
    }

    /**
     * @depracated
     * @param $tagName
     * @return bool
     */
    private function _isNewsTag($tagName) {
        $validator = new Zend_Validate_Db_RecordExists(array(
            'table' => 'plugin_newslog_tag',
            'field' => 'name'
        ));
        return $validator->isValid($tagName);
    }
}
