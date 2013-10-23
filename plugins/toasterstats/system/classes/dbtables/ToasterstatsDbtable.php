<?php

class ToasterstatsDbtable extends Zend_Db_Table_Abstract {


	const CART_STATUS_SHIPPED       = 'shipped';
    const CART_STATUS_DELIVERED     = 'delivered';
    const CART_STATUS_COMPLETED     = 'completed';
    
	protected $_shoppingCartSession = 'shopping_cart_session';
    protected $_shoppingQuote = 'shopping_quote';
    protected $_user = 'user';
    protected $_shoppingCustomerAddress = 'shopping_customer_address';
    protected $_shoppingProduct = 'shopping_product';
    protected $_shoppingCartSessionContent='shopping_cart_session_content';
    protected $_page = 'page';
    protected $_billingAddressId = 'billing_address_id';
    protected $_shoppingListState = 'shopping_list_state';
    
    public function selectAllSales() {
		$where = $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_COMPLETED);
        $where .= ' OR ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_DELIVERED);
        $where .= ' OR ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_SHIPPED);
        $select = $this->getAdapter()->select()->from($this->_shoppingCartSession)->where($where);
        return $this->getAdapter()->fetchAll($select);
        
	}
    
     public function selectAllNewSales() {
		$where = $this->getAdapter()->quoteInto('status = ?', 'new');
        $select = $this->getAdapter()->select()->from($this->_shoppingCartSession)->where($where);
        return $this->getAdapter()->fetchAll($select);
        
	}
    
    public function selectSalesFromPeriod($period) {
        $where = 'created_at '.$period.' and status="completed" and gateway<>"Quote"';
        $where .= ' OR created_at '.$period.' AND gateway<>"Quote" AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_DELIVERED);
        $where .= ' OR created_at '.$period.' AND gateway<>"Quote" AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_SHIPPED);
        $select = $this->getAdapter()->select()->from($this->_shoppingCartSession)->where($where);
        return $this->getAdapter()->fetchAll($select);
        
    }
    
    public function selectAllMoney() {
        $where = $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_COMPLETED);
        $where .= ' OR ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_DELIVERED);
        $where .= ' OR ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_SHIPPED);
        $select = $this->getAdapter()->select()->from($this->_shoppingCartSession, array('SUM(total) as count'))->where($where);
        return $this->getAdapter()->fetchAll($select);
               
    }
    
    public function selectAllMoneyFromPeriod($period) {
        $where = 'created_at '.$period.' and status="completed"';
        $where .= ' OR created_at '.$period.' AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_DELIVERED);
        $where .= ' OR created_at '.$period.' AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_SHIPPED);
        $select = $this->getAdapter()->select()->from($this->_shoppingCartSession, array('SUM(total) as count'))->where($where);
        return $this->getAdapter()->fetchAll($select);
               
    }
    
    public function selectAllQuotes() {
        $where = $this->getAdapter()->quoteInto('q.status = ?', 'sold');
        $select = $this->getAdapter()->select()->from(array('s'=>$this->_shoppingCartSession), array('SUM(total) as count'))->join(array('q' => $this->_shoppingQuote),
                    's.id = q.cart_id', array('created_at'))->where($where)->group('q.created_at');
        
        return $this->getAdapter()->fetchAll($select);
               
    }
    
    public function selectAllQuotesAmountFromPeriod($period) {
        $where = 'q.created_at '.$period.' and q.status="sold"';
        $select = $this->getAdapter()->select()->from(array('s'=>$this->_shoppingCartSession), array('total as count'))->join(array('q' => $this->_shoppingQuote),
                    's.id = q.cart_id', array('created_at'))->where($where)->group('q.created_at');
        
        return $this->getAdapter()->fetchAll($select);
               
    }
    
    public function selectAllQuotesAmountFromPeriodWithoutTax($period) {
        $where = 'q.created_at '.$period.' and q.status="sold"';
        $select = $this->getAdapter()->select()->from(array('s'=>$this->_shoppingCartSession), array('sub_total as count'))->join(array('q' => $this->_shoppingQuote),
                    's.id = q.cart_id', array('created_at'))->where($where)->group('q.created_at');
        
        return $this->getAdapter()->fetchAll($select);
               
    }
    
    public function selectQuotesFromPeriod($period) {
        $where = 'created_at '.$period.' and status="new"';
        $select = $this->getAdapter()->select()->from($this->_shoppingQuote, array('COUNT(*) as count'))->where($where);
        return $this->getAdapter()->fetchAll($select);
               
    }
    
    public function selectQuotesFromPeriodWithoutStatus($period) {
        $where = 'created_at '.$period.'';
        $select = $this->getAdapter()->select()->from($this->_shoppingQuote)->where($where);
        return $this->getAdapter()->fetchAll($select);
               
    }
    
    public function selectLastOrder() {
        $where = $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_COMPLETED);
        $where .= ' OR ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_DELIVERED);
        $where .= ' OR ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_SHIPPED);
        $select = $this->getAdapter()->select()->from($this->_shoppingCartSession)->where($where)->order('created_at DESC');
        return $this->getAdapter()->fetchAll($select);
               
    }
    
    public function selectAverageTotalOrder() {
        $where = $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_COMPLETED);
        $where .= ' OR ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_DELIVERED);
        $where .= ' OR ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_SHIPPED);
        $select = $this->getAdapter()->select()->from($this->_shoppingCartSession, array('avg(total) as count'))->where($where);
        return $this->getAdapter()->fetchAll($select);
               
    }
    
    public function selectAverageOrdersFromPeriod($period) {
        $where = 'created_at '.$period.' and status="completed"';
        $where .= ' OR created_at '.$period.' AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_DELIVERED);
        $where .= ' OR created_at '.$period.' AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_SHIPPED);
        $select = $this->getAdapter()->select()->from($this->_shoppingCartSession, array('avg(total) as count'))->where($where);
        return $this->getAdapter()->fetchAll($select);
               
    }
    
    public function selectAmountFromPeriod($period) {
        $where = 'created_at '.$period.' and status="completed" and gateway<>"Quote"';
        $where .= ' OR created_at '.$period.' AND gateway<>"Quote" AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_DELIVERED);
        $where .= ' OR created_at '.$period.' AND gateway<>"Quote" AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_SHIPPED);
        $select = $this->getAdapter()->select()->from($this->_shoppingCartSession, array('SUM(total) as count', 'created_at' ))->where($where)->group('created_at');
        return $this->getAdapter()->fetchAll($select);
               
    }
    
    public function selectAmountFromPeriodWithoutTax($period) {
        $where = 'created_at '.$period.' and status="completed" and gateway<>"Quote"';
        $where .= ' OR created_at '.$period.' AND gateway<>"Quote" AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_DELIVERED);
        $where .= ' OR created_at '.$period.' AND gateway<>"Quote" AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_SHIPPED);
        $select = $this->getAdapter()->select()->from($this->_shoppingCartSession, array('SUM(sub_total) as count', 'created_at' ))->where($where)->group('created_at');
        return $this->getAdapter()->fetchAll($select);
               
    }
    
     public function salesamountByProduct($period) {
         return $this->getAdapter()->fetchAll('
			SELECT `shopping_product`.`name` , `shopping_product`.`id` , `shopping_cart_session_content`.`qty` AS count, `shopping_cart_session_content`.`tax_price`
				FROM `shopping_cart_session_content` 
				RIGHT JOIN `shopping_product` ON `shopping_product`.`id` = `shopping_cart_session_content`.`product_id`
				RIGHT JOIN `shopping_cart_session` ON `shopping_cart_session_content`.`cart_id` = `shopping_cart_session`.`id` 
				WHERE `shopping_cart_session`.`status` = \'completed\' and `shopping_cart_session`.`created_at`' .$period.' OR 
                    `shopping_cart_session`.`status` = \'delivered\' and `shopping_cart_session`.`created_at`' .$period.' OR 
                    `shopping_cart_session`.`status` = \'shipped\' and `shopping_cart_session`.`created_at`' .$period.'
				');
    }
    
    public function salesamountByProductWithoutTax($period) {
         return $this->getAdapter()->fetchAll('
			SELECT `shopping_product`.`name` , `shopping_product`.`id` , `shopping_cart_session_content`.`qty` AS count, `shopping_cart_session_content`.`price` as tax_price
				FROM `shopping_cart_session_content` 
				RIGHT JOIN `shopping_product` ON `shopping_product`.`id` = `shopping_cart_session_content`.`product_id`
				RIGHT JOIN `shopping_cart_session` ON `shopping_cart_session_content`.`cart_id` = `shopping_cart_session`.`id` 
				WHERE `shopping_cart_session`.`status` = \'completed\' and `shopping_cart_session`.`created_at`' .$period.' OR 
                    `shopping_cart_session`.`status` = \'delivered\' and `shopping_cart_session`.`created_at`' .$period.' OR 
                    `shopping_cart_session`.`status` = \'shipped\' and `shopping_cart_session`.`created_at`' .$period.'
				');
    }
    
    public function salesamountByBrand($period) {
       return $this->getAdapter()->fetchAll('SELECT `shopping_brands`.`name` , `shopping_product`.`id` , `shopping_cart_session_content`.`qty` AS count, `shopping_cart_session_content`.`tax_price`
            FROM `shopping_cart_session_content`
            RIGHT JOIN `shopping_product` ON `shopping_product`.`id` = `shopping_cart_session_content`.`product_id`
            RIGHT JOIN `shopping_brands` ON `shopping_brands`.`id` = `shopping_product`.`brand_id`
            RIGHT JOIN `shopping_cart_session` ON `shopping_cart_session_content`.`cart_id` = `shopping_cart_session`.`id`
            WHERE `shopping_cart_session`.`status` = \'completed\' and `shopping_cart_session`.`created_at`' .$period.' OR 
                  `shopping_cart_session`.`status` = \'delivered\' and `shopping_cart_session`.`created_at`' .$period.' OR 
                  `shopping_cart_session`.`status` = \'shipped\' and `shopping_cart_session`.`created_at`' .$period.'
            ');
    }
    
    public function salesamountByBrandWithoutTax($period) {
       return $this->getAdapter()->fetchAll('SELECT `shopping_brands`.`name` , `shopping_product`.`id` , `shopping_cart_session_content`.`qty` AS count, `shopping_cart_session_content`.`price` as tax_price
            FROM `shopping_cart_session_content`
            RIGHT JOIN `shopping_product` ON `shopping_product`.`id` = `shopping_cart_session_content`.`product_id`
            RIGHT JOIN `shopping_brands` ON `shopping_brands`.`id` = `shopping_product`.`brand_id`
            RIGHT JOIN `shopping_cart_session` ON `shopping_cart_session_content`.`cart_id` = `shopping_cart_session`.`id`
            WHERE `shopping_cart_session`.`status` = \'completed\' and `shopping_cart_session`.`created_at`' .$period.' OR 
                  `shopping_cart_session`.`status` = \'delivered\' and `shopping_cart_session`.`created_at`' .$period.' OR 
                  `shopping_cart_session`.`status` = \'shipped\' and `shopping_cart_session`.`created_at`' .$period.'
            ');
    }
               
        
    public function salesamountByTag($period) {
        return $this->getAdapter()->fetchAll('SELECT `shopping_tags`.`name` , `shopping_product`.`id` , `shopping_cart_session_content`.`qty` AS count, `shopping_cart_session_content`.`tax_price` from `shopping_cart_session_content`   RIGHT JOIN `shopping_cart_session` ON `shopping_cart_session_content`.`cart_id` = `shopping_cart_session`.`id`
              RIGHT JOIN `shopping_product` ON `shopping_product`.`id` = `shopping_cart_session_content`.`product_id`
              RIGHT JOIN `shopping_product_has_tag` ON `shopping_product_has_tag`.`product_id` = `shopping_product`.`id`
              RIGHT JOIN `shopping_tags` ON `shopping_product_has_tag`.`tag_id` = `shopping_tags`.`id`  WHERE `shopping_cart_session`.`status` = \'completed\' and `shopping_cart_session`.`created_at`' .$period.' OR 
                  `shopping_cart_session`.`status` = \'delivered\' and `shopping_cart_session`.`created_at`' .$period.' OR 
                  `shopping_cart_session`.`status` = \'shipped\' and `shopping_cart_session`.`created_at`' .$period.'
              ');
    }
    
    public function salesamountByTagWitoutTax($period) {
        return $this->getAdapter()->fetchAll('SELECT `shopping_tags`.`name` , `shopping_product`.`id` , `shopping_cart_session_content`.`qty` AS count, `shopping_cart_session_content`.`price` as tax_price from `shopping_cart_session_content`   RIGHT JOIN `shopping_cart_session` ON `shopping_cart_session_content`.`cart_id` = `shopping_cart_session`.`id`
              RIGHT JOIN `shopping_product` ON `shopping_product`.`id` = `shopping_cart_session_content`.`product_id`
              RIGHT JOIN `shopping_product_has_tag` ON `shopping_product_has_tag`.`product_id` = `shopping_product`.`id`
              RIGHT JOIN `shopping_tags` ON `shopping_product_has_tag`.`tag_id` = `shopping_tags`.`id`  WHERE `shopping_cart_session`.`status` = \'completed\' and `shopping_cart_session`.`created_at`' .$period.' OR 
                  `shopping_cart_session`.`status` = \'delivered\' and `shopping_cart_session`.`created_at`' .$period.' OR 
                  `shopping_cart_session`.`status` = \'shipped\' and `shopping_cart_session`.`created_at`' .$period.'
              ');
    }
    
    public function salesamountByCustomer($period) {
        return $this->getAdapter()->fetchAll('SELECT sum(`shopping_cart_session`.`total`) as count , `user`.`full_name` as name  from `shopping_cart_session` JOIN  `user` ON `shopping_cart_session`.`user_id` = `user`.`id` where `shopping_cart_session`.`status` = \'completed\' 
            and `shopping_cart_session`.`created_at`' .$period. ' OR `shopping_cart_session`.`status` = \'delivered\' 
            and `shopping_cart_session`.`created_at`' .$period. ' OR `shopping_cart_session`.`status` = \'shipped\' 
            and `shopping_cart_session`.`created_at`' .$period. ' group by `user`.`id`');
               
    }
    
    public function salesamountByCustomerWithoutTax($period) {
        return $this->getAdapter()->fetchAll('SELECT sum(`shopping_cart_session`.`sub_total`) as count , `user`.`full_name` as name  from `shopping_cart_session` JOIN  `user` ON `shopping_cart_session`.`user_id` = `user`.`id` 
            where `shopping_cart_session`.`status` = \'completed\' and `shopping_cart_session`.`created_at`' .$period. ' OR 
                `shopping_cart_session`.`status` = \'delivered\' and `shopping_cart_session`.`created_at`' .$period. ' OR 
                `shopping_cart_session`.`status` = \'shipped\' and `shopping_cart_session`.`created_at`' .$period. ' 
                group by `user`.`id`');
               
    }
    
    public function salesamountByTypeSalesQuotes($period) {
        $where = 'created_at '.$period.' and status="completed" and gateway="Quote"';
        $where .= ' OR created_at '.$period.' AND gateway="Quote" AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_DELIVERED);
        $where .= ' OR created_at '.$period.' AND gateway="Quote" AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_SHIPPED);
        $select = $this->getAdapter()->select()->from($this->_shoppingCartSession, array('SUM(total) as count'))->where($where);
        return $this->getAdapter()->fetchAll($select);
        
    }
    
    public function salesamountByTypeSalesQuotesWithoutTax($period) {
        $where = 'created_at '.$period.' and status="completed" and gateway="Quote"';
        $where .= ' OR created_at '.$period.' AND gateway="Quote" AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_DELIVERED);
        $where .= ' OR created_at '.$period.' AND gateway="Quote" AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_SHIPPED);
        $select = $this->getAdapter()->select()->from($this->_shoppingCartSession, array('SUM(sub_total) as count'))->where($where);
        return $this->getAdapter()->fetchAll($select);
        
    }
    
    public function salesamountByTypeSalesCart($period) {
        $where = 'created_at '.$period.' and status="completed" and gateway<>"Quote"';
        $where .= ' OR created_at '.$period.' AND gateway<>"Quote" AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_DELIVERED);
        $where .= ' OR created_at '.$period.' AND gateway<>"Quote" AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_SHIPPED);
        $select = $this->getAdapter()->select()->from($this->_shoppingCartSession, array('SUM(total) as count'))->where($where);
        return $this->getAdapter()->fetchAll($select);
    }
    
    public function salesamountByTypeSalesCartWithoutTax($period) {
        $where = 'created_at '.$period.' and status="completed" and gateway<>"Quote"';
        $where .= ' OR created_at '.$period.' AND gateway<>"Quote" AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_DELIVERED);
        $where .= ' OR created_at '.$period.' AND gateway<>"Quote" AND ' . $this->getAdapter()->quoteInto('status = ?', self::CART_STATUS_SHIPPED);
        $select = $this->getAdapter()->select()->from($this->_shoppingCartSession, array('SUM(sub_total) as count'))->where($where);
        return $this->getAdapter()->fetchAll($select);
    }
    
    public function salesCustomersCountrysByPeriod($period) {
        return $this->getAdapter()->fetchAll('SELECT `shopping_customer_address`.`country` , `shopping_cart_session`.`total`
            FROM `shopping_customer_address`
            JOIN `shopping_cart_session` ON `shopping_cart_session`.`billing_address_id` = `shopping_customer_address`.`id`
            where `shopping_cart_session`.`status` = \'completed\' and `shopping_cart_session`.`created_at`' .$period.' OR 
            `shopping_cart_session`.`status` = \'delivered\' and `shopping_cart_session`.`created_at`' .$period.' OR 
            `shopping_cart_session`.`status` = \'shipped\' and `shopping_cart_session`.`created_at`' .$period.' 
        ');
    }
    
    public function salesCustomersStatesByPeriod($period) {
        return $this->getAdapter()->fetchAll('SELECT `shopping_customer_address`.`country` , `shopping_list_state`.`state` , `shopping_cart_session`.`total`
            FROM `shopping_customer_address`
            JOIN `shopping_cart_session` ON `shopping_cart_session`.`billing_address_id` = `shopping_customer_address`.`id`
            JOIN `shopping_list_state` ON `shopping_customer_address`.`state` = `shopping_list_state`.`id` 
            where `shopping_cart_session`.`status` = \'completed\' and `shopping_cart_session`.`created_at`' .$period.' OR 
                `shopping_cart_session`.`status` = \'delivered\' and `shopping_cart_session`.`created_at`' .$period.' OR 
                `shopping_cart_session`.`status` = \'shipped\' and `shopping_cart_session`.`created_at`' .$period.'
            ');
    }
    
    public function lastNewCustomers($limit=5) {
        $where = 's.status="completed" or s.status="pending" or s.status="delivered" or s.status="shipped"';
        $select = $this->getAdapter()->select()->from(array('u'=>$this->_user), 'u.full_name')->join(array('s' => $this->_shoppingCartSession),
                    'u.id = s.user_id', array('u.reg_date', 's.total'))->join(array('sk' => $this->_shoppingCustomerAddress),
                    'sk.id = s.billing_address_id')->where($where)->group('u.id')->order('u.reg_date DESC')->limit($limit,0);
        
        return $this->getAdapter()->fetchAll($select);
    }
    
    public function lastNewCustomersOrders($limit=5) {
        $where = 's.status="completed" or s.status="pending" or s.status="delivered" or s.status="shipped"';
        $select = $this->getAdapter()->select()->from(array('u'=>$this->_user), 'u.full_name')->join(array('s' => $this->_shoppingCartSession),
                    'u.id = s.user_id', array('u.reg_date', 's.total', 's.created_at'))->join(array('sk' => $this->_shoppingCustomerAddress),
                    'sk.id = s.billing_address_id')->where($where)->order('s.created_at DESC')->limit($limit,0);
        
        return $this->getAdapter()->fetchAll($select);
    }
        
    public function mostSaledProducts($period, $limit=5) {
        $where = 'scs.created_at '.$period.' and scs.status="completed" or scs.created_at '.$period.' and scs.status="delivered" or scs.created_at '.$period.' and scs.status="shipped"';
        $select = $this->getAdapter()->select()->from(array('scsc'=>$this->_shoppingCartSessionContent),  array('sum(scsc.qty) as count', 'scsc.tax_price'))->joinRight(array('sp' => $this->_shoppingProduct),
                    'sp.id = scsc.product_id', array('sp.name', 'sp.id'))->joinRight(array('scs' => $this->_shoppingCartSession),
                    'scs.id = scsc.cart_id')->joinRight(array('p' => $this->_page),
                    'p.id = sp.page_id')->where($where)->group('sp.name')->order('count DESC')->limit($limit,0);
        
        return $this->getAdapter()->fetchAll($select); 
    }
    
    public function mostSaledProductsAllTime($limit=5) {
        $where = 'scs.status="completed" or scs.status="delivered" or scs.status="shipped"';
        $select = $this->getAdapter()->select()->from(array('scsc'=>$this->_shoppingCartSessionContent),  array('sum(scsc.qty) as count', 'scsc.tax_price'))->joinRight(array('sp' => $this->_shoppingProduct),
                    'sp.id = scsc.product_id', array('sp.name', 'sp.id'))->joinRight(array('scs' => $this->_shoppingCartSession),
                    'scs.id = scsc.cart_id')->joinRight(array('p' => $this->_page),
                    'p.id = sp.page_id')->where($where)->group('sp.name')->order('count DESC')->limit($limit,0);
        
        return $this->getAdapter()->fetchAll($select); 
    }
    
    public function quantityOfStaticMenuPages(){
        $pageDbTable = new Application_Model_DbTable_Page();
        $where = $pageDbTable->getAdapter()->quoteInto("show_in_menu = '?'", Application_Model_Models_Page::IN_STATICMENU);
        $select = $pageDbTable->getAdapter()->select()->from('page', array("id"=>"COUNT(*)"))->where($where);
        return $pageDbTable->getAdapter()->fetchAll($select); 
    }
    
    public function quantityOfNomenuPages(){
        $pageDbTable = new Application_Model_DbTable_Page();
        $where = sprintf("show_in_menu = '%s' AND parent_id = %d", Application_Model_Models_Page::IN_NOMENU, Application_Model_Models_Page::IDCATEGORY_DEFAULT);
        $select = $pageDbTable->getAdapter()->select()->from('page', array("id"=>"COUNT(*)"))->where($where);
        return $pageDbTable->getAdapter()->fetchAll($select);
    }
    
    public function quantityOfProducts(){
        $productDbTable = new Models_DbTable_Product();
        $select = $productDbTable->getAdapter()->select()->from('shopping_product', array("id"=>"COUNT(*)"));
        return $productDbTable->getAdapter()->fetchAll($select);
    }
    
}
