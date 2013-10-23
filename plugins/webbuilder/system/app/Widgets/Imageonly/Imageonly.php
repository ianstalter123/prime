<?php

class Widgets_Imageonly_Imageonly extends Widgets_WebbuilderWidget {

    const LINK_OPTION_URL     = 'external';

    const LINK_OPTION_NOTHING = 'nothing';

    const LINK_OPTION_IMAGE   = 'image';

   	protected function _load(){
        $name      = Webbuilder_Tools_Misc::toHash($this->_options[0] . __CLASS__);
        $container = Application_Model_Mappers_ContainerMapper::getInstance()->findByName($name, $this->_toasterOptions['id']);

        if($container instanceof Application_Model_Models_Container) {
            $ioData = Zend_Json::decode($container->getContent());

            if(is_array($ioData) && !empty($ioData)) {
                foreach($ioData as $key => $value) {
                    $this->_view->$key = $value;
                }
            }
        }
        $width                       = isset($this->_options[1]) ? $this->_options[1] : self::DEFAULT_THUMB_SIZE;

        $this->_view->containerName  = $name;
        $this->_view->width          = $width;
        $this->_view->mediaSubFolder = Webbuilder_Tools_Filesystem::getMediaSubFolderByWidth($width);
        $this->_view->pageId         = (end($this->_options) == 'static') ? 0 : $this->_toasterOptions['id'];

        return $this->_view->render('imageonly.phtml');
	}
}
