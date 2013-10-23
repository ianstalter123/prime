<?php
/**
 * TagMapper
 *
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/23/12
 * Time: 3:56 PM
 */
class Newslog_Models_Mapper_TagMapper extends Application_Model_Mappers_Abstract {

    protected $_dbTable = 'Newslog_Models_DbTable_Tag';

    protected $_model   = 'Newslog_Models_Model_Tag';

    public function save($model) {

        if(!$model instanceof $this->_model) {
            $model = new $this->_model($model);
        }

        $data = array(
            'name' => $model->getName()
        );

        if($model->getId()) {
            $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $model->getId());
            $this->getDbTable()->update($data, $where);
        } else {
            $data['created_at'] = date(DATE_ATOM);
            $tagId = $this->getDbTable()->insert($data);
            if($tagId) {
                $model->setId($tagId);
            } else {
                throw new Exceptions_NewslogException('Can not save news tag!');
            }
        }

        $model->notifyObservers();

        return $model;

    }

    public function findByNewsId($newsId) {
        $entries = array();
        $where   =  $this->getDbTable()->getAdapter()->quoteInto('nht.news_id=?', $newsId);
        $select  = $this->getDbTable()->getAdapter()->select()
            ->from(array('nht' => 'plugin_newslog_news_has_tag'))
            ->join(array('t' => 'plugin_newslog_tag'), 'nht.tag_id=t.id')
            ->where($where);
        $tags = $this->getDbTable()->getAdapter()->fetchAll($select);
        if(!is_array($tags) || empty($tags)) {
            return null;
        }
        foreach($tags as $tagData) {
            $entries[] = new $this->_model($tagData);
        }
        return $entries;
    }

    public function delete(Newslog_Models_Model_Tag $tag) {
        $where        = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $tag->getId());
        $deleteResult = $this->getDbTable()->delete($where);
        $tag->notifyObservers();
        return $deleteResult;
    }

    public function findByName($name) {
        $where = $this->getDbTable()->getAdapter()->quoteInto('name=?', $name);
        return $this->_findWhere($where);
    }
}
