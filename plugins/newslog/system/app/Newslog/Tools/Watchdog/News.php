<?php
/**
 * News
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/24/12
 * Time: 5:20 PM
 */
class Newslog_Tools_Watchdog_News extends Tools_System_GarbageCollector {

    // const SITEMAPNEWS_FILE = 'sitemapnews.xml';

    protected $_websiteHelper = null;

    protected function _runOnDefault() {
        $this->_websiteHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('website');
        $this->_savePageForNews($this->_object);
    }

    protected function _runOnDelete() {
        $mapper      = Application_Model_Mappers_PageMapper::getInstance();
        $result      = $mapper->delete($mapper->find($this->_object->getPageId()));

        $cacheHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('cache');
        $cacheHelper->clean(false, false, array('pageid_' . $this->_object->getPageId()));
    }

    /**
     * Create or update page for the news item
     *
     * @param Newslog_Models_Model_News $newsItem
     */
    protected function _savePageForNews($newsItem) {
        //get the metadata from news item to create a page
        $metaData      = $newsItem->getMetaData(true);
        $pageHelper    = Zend_Controller_Action_HelperBroker::getStaticHelper('page');
        // Prepare url for the news page. In addition filter the /
        $url           = str_replace('/', '', $pageHelper->filterUrl($metaData['url']));
        if(($page = Application_Model_Mappers_PageMapper::getInstance()->findByUrl((isset($metaData['oldUrl']) && $metaData['oldUrl']) ? $pageHelper->filterUrl($metaData['oldUrl']) : $url)) === null) {
            $page = new Application_Model_Models_Page();
        }
        $page->setTemplateId(isset($metaData['template']) ? $metaData['template'] : 'news')
            ->setParentId(Application_Model_Models_Page::IDCATEGORY_DEFAULT)
            ->setH1($metaData['h1'])
            ->setNavName($metaData['navName'])
            ->setHeaderTitle($metaData['title'])
            ->setUrl($url)
            ->setMetaKeywords(isset($metaData['metaKeywords']) ? $metaData['metaKeywords'] : '')
            ->setMetaDescription(isset($metaData['teaserText']) ? $metaData['teaserText'] : '')
            ->setNews(true)
            ->setShowInMenu(Application_Model_Models_Page::IN_NOMENU)
            ->setTargetedKeyPhrase($metaData['h1'])
            ->setSystem(true)
            ->setLastUpdate(date(Tools_System_Tools::DATE_MYSQL))
            ->setIs404page(false)
            ->setDraft(!$newsItem->getPublished())
            ->setTeaserText(isset($metaData['teaserText']) ? $metaData['teaserText'] : '');

        //work on 301 redirect if this page is not new
        if(isset($metaData['oldUrl']) && $metaData['oldUrl']) {
            $sessionHelper             = Zend_Controller_Action_HelperBroker::getStaticHelper('session');
            $sessionHelper->oldPageUrl = $pageHelper->filterUrl($metaData['oldUrl']);
            $page->registerObserver(new Tools_Seo_Watchdog());
        }

        $page = Application_Model_Mappers_PageMapper::getInstance()->save($page);
        if($page) {
            $page->notifyObservers();

            //proccess preview image
            if(isset($metaData['image']) && $metaData['image']) {
                $tmpImage      = $this->_websiteHelper->getPath() . ltrim(str_replace($this->_websiteHelper->getUrl(), '', $metaData['image']));
                $miscConfig    = Zend_Registry::get('misc');
                $savePath      = $this->_websiteHelper->getPath() . $this->_websiteHelper->getPreview();
                $existingFiles = preg_grep('~^'. $pageHelper->clean($url) .'\.(png|jpg|gif)$~i', Tools_Filesystem_Tools::scanDirectory($savePath, false, false));
                if(!empty($existingFiles)){
                    foreach ($existingFiles as $file) {
                        if($savePath . $file != $tmpImage) {
                            Tools_Filesystem_Tools::deleteFile($savePath . $file);
                        }
                    }
                }

                $pagePreviewImg = $savePath . $pageHelper->clean($url) . '.' . pathinfo($tmpImage, PATHINFO_EXTENSION);
                if (is_file($tmpImage) && copy($tmpImage, $pagePreviewImg)) {
                    Tools_Image_Tools::resize($pagePreviewImg, $miscConfig['pageTeaserSize'], true, null, true);
                }
                //pathinfo($pagePreviewImg, PATHINFO_BASENAME
                $pathExploded = explode('/', $pagePreviewImg);
                $page->setPreviewImage(end($pathExploded));
                $page = Application_Model_Mappers_PageMapper::getInstance()->save($page);
            }

            $newsItem->setPageId($page->getId());
            $newsItem->setMetaData(array(
                'h1'           => $page->getH1(),
                'title'        => $page->getHeaderTitle(),
                'navName'      => $page->getNavName(),
                'url'          => $page->getUrl(),
                'teaserText'   => $page->getTeaserText(),
                'metaKeywords' => $page->getMetaKeywords(),
                'template'     => $page->getTemplateId(),
                'image'        => $page->getPreviewImage()
            ));
            $newsItem->removeObserver(new Newslog_Tools_Watchdog_News());
            Newslog_Models_Mapper_NewsMapper::getInstance()->save($newsItem);
        }

    }
}
