<?php

/**
 * List
 *
 * @author Eugene I. Nezhuta [Seotoaster Dev Team] <eugene@seotoaster.com>
 */
class Widgets_List_List extends Widgets_Abstract {

	protected function  _init() {
		parent::_init();
		$this->_view = new Zend_View(array(
			'scriptPath' => dirname(__FILE__) . '/views'
		));
		$website = Zend_Controller_Action_HelperBroker::getStaticHelper('website');
		$this->_view->websiteUrl = $website->getUrl();
        array_push($this->_cacheTags, __CLASS__);
	}

	protected function  _load() {
		$listType     = $this->_options[0];
		$rendererName = '_render' . ucfirst($listType) . 'List';
		if(method_exists($this, $rendererName)) {
			return $this->$rendererName();
		}
		throw new Exceptions_SeotoasterException('Can not render <strong>' . $listType . '</strong> list.');
	}

	private function _renderCategoriesList() {
		$this->_view->categoriesList = Application_Model_Mappers_PageMapper::getInstance()->findByParentId(0);
		$this->_view->useImage       = (isset($this->_options[1]) && $this->_options[1]) ? true : false;
		$this->_view->crop           = (isset($this->_options[2]) && $this->_options[2]) ? true : false;
		$this->_addCacheTags($this->_view->categoriesList);
		return $this->_view->render('categories.phtml');
	}

	private function _renderPagesList() {
		return (isset($this->_options[1]) && $this->_options[1] !== 'img') ? $this->_renderPagesListByCategoryName() : $this->_renderCurrentCategoryPagesList();
	}

	private function _renderCurrentCategoryPagesList() {
		$categoryName = $this->_toasterOptions['navName'];
		$this->_view->pagesList = $this->_findPagesListByCategoryName($categoryName);
		$this->_view->useImage  = (isset($this->_options[1]) && $this->_options[1]) ? true : false;
		$this->_view->crop      = (isset($this->_options[2]) && $this->_options[2]) ? true : false;
		$this->_addCacheTags($this->_view->pagesList);
		return $this->_view->render('pages.phtml');
	}

	private function _renderPagesListByCategoryName() {
		$categoryName = $this->_options[1];
		$this->_view->pagesList = $this->_findPagesListByCategoryName($categoryName);
		$this->_view->useImage  = (isset($this->_options[2]) && $this->_options[2]) ? true : false;
		$this->_view->crop      = (isset($this->_options[3]) && $this->_options[3]) ? true : false;
		$this->_addCacheTags($this->_view->pagesList);
		return $this->_view->render('pages.phtml');
	}

	private function _findPagesListByCategoryName($categoryName) {
        $pageMapper = Application_Model_Mappers_PageMapper::getInstance();
		$page       = $pageMapper->findByNavName($categoryName);
		if(!$page instanceof Application_Model_Models_Page) {
			throw new Exceptions_SeotoasterWidgetException('There is no category with such name: ' . $categoryName);
		}
		return Application_Model_Mappers_PageMapper::getInstance()->findByParentId(($page->getParentId() > 0) ? $page->getParentId() : $page->getId());
	}

	private function _addCacheTags($pagesList){
		if (is_array($pagesList) && !empty($pagesList)){
			foreach ($pagesList as $page) {
				array_push($this->_cacheTags, 'pageid_'.$page->getId());
			}
		}
	}

	public static function getAllowedOptions() {
		$translator = Zend_Registry::get('Zend_Translate');
		return array(
			array(
				'alias'  => $translator->translate('List all categories'),
				'option' => 'list:categories'
			),
			array(
				'alias'  => $translator->translate('List all categories (with images)'),
				'option' => 'list:categories:img'
			),
			array(
				'alias'  => $translator->translate('List all pages for current category'),
				'option' => 'list:pages'
			),
			array(
				'alias'  => $translator->translate('List all pages for current category (with images)'),
				'option' => 'list:pages:img'
			),
			array(
				'alias'  => $translator->translate('List all pages for category'),
				'option' => 'list:pages:category_name'
			),
			array(
				'alias'  => $translator->translate('List all pages for category (with images)'),
				'option' => 'list:pages:category_name:img'
			)
		);

		//return array('list:categories', 'list:categories:img', 'list:pages', 'list:pages:img', 'list:pages:category_name', 'list:pages:category_name:img');
	}
}

