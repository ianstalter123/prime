<?php

class Widgets_Gal_Gal extends Widgets_Abstract {

	const DEFAULT_THUMB_SIZE = '250';

	private $_websiteHelper  = null;

	protected function  _init() {
		parent::_init();
		$this->_view = new Zend_View(array(
			'scriptPath' => dirname(__FILE__) . '/views'
		));
		$this->_websiteHelper    = Zend_Controller_Action_HelperBroker::getStaticHelper('website');
		$this->_view->websiteUrl = $this->_websiteHelper->getUrl();
        array_push($this->_cacheTags, __CLASS__);
	}

	protected function  _load() {
		if(!is_array($this->_options) || empty($this->_options) || !isset($this->_options[0]) || !$this->_options[0] || preg_match('~^\s*$~', $this->_options[0])) {
			throw new Exceptions_SeotoasterException($this->_translator->translate('You should specify folder.'));
		}

		$configHelper        = Zend_Controller_Action_HelperBroker::getStaticHelper('config');
		$path                = $this->_websiteHelper->getPath() . $this->_websiteHelper->getMedia() . $this->_options[0] . '/';

		$mediaServersAllowed = $configHelper->getConfig('mediaServers');
		unset($configHelper);
		$websiteData         = ($mediaServersAllowed) ? Zend_Registry::get('website') : null;


		$thumbSize           = isset($this->_options[1]) ? $this->_options[1] : self::DEFAULT_THUMB_SIZE;
		$useCrop             = isset($this->_options[2]) ? (boolean)$this->_options[2] : false;
		$useCaption          = isset($this->_options[3]) ? (boolean)$this->_options[3] : false;

		if(!is_dir($path)) {
			throw new Exceptions_SeotoasterException($path . ' is not a directory.');
		}

		$sourceImages = Tools_Filesystem_Tools::scanDirectory($path . 'original/');
		$galFolder    = $path . (($useCrop) ? 'crop/' : 'thumbnails/');

		if(!is_dir($galFolder)) {
			 @mkdir($galFolder);
		}

		foreach ($sourceImages as $key => $image) {
			if(is_file($galFolder . $image)) {
				$imgInfo = getimagesize($galFolder . $image);
				if($imgInfo[0] != $thumbSize) {
					Tools_Image_Tools::resize($path . 'original/' . $image, $thumbSize, !($useCrop), $galFolder, $useCrop);
				}
			}
			else {
				Tools_Image_Tools::resize($path . 'original/' . $image, $thumbSize, !($useCrop), $galFolder, $useCrop);
			}

			$sourcePart = str_replace($this->_websiteHelper->getPath(), $this->_websiteHelper->getUrl(), $galFolder);
			if($mediaServersAllowed) {
				$mediaServer     = Tools_Content_Tools::getMediaServer();
				$cleanWebsiteUrl = str_replace('www.', '', $websiteData['url']);
				$sourcePart      = str_replace($websiteData['url'], $mediaServer . '.' . $cleanWebsiteUrl, $sourcePart);
			}
			$sourceImages[$key] = array(
				'path' => $sourcePart . $image,
				'name' => $image
			);
		}


		$this->_view->folder              = $this->_options[0];
		$this->_view->original            = str_replace($this->_websiteHelper->getPath(), $this->_websiteHelper->getUrl(), $path) . 'original/';
		$this->_view->images              = $sourceImages;
		$this->_view->useCaption          = $useCaption;
		$this->_view->galFolderPath       = $galFolder;
		$this->_view->mediaServersAllowed = $mediaServersAllowed;
		$this->_view->galFolder           = str_replace($this->_websiteHelper->getPath(), $this->_websiteHelper->getUrl(), $galFolder);
		return $this->_view->render('gallery.phtml');
	}

	public static function getWidgetMakerContent() {
		$translator = Zend_Registry::get('Zend_Translate');
		$view       = new Zend_View(array(
			'scriptPath' => dirname(__FILE__) . '/views'
		));

		$websiteHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('website');
		$data = array(
			'title'   => $translator->translate('Image Gallery'),
			'content' => $view->render('wmcontent.phtml'),
			'icons'   => array(
				$websiteHelper->getUrl() . 'system/images/widgets/imageGallery.png',
			)
		);

		unset($view);
		unset($translator);
		return $data;
	}
}

