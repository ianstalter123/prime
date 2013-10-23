<?php
/**
 * @method Models_Mapper_CartSessionMapper getInstance() getInstance() Returns an instance of itself
 * @method Zend_Db_Table getDbTable() getDbTable()  Returns an instance of Zend_Db_Table
 */
class Models_Mapper_CartSessionMapper extends Application_Model_Mappers_Abstract {

	protected $_dbTable	= 'Models_DbTable_CartSession';

	protected $_model	= 'Models_Model_CartSession';

	/**
	 * Save cart to database
	 * @param $model Models_Model_CartSession
	 * @return mixed
	 * @throws Exceptions_SeotoasterPluginException
	 */
	public function save($model) {
		if(!$model instanceof Models_Model_CartSession) {
			throw new Exceptions_SeotoasterPluginException('Wrong model type given.');
		}
		$data = array(
			'ip_address'   => $model->getIpAddress(),
			'referer'      => $model->getReferer(),
			'user_id'      => $model->getUserId(),
			'status'       => $model->getStatus(),
			'gateway'      => $model->getGateway(),
			'shipping_address_id'   => $model->getShippingAddressId(),
			'billing_address_id'    => $model->getBillingAddressId(),
			'shipping_price'        => $model->getShippingPrice(),
			'shipping_type'         => $model->getShippingType(),
			'shipping_service'      => $model->getShippingService(),
			'shipping_tracking_id'  => $model->getShippingTrackingId(),
			'sub_total'             => $model->getSubTotal(),
			'total_tax'             => $model->getTotalTax(),
			'total'                 => $model->getTotal(),
            'notes'                 => $model->getNotes(),
			'discount'              => $model->getDiscount()
		);

		if(!$model->getId() || null === ($exists = $this->find($model->getId()))) {
			$data['created_at'] = date(DATE_ATOM);
			$newId = $this->getDbTable()->insert($data);
			if ($newId){
				$model->setId($newId);
			}
		}
		else {
			$data['updated_at'] = date(DATE_ATOM);
			$this->getDbTable()->update($data, array('id = ?' => $exists->getId()));
		}

		try {
			$this->_processCartContent($model);
		} catch (Exception $e) {
			error_log($e->getMessage());
			error_log($e->getTraceAsString());
			return false;
		}
		$model->notifyObservers();

		return $model;
	}

	private function _processCartContent(Models_Model_CartSession $cartSession){
		$cartSessionContentDbTable = new Models_DbTable_CartSessionContent();
		$content = $cartSession->getCartContent();

        $cartSessionContentDbTable->getAdapter()->beginTransaction();
		$cartSessionId = $cartSession->getId();
        if (!empty($content)) {
	        $currentProductIds = array();
            foreach ($content as $item) {
	            $productId = isset($item['product_id']) ? $item['product_id'] : $item['id'];
	            array_push($currentProductIds, $productId);

	            $select = $cartSessionContentDbTable->select()
			            ->where('cart_id = ?', $cartSessionId)
			            ->where('product_id = ?', $productId );

	            $itemRow = $cartSessionContentDbTable->fetchRow($select);
	            if (is_null($itemRow)) {
		            $itemRow = $cartSessionContentDbTable->createRow();
	            }

	            $data = array(
		            'cart_id' => $cartSessionId,
		            'product_id' => $productId,
		            'price' => $item['price'],
		            'qty' => $item['qty'],
		            'tax' => $item['tax'],
		            'tax_price' => isset($item['taxPrice']) ? $item['taxPrice'] : $item['tax_price']
	            );
	            if (isset($item['options']) && !empty($item['options'])) {
		            $options = array();
		            foreach ($item['options'] as $optName => $opt) {
			            $options[$opt['option_id']] = isset($opt['id']) ? $opt['id'] : $opt['title'];
		            }
		            $data['options'] = http_build_query($options);
		            unset($options);
	            }

	            $itemRow->setFromArray($data);
	            $r = $itemRow->save();
            }
	        $cartSessionContentDbTable->delete(array(
		        'cart_id = ?' => $cartSession->getId(),
		        'product_id NOT IN (?)' => $currentProductIds
	        ));
        } else {
	        $cartSessionContentDbTable->delete(array(
		        'cart_id = ?' => $cartSession->getId()
	        ));
        }
        $cartSessionContentDbTable->getAdapter()->commit();
	}

	/**
	 * Search for cart session by id
	 * @param $id
	 * @return Models_Model_CartSession
	 */
	public function find($id) {
		$result = $this->getDbTable()->find($id);
		if(0 == count($result)) {
			return null;
		}
		$row = $result->current();
		if ($row) {
			return $this->_toModel($row);
		}
		return null;
	}

	public function fetchAll($where = null, $order = array()) {
		$entries = array();
		$resultSet = $this->getDbTable()->fetchAll($where, $order);
		if(sizeof($resultSet)){
			foreach ($resultSet as $row) {
				$entries[] = $this->_toModel($row);
			}
		}
		return $entries;
	}

	private function _toModel(Zend_Db_Table_Row_Abstract $row) {
		$model = new $this->_model($row->toArray());
		$contentTable = new Models_DbTable_CartSessionContent();
		$select = $contentTable->select()
				->setIntegrityCheck(false)
				->from(array('c' => 'shopping_cart_session_content'))
				->join(array('prod' => 'shopping_product'), 'prod.id = c.product_id', array('name', 'sku', 'original_price'=>'price'))
				->where('cart_id = ?', $model->getId());
		$content = $contentTable->fetchAll($select)->toArray();
		if (!empty($content)){
			$cartId = $model->getId();

			foreach ($content as &$item) {
				if (!empty($item['options'])) {
					parse_str($item['options'], $tmpOptions);
					$options = $this->_restoreOptionsForCartSession($tmpOptions);
					$item['options'] = empty($options) ? null : $options;
				}
			}

			$model->setCartContent($content);
		}

		return $model;
	}

	public function findByProductId($productId){
		if (!is_numeric($productId)){}
		$select = $this->getDbTable()->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)->setIntegrityCheck(false)
				->from(array('cart' => 'shopping_cart_session'))
				->join(array('content' => 'shopping_cart_session_content'), 'content.cart_id = cart.id')
				->where('content.product_id = ?', $productId);

		APPLICATION_ENV === 'development' && error_log($select->__toString());
		return $this->getDbTable()->fetchAll($select)->toArray();
	}

	protected function _restoreOptionsForCartSession($mapping){
		if (!is_array($mapping) || empty($mapping)) {
			throw new Exceptions_SeotoasterException('Wrong parameters passed');
		}

		$options = Models_Mapper_OptionMapper::getInstance()->find(array_keys($mapping));

		$result = array();
		foreach ($options as $option) {
			$value = $mapping[$option->getId()];
			switch ($option->getType()){
				case Models_Model_Option::TYPE_DATE:
				case Models_Model_Option::TYPE_TEXT:
					$result[$option->getTitle()] = array(
						'option_id'   => $option->getId(),
						'title'       => $value,
						'priceSign'   => null,
						'priceType'   => null,
						'priceValue'  => null,
						'weightSign'  => null,
						'weightValue' => null
					);
					break;
				default:
					$selections = $option->getSelection();
					if (empty($selections)){
						continue;
					}
					$result[$option->getTitle()] = current(array_filter($selections, function($sel) use ($value) {
						return $sel['id'] === $value;
					}));
					break;
			}
		}

		return $result;
	}
}
