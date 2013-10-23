<?php
/**
 * NewsHasTag
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/25/12
 * Time: 6:35 PM
 */
class Newslog_Models_DbTable_NewsHasTag extends Zend_Db_Table_Abstract {

    protected $_name = 'plugin_newslog_news_has_tag';

    protected $_referenceMap = array(
        'News' => array(
            'columns'		=> 'news_id',
            'refTableClass'	=> 'Newslog_Models_DbTable_News',
            'refColumns'	=> 'id'
        ),
        'Tags' => array(
            'columns'		=> 'tag_id',
            'refTableClass'	=> 'Newslog_Models_DbTable_Tag',
            'refColumns'	=> 'id'
        )
    );
}
