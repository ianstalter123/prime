<?php

class Dashboard extends Tools_Plugins_Abstract {

    const RESOURCE_DASHBOARD_SHOPPING = 'dashboard_shopping';
    const ROLE_SALESPERSON = 'sales person';
      
    const CSS_NAME = 'dash.css';
    const DEPLIST = 'deplist.txt';
    
	public function  __construct($options, $seotoasterData) {
		parent::__construct($options, $seotoasterData);
        $this->_view->setScriptPath(dirname(__FILE__) . '/system/views/');
        $this->_websiteConfig	= Zend_Registry::get('website');
        $this->_uploader = new Zend_File_Transfer_Adapter_Http();
        $this->_websiteHelper  = Zend_Controller_Action_HelperBroker::getStaticHelper('website');
        $this->_configHelper   = Zend_Controller_Action_HelperBroker::getStaticHelper('config');
        $this->_uploader->setDestination(realpath($this->_websiteConfig['path'] . $this->_websiteConfig['tmp']));
                                        
    }
	public function run($requestedParams = array()) {
        $this->_requestedParams = $requestedParams;
		$dispatchersResult      = parent::run($requestedParams);
               
    }
    
    public function initializeAction(){
        $acl = Zend_Registry::get('acl');
        if(!$acl->has(self::RESOURCE_DASHBOARD_SHOPPING)) {
            $acl->addResource(new Zend_Acl_Resource(self::RESOURCE_DASHBOARD_SHOPPING));
        }
        $acl->allow(self::ROLE_SALESPERSON, self::RESOURCE_DASHBOARD_SHOPPING);
        $acl->allow(Tools_Security_Acl::ROLE_ADMIN, self::RESOURCE_DASHBOARD_SHOPPING);
        $acl->allow(Tools_Security_Acl::ROLE_SUPERADMIN, self::RESOURCE_DASHBOARD_SHOPPING);
        Zend_Registry::set('acl', $acl);
      
        if(Tools_Security_Acl::isAllowed(self::RESOURCE_DASHBOARD_SHOPPING)){
          $nameOfHtmlFile = $this->_request->getParams();
          $pageMapper = Application_Model_Mappers_PageMapper::getInstance();
          $pageModel = new Application_Model_Models_Page();
          $nameOfDashboardPage = 'dashboarddash'.md5('dashboarddash');
          $dashboardPage = $pageMapper->findByUrl($nameOfDashboardPage.'.html');
          if($dashboardPage == null ){
             $pageModel->setTemplateId('default');
             $pageModel->setParentId('-3');
             $pageModel->setH1('dashboard');
             $pageModel->setHeaderTitle('dashboard');
             $pageModel->setUrl($nameOfDashboardPage.'.html');
             $pageModel->setNavName('dashboard');
             $pageModel->setMetaDescription('');
             $pageModel->setMetaKeywords('');
             $pageModel->setTeaserText('');
             $pageModel->setShowInMenu('0');
             $pageModel->setIs404page('0');
             $pageModel->setProtected('0');
             $pageModel->setMemLanding('0');
             $pageModel->setSignupLanding('0');
             $pageModel->setErrLoginLanding('0');
             $pageModel->setOrder('0');
             $pageModel->setSiloId('0');
             $pageModel->setTargetedKeyPhrase('dashboard');
             $pageModel->setSystem('0');
             $pageModel->setDraft('0');
             $pageModel->setNews('0');
             $pageModel->setPublishAt('');
             $pageMapper->save($pageModel);
         }
         $dashboardPage = $pageMapper->findByUrl($nameOfDashboardPage.'.html');
         $layout = Zend_Layout::getMvcInstance();
         
         if($nameOfHtmlFile['page'] == 'index'){
            $dashboardThemeMapper  = Dashboard_Models_Mapper_DashboardThemeMapper::getInstance();
            $dashboardThemeContent = $dashboardThemeMapper->getThemeContent();
            if(isset($dashboardThemeContent['index.html'])){
                $themeHtmlFiles = $dashboardThemeContent['index.html'];
                $themeData = Zend_Registry::get('theme');
                $parserOptions = array(
                    'websiteUrl'   => $this->_websiteHelper->getUrl(),
                    'websitePath'  => $this->_websiteHelper->getPath(),
                    'currentTheme' => $this->_configHelper->getConfig('currentTheme'),
                    'themePath'    => $themeData['path'],
                );
                $page        = $dashboardPage->toArray();
                $parser      = new Tools_Content_Parser($dashboardThemeContent['index.html'], $page, $parserOptions);
                $pageContent = $parser->parse();
                $this->_view->pageContent = $pageContent;
                $layout->content = $pageContent;
                $this->_view->layout = $layout;
                echo $this->_view->render('dashboard.phtml');
            }
        }
        else{
           $nameOfHtmlFile = htmlspecialchars(filter_var($this->_request->getParam('page'), FILTER_SANITIZE_MAGIC_QUOTES));
           $htmlFile = $nameOfHtmlFile.'.html';
           $dashboardThemeMapper  = Dashboard_Models_Mapper_DashboardThemeMapper::getInstance();
           $content = $dashboardThemeMapper->getHtmlContent($htmlFile);
           if(!empty($content)){
               $themeData = Zend_Registry::get('theme');
               $parserOptions = array(
                    'websiteUrl'   => $this->_websiteHelper->getUrl(),
                    'websitePath'  => $this->_websiteHelper->getPath(),
                    'currentTheme' => $this->_configHelper->getConfig('currentTheme'),
                    'themePath'    => $themeData['path'],
               );
               $page        = $dashboardPage->toArray();
               $parser      = new Tools_Content_Parser($content, $page, $parserOptions);
               $pageContent = $parser->parse();
               $this->_view->pageContent = $pageContent;
               $layout->content = $pageContent;
               $this->_view->layout = $layout;
               echo $this->_view->render('dashboard.phtml');
           }
        }
        
      }else{
          $this->_redirector->gotoUrl(Zend_Controller_Action_HelperBroker::getStaticHelper('website')->getUrl());
      }
    }
        
    public function changeThemeAction(){
        if(Tools_Security_Acl::isAllowed(Tools_Security_Acl::RESOURCE_PLUGINS)){
            $dashboardThemeMapper  = Dashboard_Models_Mapper_DashboardThemeMapper::getInstance();
            $dashboardTheme = $dashboardThemeMapper->getConfigParam('themeName');
            $dashboardThemeName = 'Please upload your theme';
            if($dashboardTheme !== null){
                 $dashboardThemeName = $dashboardTheme;
            }
            $this->_view->currentDashboardTheme = $dashboardThemeName;
            $this->_view->translator = $this->_translator;
            echo $this->_view->render('uploadTheme.phtml');
        }
    }

    public static function getEcommerceConfigTab() {
        $translator = Zend_Controller_Action_HelperBroker::getStaticHelper('language');
        return array(
            'title'      => $translator->translate('Dashboard'),
            'contentUrl' =>  Zend_Controller_Action_HelperBroker::getStaticHelper('website')->getUrl() . 'plugin/dashboard/run/changeTheme/'
        );
    }

    public function uploadThemeAction(){
        if(Tools_Security_Acl::isAllowed(Tools_Security_Acl::RESOURCE_PLUGINS)){
            $this->_uploader = new Zend_File_Transfer_Adapter_Http();
            $this->_uploader->addValidator('Extension', false, 'zip')->addValidator(new Validators_MimeType(array('application/zip')), false);
            $themeArchive = $this->_uploader->getFileInfo();
            if (!$this->_uploader->isValid()){
                echo json_encode(array('error'=>'1', 'data'=>'error'));
                return;
            }
            if (!extension_loaded('zip')){
                echo json_encode(array('error'=>'1', 'data'=>'No zip extension loaded'));
                return;
            }
            $tmpFolder = $this->_uploader->getDestination();
            $zip       = new ZipArchive();
            $zip->open($themeArchive['file']['tmp_name']);
            $unzipped = $zip->extractTo($tmpFolder);
            if($unzipped !== true){
                echo json_encode(array('error'=>'1', 'data'=>'Can\'t extract zip file to tmp directory'));
                return;
            }
            $themeName = str_replace('.zip', '', $themeArchive['file']['name']);
            $dashboardThemeMapper = Dashboard_Models_Mapper_DashboardThemeMapper::getInstance();
            $dashboardThemeContent = $dashboardThemeMapper->getThemeContent();
            $previousThemeName = '';
            if(isset($dashboardThemeContent['themeName'])){
                $previousThemeName = $dashboardThemeContent['themeName'];
            }
            $isValid = $this->_validateTheme($themeName);
            if(true === $isValid ) {
                $destinationDir = $this->_websiteConfig['path'].'plugins/dashboard/web/themes/';
                if($previousThemeName != ''){
                    if (is_dir($destinationDir.$previousThemeName)){
                        Tools_Filesystem_Tools::deleteDir($destinationDir.$previousThemeName);
                    }
                }
                if($zip->extractTo($destinationDir) === false){
                    echo json_encode(array('error'=>'1', 'data'=>'Can\'t extract zip file to themes directory'));
                    return;
                }
                $zip->close();
                Tools_Filesystem_Tools::deleteDir($tmpFolder.'/'.$themeName);
                echo json_encode(array('error'=>'0', 'data'=>'Theme uploaded'));
                
            } else {
                $zip->close();
                echo json_encode(array('error'=>'1', 'data'=>$isValid));
            }
        }

    }
    
    private function _validateTheme($themeName){
		$tmpPath = $this->_uploader->getDestination();
		$themeFolder = realpath($tmpPath.'/'.$themeName);
		if ($themeFolder === false) {
			return 'Theme directory don\'t match the archive name.';
		}
        if (!is_dir($themeFolder)) {
            return 'Can not create folder for unpack zip file. 0peration not permitted.';
        }

        $listFiles = Tools_Filesystem_Tools::scanDirectory($themeFolder);
        if (empty($listFiles)) {
            return 'Your theme directory is empty.';
        }
        if (!preg_match("/^[a-zA-Z-0-9]{1,255}$/", $themeName)) {
            return 'Theme name is invalid. Only letters, digits and dashes allowed.';
        }
        if (!file_exists($themeFolder . '/index.html')) {
            return 'File "index.html" doesn\'t exists.';
        }
        if (!in_array(self::DEPLIST, $listFiles)) {
            return 'File'.self::DEPLIST.'doesn\'t exists.';
        }
        $themeDatabaseArray = array();
        $themeFilesHtml = array();
        foreach($listFiles as $file){
            if(preg_match("/\.html|.txt/",$file)){
                $fileContent = file_get_contents($themeFolder.'/'.$file);
                $fileContent = preg_replace('/{website:url}/', '{$website:url}', $fileContent);
                $fileContent = preg_replace('/{theme:name}/', $themeName, $fileContent);
                $themeDatabaseArray[$file] = $fileContent;
            }
            if(preg_match("/\.html/",$file)){
                array_push($themeFilesHtml, $file);
            }
        }
        if(!empty($themeDatabaseArray)){
            $themeDatabaseArray['themeName'] = $themeName;
            $themeDatabaseArray['themeHtml'] = serialize($themeFilesHtml);
            $dashboardThemeMapper = Dashboard_Models_Mapper_DashboardThemeMapper::getInstance();
            $listOfExcistingFiles = $dashboardThemeMapper->getHtmlContent('themeHtml');
            if(!empty($listOfExcistingFiles)){
                $listOfExcistingFiles = unserialize($listOfExcistingFiles);
                foreach($listOfExcistingFiles as $someFile){
                    $dashboardThemeMapper->deleteHtmlFile($someFile);
                }
            }
            $dashboardThemeMapper->save($themeDatabaseArray);
        }
        return true;
    }
    
  	   
}

