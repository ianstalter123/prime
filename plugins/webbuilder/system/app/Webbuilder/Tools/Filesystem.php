<?php

class Webbuilder_Tools_Filesystem {

    public static function getMediaSubFolderByWidth($width) {
        $config = Zend_Controller_Action_HelperBroker::getStaticHelper('config')->getConfig();

        if($width <= $config['imgSmall'])  return 'small';
        if($width <= $config['imgMedium']) return 'medium';
        if($width <= $config['imgLarge'])  return 'large';

        return 'original';
    }

}