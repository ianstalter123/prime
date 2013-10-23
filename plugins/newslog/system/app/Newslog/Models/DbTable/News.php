<?php
/**
 * News
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/25/12
 * Time: 6:25 PM
 */
class Newslog_Models_DbTable_News extends Zend_Db_Table_Abstract {

    protected $_name = 'plugin_newslog_news';

    protected $_dependentTables = array(
        'Newslog_Models_DbTable_NewsHasTag'
    );
}
