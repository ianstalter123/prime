<?php
/**
 * Seotoaster 2.0 newslog plugin.
 *
 * @todo Add more comments
 * @author Seotoaster core team <core.team@seotoaster.com>
 */

class Newslog extends Tools_Plugins_Abstract {

    const VIEWS_POSTFIX     = '.newslog.phtml';

    const ROUTE_NAME        = 'newslogRoute';

    const ROUTE_INDEX_NAME  = 'newslogIndexRoute';

    const DEFAULT_INDEX     = 'index.html';

    const PAGE_OPTION       = 'option_newsindex';

    const NEWS_PAGE_OPTION  = 'option_news_page';

    const TEMPLATE_TYPE     = 'type_news';

    private $_layout        = null;

    /**
     * Newslog preferences
     *
     * @var array
     */
    private $_newslogConfig = array();

    /**
     * Instance of the Application_Model_Models_Page with 'option_newsindex' option
     *
     * @var Application_Model_Models_Page
     */
    private $_newsIndexPage = null;


    private $_currentPageId = 0;

	/**
	 * List of action that should be allowed to specific roles
	 *
	 * By default all actions of your plugin are available to the guest user
	 * @var array
	 */
	protected $_securedActions = array(
		Tools_Security_Acl::ROLE_SUPERADMIN => array('news', 'configuration'),
        Tools_Security_Acl::ROLE_ADMIN      => array('news'),
        Tools_Security_Acl::ROLE_USER       => array('news')
	);

	protected function _init() {
        $this->_layout = new Zend_Layout();
        $this->_layout->setLayoutPath(__DIR__ . '/system/views/');

        if(($scriptPaths = Zend_Layout::getMvcInstance()->getView()->getScriptPaths()) !== false) {
            $this->_view->setScriptPath($scriptPaths);
        }
		$this->_view->addScriptPath(__DIR__ . '/system/views/');

        $this->_newslogConfig = Newslog_Models_Mapper_ConfigurationMapper::getInstance()->fetchConfigParams();
        // fix news folder path
        $this->_newslogConfig['folder'] .= ($this->_newslogConfig['folder']) ? '/' : '';
        $this->_newsIndexPage = Application_Model_Mappers_PageMapper::getInstance()->fetchByOption(self::PAGE_OPTION, true);
	}

	/**
	 * Before controller hook. Solving the news specific routes
	 *
	 */
	public function beforeController() {

        //Check if news page was requested not through the news route -> redirect using valid url
        $routeName     = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
        $requestedPage = $this->_request->getParam('page');

        // if current route is not a news page route or news index page route, check if the page is news page and do a proper redirect
        if($routeName != self::ROUTE_NAME && $routeName != self::ROUTE_INDEX_NAME) {
            $page = Application_Model_Mappers_PageMapper::getInstance()->findByUrl($requestedPage);
            if($page instanceof Application_Model_Models_Page && $this->_newslogConfig['folder']) {
                if($page->getExtraOption(self::PAGE_OPTION)) {
                    $this->_redirector->gotoUrl($this->_websiteUrl . $this->_newslogConfig['folder']);
                }
                if($page->getExtraOption(self::NEWS_PAGE_OPTION) || $page->getNews()) {
                    $this->_redirector->gotoUrl($this->_websiteUrl . $this->_newslogConfig['folder'] . $page->getUrl());
                }
            }
        } else {
            //if news page was requested correctly (through the news / news index route)
            if($routeName == self::ROUTE_INDEX_NAME || $requestedPage == self::DEFAULT_INDEX) {
                if($this->_newsIndexPage === null) {
                    return false;
                }
                $this->_request->setParam('page', $this->_newsIndexPage->getUrl());
            } else {
                $page = Application_Model_Mappers_PageMapper::getInstance()->findByUrl($requestedPage);
                if(!$page instanceof Application_Model_Models_Page) {
                    return false;
                }
                $this->_currentPageId = $page->getId();
                $this->_appendToLayout();
            }
        }
	}

    /**
     * Rewrite the 'canonical' link to include a news folder
     *
     */
    public function afterController() {
        $view         = Zend_Layout::getMvcInstance()->getView();
        $canonicalUrl = $view->canonicalUrl;
        $pageUrl      = $view->pageData['url'];

        // rewrite a canonical for a regular news page
        if($view->pageData['news'] == 1) {
            $canonicalUrl = str_replace($pageUrl, $this->_newslogConfig['folder'] . $pageUrl, $canonicalUrl);
        }
        // rewrite a canonical for news index page
        if(($this->_newsIndexPage instanceof Application_Model_Models_Page) && ($view->pageData['url'] == $this->_newsIndexPage->getUrl())) {
            $canonicalUrl = str_replace($pageUrl, $this->_newslogConfig['folder'], $canonicalUrl);
        }
        $view->canonicalUrl = $canonicalUrl;
        Zend_Layout::getMvcInstance()->setView($view);
    }

    /**
     * Before router hook
     *
     */
    public function beforeRouter() {
        // if news folder is not set then we don't need to do any routes
        if(!$this->_newslogConfig['folder']) {
            return;
        }

        $router = Zend_Controller_Front::getInstance()->getRouter();

        //add news page route
        $router->addRoute(self::ROUTE_NAME,
            new Zend_Controller_Router_Route($this->_newslogConfig['folder'] . ':page', array(
                'controller' => 'index',
                'action'     => 'index',
                'page'       => self::DEFAULT_INDEX
            ))
        );

        //add news index route
        $router->addRoute(self::ROUTE_INDEX_NAME,
            new Zend_Controller_Router_Route($this->_newslogConfig['folder'], array(
                'controller' => 'index',
                'action'     => 'index',
                'page'       => ($this->_newsIndexPage) ? $this->_newsIndexPage->getUrl() : self::DEFAULT_INDEX
            ))
        );
    }

    /**
     * Show add / edit news screen
     *
     */
    public function newsAction() {
        $this->_view->newPostForm = new Newslog_Forms_Post();
        $this->_show();
    }

    /**
     * Show newslog config screen
     *
     */
    public function preferencesAction() {
        $form        = new Newslog_Forms_Configuration();
        $authorsForm = new Newslog_Forms_Authors();

        if($this->_request->isPost()) {
            $option = $this->_request->getParam('opt');
            $form   = ($option == 'folder') ? $form : $authorsForm;

            if(!$form->isValid($this->_request->getParams())) {
                $this->_responseHelper->fail(join('<br />', $form->getMessages()));
            }

            $formData = $form->getValues();

            if($option == 'folder') {
                $this->_switchBlogOption($formData['folder']);

                // notify mojo about news folder update
                $configHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('config');
                $sambaToken   = $configHelper->getConfig('sambaToken');
                if($sambaToken) {
                    $data = array(
                        'sambaToken' => $sambaToken,
                        'folder'     => $formData['folder'],
                        'website'    => $this->_websiteUrl,
                        'hash'       => sha1($formData['folder'] . $this->_websiteUrl)
                    );
                    Api::request('put', 'news', $data);
                }
            }


            if(Newslog_Models_Mapper_ConfigurationMapper::getInstance()->save($formData)) {
                $this->_responseHelper->success($this->_translator->translate('Configuration updated'));
            }
            $this->_responseHelper->fail($this->_translator->translate('Cannot update configuration.'));
        }

        $this->_newslogConfig['folder'] = rtrim($this->_newslogConfig['folder'], '/');
        $form->populate($this->_newslogConfig);
        $authorsForm->populate($this->_newslogConfig);

        $this->_view->form        = $form;
        $this->_view->authorsForm = $authorsForm;

        $parsedUrl           = parse_url($this->_websiteUrl);
        $this->_view->domain = $parsedUrl['host'];

        $this->_show();
    }

    /**
     * Toggle news index option and news index page according to the newsfolder setting
     *
     * @param $newsFolder
     */
    private function _switchBlogOption($newsFolder) {
        $pageOptionMapper = Application_Model_Mappers_PageOptionMapper::getInstance();
        //load page news index option

        $pageOption = $pageOptionMapper->find(self::PAGE_OPTION);
        $pageOption->setActive((boolean)$newsFolder);
        $pageOptionMapper->save($pageOption);

        //news folder is empty we assume the whole site is a blog and disable new inewx page option
        if(!$newsFolder) {
            //find current news index page if exists
            if($this->_newsIndexPage instanceof Application_Model_Models_Page) {
                $this->_newsIndexPage->setExtraOptions(array(), true);
                Application_Model_Mappers_PageMapper::getInstance()->save($this->_newsIndexPage);
            }
        }
    }

    /**
     * Generates sitemapnews.xml content
     *
     * @return string
     */
    public static function getSitemapNews() {
        $view             = new Zend_View(array('scriptPath' => __DIR__ . '/system/views'));
        $view->news       = Newslog_Models_Mapper_NewsMapper::getInstance()->fetchAll();
        $view->folder     = Newslog_Models_Mapper_ConfigurationMapper::getInstance()->fetchConfigParam('folder') . '/';
        $view->language   = substr(Zend_Locale::getLocaleToTerritory(Zend_Controller_Action_HelperBroker::getStaticHelper('config')->getConfig('language')), 0, 2);
        $view->websiteUrl = Zend_Controller_Action_HelperBroker::getStaticHelper('website')->getUrl();
        return $view->render('sitemap.phtml');
    }

    /**
     * Render a proper view script
     *
     * If $screenViewScript not passed, generates view script file name automatically using the action name and VIEWS_POSTFIX
     * @param string $screenViewScript
     */
    private function _show($screenViewScript = '') {
        if(!$screenViewScript) {
            $trace  = debug_backtrace(false);
            $screenViewScript = str_ireplace('Action', self::VIEWS_POSTFIX, $trace[1]['function']);
        }
        $this->_layout->content = $this->_view->render($screenViewScript);
        echo $this->_layout->render();
    }

    /**
     * Append some js and content to the layout
     *
     */
    private function _appendToLayout() {
        //Zend_Layout::getMvcInstance()->getView()->placeholder('logoSource')->set('http://backbonejs.org/docs/images/backbone.png');
        $newsPost                = Newslog_Models_Mapper_NewsMapper::getInstance()->findByPageId($this->_currentPageId);
        if(!$newsPost) {
            return false;
        }
        $this->_view->newsPage   = true;
        $this->_view->newsPostId = $newsPost->getId();
        $this->_view->newsFolder = $this->_newslogConfig['folder'];
        $this->_view->isPR       = $newsPost->isPressRelease();

        $this->_injectContent($this->_view->render('inject.newslog.phtml'));
    }
}
