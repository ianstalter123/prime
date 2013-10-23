<?php
/**
 * NewsMapper
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/25/12
 * Time: 5:54 PM
 */
class Newslog_Models_Mapper_NewsMapper extends Application_Model_Mappers_Abstract {

    protected $_dbTable = 'Newslog_Models_DbTable_News';

    protected $_model   = 'Newslog_Models_Model_News';

    /**
     * Save the Newslog_Models_Model_News to the database
     * @param Newslog_Models_Model_News $model
     * @return mixed
     * @throws Exceptions_NewslogException
     */
    public function save($model) {
        if(!$model instanceof $this->_model) {
            $model = new $this->_model($model);
        }

        //get news page id
        $pageId = $model->getPageId();

        $data   = array(
            'title'       => $model->getTitle(),
            'metaData'    => $model->getMetaData(),
            'teaser'      => $model->getTeaser(),
            'content'     => $model->getContent(),
            'broadcast'   => (boolean)$model->getBroadcast(),
            'published'   => (boolean)$model->getPublished(),
            'featured'    => (boolean)$model->getFeatured(),
            'archived'    => (boolean)$model->getArchived(),
            'type'        => $model->getType(),
            'page_id'     => ($pageId) ? $pageId : null,
            'external_id' => $model->getExternalId(),
            'user_id'     => $model->getUserId()
        );
        if($model->getId()) {
            $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $model->getId());
            $this->getDbTable()->update($data, $where);
        } else {
            $createdAt          = $model->getCreatedAt();
            $data['created_at'] = $createdAt ? $createdAt : date(Tools_System_Tools::DATE_MYSQL);
            $newsId             = $this->getDbTable()->insert($data);
            if($newsId) {
                $model->setId($newsId);
            } else {
                throw new Exceptions_NewslogException('Can not save news item!');
            }
        }
        if (!is_null($model->getTags())) {
            $newsHasTagDbTable = new Newslog_Models_DbTable_NewsHasTag();
            $newsHasTagDbTable->getAdapter()->beginTransaction();
            $newsHasTagDbTable->delete($newsHasTagDbTable->getAdapter()->quoteInto('news_id = ?', $model->getId()));
            foreach ($model->getTags() as $tag) {
                try {
                    $newsHasTagDbTable->insert(array(
                        'news_id' => $model->getId(),
                        'tag_id'  => $tag['id']
                    ));
                } catch (Exception $e) {
                    Tools_System_Tools::debugMode() && error_log($e->getMessage());
                    continue;
                }
            }
            $newsHasTagDbTable->getAdapter()->commit();
        }

        $model->notifyObservers();

        return $model;
    }

    public function delete(Newslog_Models_Model_News $news) {
        $where        = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $news->getId());
        $deleteResult = $this->getDbTable()->delete($where);
        $news->notifyObservers();
        return $deleteResult;
    }

    public function find($id) {
        $newsItem = parent::find($id);
        return $newsItem->setTags($this->_fetchRelatedTags($newsItem->getId()));
    }

    public function findByPageId($pageId) {
        $where = $this->getDbTable()->getAdapter()->quoteInto('page_id = ?', $pageId);
        $row   = $this->getDbTable()->fetchAll($where)->current();
        if(!$row) {
            return null;
        }
        return $this->_toModel($row);
    }

    public function findByTitle($title) {
        $where = $this->getDbTable()->getAdapter()->quoteInto('title = ?', $title);
        $row   = $this->getDbTable()->fetchAll($where)->current();
        if(!$row) {
            return null;
        }
        return $this->_toModel($row);
    }

    public function findByExternalId($id) {
        $where = $this->getDbTable()->getAdapter()->quoteInto('external_id = ?', $id);
        $row   = $this->getDbTable()->fetchAll($where)->current();
        if(!$row) {
            return null;
        }
        return $this->_toModel($row);
    }

    public function fetchAll($where = null, $order = array(), $count = null, $offset = null, $tags = array()) {
        $entries = array();

        $select = $this->getDbTable()
            ->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)
            ->from(array('n' => 'plugin_newslog_news'))
            ->setIntegrityCheck(false)
            ->group('n.id');

        if(!empty($tags)) {
            $select->from(array('t' => 'plugin_newslog_tag'), null)
                ->join(array('nht' => 'plugin_newslog_news_has_tag'), 'nht.tag_id = t.id AND nht.news_id = n.id', array())
                ->where('t.name IN (?)', $tags);

            //$select->having('COUNT(t.id) = ?', sizeof($tags))
        }

        if(!empty($order)) {
            $select->order($order);
        }

        if(!is_null($count)) {
            $select->limit($count, $offset);
        }

        $resultSet = $this->getDbTable()->fetchAll($select);
        if(null === $resultSet) {
            return null;
        }
        foreach ($resultSet as $row) {
            $entries[]   = $this->_toModel($row);
        }
        return $entries;
    }

    private function _toModel($row, $fetchTags = true) {
        if(!is_array($row)) {
            $row = $row->toArray();
        }
        if($fetchTags) {
            $row['tags'] = $this->_fetchRelatedTags($row['id']);
        }
        return new $this->_model($row);
    }

    private function _fetchRelatedTags($newsId) {
        $tagMapper = Newslog_Models_Mapper_TagMapper::getInstance();
        $tags      = $tagMapper->findByNewsId($newsId);
        if(is_array($tags) && !empty($tags)) {
            return array_map(function($tag) {
                return array(
                    'id'   => $tag->getId(),
                    'name' => $tag->getName()
                );
            }, $tags);
        }
        return null;
    }

}
