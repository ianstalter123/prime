<?php
/**
 * Seotoaster page observer
 *
 * Watching for the news index page and updates it's "news" flag to true
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/30/12
 * Time: 4:29 PM
 */
class Newslog_Tools_Watchdog_Page implements Interfaces_Observer {

    /**
     * @param Application_Model_Models_Page $object
     */
    public function notify($object) {
        if($object instanceof Application_Model_Models_Page) {
            if($object->getExtraOption(Newslog::PAGE_OPTION) && !$object->getNews()) {
                $object->setNews(false);
                Application_Model_Mappers_PageMapper::getInstance()->save($object);
            }

            if($object->getNews()) {
                $newsMapper = Newslog_Models_Mapper_NewsMapper::getInstance();
                $news       = $newsMapper->findByPageId($object->getId());
                if($news instanceof Newslog_Models_Model_News) {
                    $newsMapper->save($news->setMetaData(array(
                            'h1'           => $object->getH1(),
                            'title'        => $object->getHeaderTitle(),
                            'navName'      => $object->getNavName(),
                            'url'          => $object->getUrl(),
                            'teaserText'   => $object->getTeaserText(),
                            'metaKeywords' => $object->getMetaKeywords(),
                            'template'     => $object->getTemplateId(),
                            'image'        => Tools_Page_Tools::getPreviewPath($object->getId())
                        ))
                        ->setTitle($object->getH1())
                    );
                }
            }
        }
    }

}
