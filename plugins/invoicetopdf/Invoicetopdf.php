<?php

include_once (dirname(__FILE__) . '/system/library/mpdf/mpdf.php');

class Invoicetopdf extends Tools_Plugins_Abstract {

    const RESOURCE_STORE_MANAGEMENT = 'storemanagement';
    const ROLE_CUSTOMER = 'customer';
    
    protected function _init() {
		$this->_view->setScriptPath(__DIR__ . '/system/views/');
	}

	/**
	 * Main entry point
	 *
	 * @param array $requestedParams
	 * @return mixed $dispatcherResult
	 */
	public function run($requestedParams = array()) {
		$dispatcherResult = parent::run($requestedParams);
		return ($dispatcherResult) ? $dispatcherResult : '';
	}
    
    public function  __construct($options, $seotoasterData) {
		parent::__construct($options, $seotoasterData);
        $this->_view->setScriptPath(dirname(__FILE__) . '/system/views/');
        $this->_websiteConfig	= Zend_Registry::get('website');
        $this->_uploader = new Zend_File_Transfer_Adapter_Http();
        $this->_websiteHelper  = Zend_Controller_Action_HelperBroker::getStaticHelper('website');
        $this->_configHelper   = Zend_Controller_Action_HelperBroker::getStaticHelper('config');
        $this->_uploader->setDestination(realpath($this->_websiteConfig['path'] . $this->_websiteConfig['tmp']));
        $this->_cartStorage    = Tools_ShoppingCart::getInstance();
                                        
    }
    
    public function createPdfInvoiceAction(){
        $currentUser = $this->_sessionHelper->getCurrentUser()->getRoleId();
        $userId = $this->_sessionHelper->getCurrentUser()->getId();
        $data = $this->_request->getParams();
        $invoicetopdfSettings = Invoicetopdf_Models_Mapper_InvoicetopdfSettingsMapper::getInstance()->getConfigParams();
        if(isset($data['cartId']) && isset($invoicetopdfSettings['invoiceTemplate']) && isset($data['dwn'])){
            if(Tools_Security_Acl::isAllowed(self::RESOURCE_STORE_MANAGEMENT) || $currentUser == self::ROLE_CUSTOMER){         
                $cartId = $data['cartId'];
                $templateTable = new Application_Model_DbTable_Template;
                $where = $templateTable->getAdapter()->quoteInto('name = ?', $invoicetopdfSettings['invoiceTemplate']);
                $invoiceTemplate = Application_Model_Mappers_TemplateMapper::getInstance()->fetchAll($where);  
                $templateContent = $invoiceTemplate[0]->getContent();
                $cartSession = Models_Mapper_CartSessionMapper::getInstance()->find($cartId);
                if(!empty($cartSession)){
                    if($currentUser == self::ROLE_CUSTOMER && $cartSession->getUserId() != $userId){
                        $this->_redirector->gotoUrl($this->_websiteHelper->getUrl());
                    }
                    $this->_sessionHelper->storeCartSessionKey = $cartId;
                    $themeData = Zend_Registry::get('theme');
                    $pageMapper = Application_Model_Mappers_PageMapper::getInstance();
                    $parserOptions = array(
                        'websiteUrl'   => $this->_websiteHelper->getUrl(),
                        'websitePath'  => $this->_websiteHelper->getPath(),
                        'currentTheme' => $this->_configHelper->getConfig('currentTheme'),
                        'themePath'    => $themeData['path'],
                    );
                    $page = $pageMapper->findByUrl('index.html');
                    $page = $page->toArray();
                    $parser = new Tools_Content_Parser($templateContent, $page, $parserOptions);
                    $content = $parser->parse();
                    $this->_cartStorage->clean();

                    $pathTompdfTmpFolder = dirname(__FILE__) .DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'mpdf'.DIRECTORY_SEPARATOR.'tmp';
                    $pdfFile = new mPDF('utf-8','A4');
                    $pdfFile->WriteHTML($content);
                    $pdfFileName = 'Invoice_'.$cartId.'.pdf';
                    $pathToTmpFolder = $this->_websiteHelper->getPath() . $this->_websiteHelper->getTmp();
                    $pdfFile->Output($pathToTmpFolder.$pdfFileName, 'F');
                    if($data['dwn'] == 1){
                        header("Content-Description: File Transfer");
                        header("Content-Disposition: attachment; filename=$pdfFileName");
                        header("Content-type: application/pdf");
                        readfile($pathToTmpFolder.$pdfFileName);
                    }
                    if($data['dwn'] == 0){
                        $this->_redirector->gotoUrl($this->_websiteHelper->getUrl().'tmp/'.$pdfFileName);
                    }
                }
            }
         
        }
    }
    
    public function _makeOptionInvoiceNumber(){
        if(isset($this->_sessionHelper->storeCartSessionKey)){
            return $this->_sessionHelper->storeCartSessionKey;
        }
    }
    
    public function _makeOptionCreated(){
        if(isset($this->_sessionHelper->storeCartSessionKey)){
            $cartId = $this->_sessionHelper->storeCartSessionKey;
            if(Tools_Security_Acl::isAllowed(self::RESOURCE_STORE_MANAGEMENT) || $currentUser == self::ROLE_CUSTOMER){  
                $cartSession = Models_Mapper_CartSessionMapper::getInstance()->find($cartId);
                if(!empty($cartSession)){
                    return date("d-M-Y", strtotime($cartSession->getCreatedAt()));
                }
            }
        }
    }
    
    public function configAction(){
        if(Tools_Security_Acl::isAllowed(Tools_Security_Acl::RESOURCE_PLUGINS)){
            $invoicetopdfSettingsMapper = Invoicetopdf_Models_Mapper_InvoicetopdfSettingsMapper::getInstance();
            if($this->_request->isPost()){
                $configParams = $this->_request->getParams();
                $invoicetopdfSettingsMapper->save($configParams);
            }else{
                $invoiceTemplates = Application_Model_Mappers_TemplateMapper::getInstance()->findByType('typeinvoice');
                $invoicetopdfSettings = $invoicetopdfSettingsMapper->getConfigParams();
                $this->_view->settings = $invoicetopdfSettings;
                $this->_view->invoiceTemplates = $invoiceTemplates;
                $this->_view->translator = $this->_translator;
                echo $this->_view->render('invoiceConfig.phtml');
            }
            
        }
    }
    
    public static function getEcommerceConfigTab() {
        $translator = Zend_Controller_Action_HelperBroker::getStaticHelper('language');
        return array(
            'title'      => $translator->translate('Invoice'),
            'contentUrl' =>  Zend_Controller_Action_HelperBroker::getStaticHelper('website')->getUrl() . 'plugin/invoicetopdf/run/config/'
        );
    }
       
}

