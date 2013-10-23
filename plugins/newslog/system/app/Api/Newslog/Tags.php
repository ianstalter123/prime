<?php
/**
 * Categories
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/3/12
 * Time: 5:37 PM
 */
class Api_Newslog_Tags extends Api_Service_Abstract {

    protected $_accessList = array(
        Tools_Security_Acl::ROLE_GUEST => array(
            'allow' => array('get')
        ),
        Tools_Security_Acl::ROLE_ADMIN => array(
            'allow' => array('get', 'post', 'put', 'delete')
        ),
        Tools_Security_Acl::ROLE_SUPERADMIN => array(
            'allow' => array('get', 'post', 'put', 'delete')
        )
    );

    public function getAction() {
        $tags    = Newslog_Models_Mapper_TagMapper::getInstance()->fetchAll();
        $tagsMap = array_map(function($tag) {
            return array(
                'id'   => $tag->getId(),
                'name' => $tag->getName()
            );
        }, $tags);
        return $tagsMap;
    }

    public function postAction() {
        $tagData = Zend_Json::decode($this->_request->getRawBody());
        $tag     = Newslog_Models_Mapper_TagMapper::getInstance()->save(
            new Newslog_Models_Model_Tag(array(
                'name' => $tagData['name']
            )
        ));
        return $tag->toArray();
    }

    public function putAction() {

    }

    public function deleteAction() {
        $ids = array_filter(filter_var_array(explode(',', $this->_request->getParam('id')), FILTER_VALIDATE_INT));
        if(!empty($ids)) {
            $tagMapper = Newslog_Models_Mapper_TagMapper::getInstance();
            $tags      = Newslog_Models_Mapper_TagMapper::getInstance()->find($ids);
            if(is_array($tags)) {
                foreach($tags as $tag) {
                    $tagMapper->delete($tag);
                }
            } else {
                $tagMapper->delete($tags);
            }
        }
    }
}
