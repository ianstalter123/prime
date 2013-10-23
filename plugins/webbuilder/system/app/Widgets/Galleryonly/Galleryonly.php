<?php

class Widgets_Galleryonly_Galleryonly extends Widgets_WebbuilderWidget {

    const GALLERY_WIDGET_TEMPLATE = '{$gal:%s}';

   	protected function _load(){

        // get container by its name
        $name      = Webbuilder_Tools_Misc::toHash($this->_options[0] . __CLASS__);
        $container = Application_Model_Mappers_ContainerMapper::getInstance()->findByName($name, $this->_toasterOptions['id']);

        // assign view variables
        $this->_view->container = $name;
        $this->_view->pageId    = (end($this->_options) == 'static') ? 0 : $this->_toasterOptions['id'];
        $this->_view->content   = ($container instanceof Application_Model_Models_Container) ? sprintf(self::GALLERY_WIDGET_TEMPLATE, $container->getContent()) : '';

        return $this->_view->render('galleryonly.phtml');
    }
}
