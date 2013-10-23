<?php
/**
 * Tag
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/23/12
 * Time: 4:03 PM
 */
class Newslog_Models_DbTable_Tag extends Zend_Db_Table_Abstract {

    protected $_name = 'plugin_newslog_tag';

    protected $_dependentTables = array(
        'Newslog_Models_DbTable_NewsHasTag'
    );
}
