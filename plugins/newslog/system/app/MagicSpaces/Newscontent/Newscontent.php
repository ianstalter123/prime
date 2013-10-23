<?php
/**
 * Newslog
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/5/12
 * Time: 7:48 PM
 */
class MagicSpaces_Newscontent_Newscontent extends Tools_MagicSpaces_Abstract {

    protected function _run() {
        $newMapper = Newslog_Models_Mapper_NewsMapper::getInstance();
        $news = $newMapper->findByPageId($this->_toasterData['id']);
        if($news instanceof Newslog_Models_Model_News) {
            if($news->getType() == Newslog_Models_Model_News::TYPE_INTERNAL) {
                if(md5($this->_spaceContent) != md5($news->getContent())) {
                    $news->setContent($this->_spaceContent);
                    $newMapper->save($news);
                }
            } else {
               $this->_spaceContent = $news->getContent();
            }
        }
    }

}
