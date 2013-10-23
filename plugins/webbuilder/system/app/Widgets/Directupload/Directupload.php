<?php

class Widgets_Directupload_Directupload extends Widgets_WebbuilderWidget {

   	protected function _load() {
        // required parameters
        if(!isset($this->_options[0]) || !isset($this->_options[1]) || !isset($this->_options[2])) {
            $this->_error('Not enough parameters. See readme.txt for usage');
        }

        $folder        = $this->_filter(filter_var($this->_options[0], FILTER_SANITIZE_STRING), '-');
        $imageName     = $this->_filter(filter_var($this->_options[1], FILTER_SANITIZE_STRING));
        $containerName = Webbuilder_Tools_Misc::toHash($imageName . __CLASS__);
        if(end($this->_options) != 'static') {
            $containerName .= '_' . $this->_toasterOptions['id'];
        }

        $width              = filter_var($this->_options[2], FILTER_SANITIZE_NUMBER_INT);
        $mediaSubfolder     = $this->_getMediaSubfolder($width);
        $mediaSubfolderPath = realpath($this->_websiteHelper->getPath() . 'media' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $mediaSubfolder . DIRECTORY_SEPARATOR);

        if($mediaSubfolderPath) {
            $previews = Tools_Filesystem_Tools::findFilesByExtension($mediaSubfolderPath, 'jpg|png|jpeg|gif', true, true, true);

            $mapper    = Application_Model_Mappers_ContainerMapper::getInstance();
            $container = $mapper->findByName($containerName);

            if(empty($previews) || (!$container instanceof  Application_Model_Models_Container)) {
                $imageExists = false;
            } else {
                $imageExists = true;
                if(array_key_exists($containerName, $previews)) {
                    $previewImage = $this->_websiteUrl . 'media' . '/' . $folder . '/' . $mediaSubfolder . '/' . $container->getContent();
                }
            }
        }

        // assign view variables
        $this->_view->pageId         = (end($this->_options) != 'static') ? $this->_toasterOptions['id'] : 0;
        $this->_view->galleryRel     = isset($this->_options[3]) ? $this->_filter($this->_options[3]) : '';
        $this->_view->isIE           = (strpos($_SERVER['HTTP_USER_AGENT'], '(compatible; MSIE ') !== false);
        $this->_view->width          = $width;
        $this->_view->container      = $containerName;
        $this->_view->imageName      = $imageName;
        $this->_view->folder         = $folder;
        $this->_view->mediaSubfolder = $mediaSubfolder;
        $this->_view->image          = isset($previewImage) ? $previewImage : $this->_websiteUrl . 'system/images/noimage.png';
        $this->_view->imageExists    = (isset($imageExists) && $imageExists);

        if(isset($previewImage)) {
            $this->_view->originalImage = str_replace($mediaSubfolder, 'original', $previewImage);
        }

        return $this->_view->render('directupload.phtml');
	}

    /**
     * Filter the given value using the [^\w]+|[\s\-]+~ui pattern and replace all not valid chars with the $replacement
     *
     * @param string $value
     * @param string $replacement
     * @return string
     */
    private function _filter($value, $replacement = '') {
        $filter = new Zend_Filter_PregReplace();
        $value  = $filter->setMatchPattern('~[^\w]+|[\s\-]+~ui')
            ->setReplacement($replacement)
            ->filter(trim($value));
        return $value;
    }

    /**
     * Get the proper media sub-folder from option or based on the image width
     *
     * @param integer $width
     * @return string
     */
    private function _getMediaSubfolder($width = self::DEFAULT_THUMB_SIZE) {
        if(isset($this->_options[4]) && $this->_options[4] && in_array($this->_options[4], Tools_Image_Tools::$imgResizedFolders)) {
            return $this->_options[4];
        }
        return Webbuilder_Tools_Filesystem::getMediaSubFolderByWidth($width);
    }
}
