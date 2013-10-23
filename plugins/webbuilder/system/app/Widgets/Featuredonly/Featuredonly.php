<?php

class Widgets_Featuredonly_Featuredonly extends Widgets_WebbuilderWidget {

   	const FA_WIDGET_TEMPLATE           = '{$featured:area:%s}';

    const FA_DEFAULT_MAX_PAGES_COUNT   = 5;

    const FA_DEFAULT_DESCRIPTION_LIMIT = 250;

    protected function _load(){
        // get container by its name
        $name      = Webbuilder_Tools_Misc::toHash($this->_options[0] . __CLASS__);
        $container = Application_Model_Mappers_ContainerMapper::getInstance()->findByName($name, $this->_toasterOptions['id']);

        // assign view variables
        $this->_view->container = $name;
        $this->_view->pageId    = (end($this->_options) == 'static') ? 0 : $this->_toasterOptions['id'];
        $this->_view->content   = ($container instanceof Application_Model_Models_Container) ? sprintf(self::FA_WIDGET_TEMPLATE, $container->getContent()) : '';
        return $this->_view->render('featuredonly.phtml');
	}

		
}
