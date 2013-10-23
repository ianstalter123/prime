<?php
class MagicSpaces_Dashboardmenu_Dashboardmenu extends Tools_MagicSpaces_Abstract {

	protected function _run() {
		
        $content = $this->_spaceContent;
        $nameOfHtml = explode("\n",$content);
        $websiteUrl = Zend_Controller_Action_HelperBroker::getStaticHelper('website')->getUrl();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $requestUri = $request->getRequestUri();
        $pageNavName = preg_replace('~\/~', '', $requestUri);
        if($pageNavName != 'dashboard' && $pageNavName != ''){
            $pageNavName = preg_replace('~dashboard~', '', $pageNavName);
        }
        if(isset($nameOfHtml) && !empty($nameOfHtml)){
            $preparedLink = '<ul id="dashboard-list">';
            foreach($nameOfHtml as $htmlFile){
                $htmlFile = trim($htmlFile);
                if($htmlFile !='' && $htmlFile != 'index'){
                    if($pageNavName == $htmlFile){
                        $preparedLink .= '<li><a href="'.$websiteUrl.'dashboard/'.$htmlFile.'/" class="current dashboard-link-'.$htmlFile.'">'.$htmlFile.'</a></li>';
                    }else{
                        $preparedLink .= '<li><a href="'.$websiteUrl.'dashboard/'.$htmlFile.'/" class="dashboard-link-'.$htmlFile.'">'.$htmlFile.'</a></li>';
                    }
                }
                if($htmlFile !='' && $htmlFile == 'index'){
                    if($pageNavName == 'dashboard'){
                        $preparedLink .= '<li><a href="'.$websiteUrl.'dashboard/" class="current dashboard-link-'.$htmlFile.'">'.$htmlFile.'</a></li>';
                    }else{
                        $preparedLink .= '<li><a href="'.$websiteUrl.'dashboard/" class="dashboard-link-'.$htmlFile.'">'.$htmlFile.'</a></li>';
                    }
                }
                
            }
            $preparedLink .= '</ul>';
        }
        $this->_spaceContent = $preparedLink;
        return $this->_spaceContent;
	}

}