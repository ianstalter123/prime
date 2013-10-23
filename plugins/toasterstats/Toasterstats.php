<?php
set_include_path(implode(PATH_SEPARATOR, array(
	dirname(__FILE__) . '/system/classes/dbtables',
	dirname(__FILE__) . '/system/classes/mappers',
	dirname(__FILE__) . '/system/classes/models',
    get_include_path()
)));
class Toasterstats extends Tools_Plugins_Abstract {
	
    const RESOURCE_TOASTER_STATS = 'toaster_stats';
    const ROLE_SALESPERSON = 'sales person';
    
    private $_periodArray = array('days', 'week', 'month', 'year', 'totalPeriod');
    private $_typeOfPiarChartGraf = array('product', 'brand', 'tag', 'customer', 'type');
    
	public function  __construct($options, $seotoasterData) {
		parent::__construct($options, $seotoasterData);
		$this->_dbTable = new Zend_Db_Table();
		$this->_view->setScriptPath(dirname(__FILE__) . '/views/');
	}
	public function run($requestedParams = array()) {
        $dispatchersResult = parent::run($requestedParams);
		if($dispatchersResult) {
			return $dispatchersResult;
		}

	}
    
    public function beforeController(){
        $acl = Zend_Registry::get('acl');
        if(!$acl->has(self::RESOURCE_TOASTER_STATS)) {
            $acl->addResource(new Zend_Acl_Resource(self::RESOURCE_TOASTER_STATS));
        }
        $acl->allow(self::ROLE_SALESPERSON, self::RESOURCE_TOASTER_STATS);
        $acl->allow(Tools_Security_Acl::ROLE_ADMIN, self::RESOURCE_TOASTER_STATS);
        $acl->allow(Tools_Security_Acl::ROLE_SUPERADMIN, self::RESOURCE_TOASTER_STATS);
        Zend_Registry::set('acl', $acl);
    }
    
    
    public function _makeOptionGraph(){
        if(Tools_Security_Acl::isAllowed(self::RESOURCE_TOASTER_STATS)){
            if(isset($this->_options[1]) && isset($this->_options[2]) && isset($this->_options[3]) && isset($this->_options[4])){
                $currencyHandler = Zend_Registry::get('Zend_Currency');
                $currencySymbol = $currencyHandler->getSymbol();
                $toasterstatsDbTable = new ToasterstatsDbtable();
                $this->_view->currencySymbol = $currencySymbol;
                if($this->_options[1] == 'geo' && isset($this->_options[2])){
                    if($this->_options[2] == 'map'){
                        if(in_array($this->_options[3], $this->_periodArray)){
                            if(!isset($this->_options[4])){
                                $period = $this->_createTimePeriod(1, $this->_options[3]);
                            }
                            else{
                                $period = $this->_createTimePeriod($this->_options[4], $this->_options[3]);
                            }
                        }
                        $mapData = $this->_geoMapHandler($period);
                        $this->_view->typeOfMap = $mapData['typeofMap'];
                        $this->_view->mapData = $mapData['mapData'];
                        isset($this->_options[5]) ? $this->_view->width = $this->_options[5] : $this->_view->width = 240;
                        isset($this->_options[6]) ? $this->_view->height = $this->_options[6] : $this->_view->height = 150;
                        return $this->_view->render('geochart.phtml');
                    }
                    
                }
                if($this->_options[1] == 'linechart'){
                   if($this->_options[2] == 'count' || $this->_options[2] == 'amount' || $this->_options[2] == 'averageamount'){
                   $this->_view->typeSales = $this->_options[3];
                   $this->_view->typeOfGrafic = $this->_options[2];
                   if($this->_options[2] == 'count'){
                        $this->_view->typeSalesCount = $this->_options[3];
                   }
                   if($this->_options[2] == 'averageamount'){
                        $this->_view->typeSalesAverageAmount = $this->_options[3];
                   }
                   $totalSales = array('0'=>array('created_at'=>''));
                   $totalQuotes = array('0'=>array('created_at'=>''));
                   if(in_array($this->_options[4], $this->_periodArray)){
                       if(!isset($this->_options[5])){
                            $period = $this->_createTimePeriod(1, $this->_options[4]);
                            $datepickerPeriod = $this->_createTimePeriodForDatepicker(1, $this->_options[4]);
                       }
                       else{
                            $period = $this->_createTimePeriod($this->_options[5], $this->_options[4]);
                            $datepickerPeriod = $this->_createTimePeriodForDatepicker($this->_options[5], $this->_options[4]);
                       }
                       $datePikerPeriod = explode('|', $datepickerPeriod);
                       $datePikerPeriod[0] = date("d-M-Y", strtotime($datePikerPeriod[0]));
                       $datePikerPeriod[1] = date("d-M-Y", strtotime($datePikerPeriod[1]));
                       $this->_view->datepickerPeriod = $datePikerPeriod;
                       $rightPeriodForGraf = $this->_rigthGrafhPeriod($this->_options[4]);
                       if(isset($this->_options[4])){
                            $date = $this->_checkPeriod($this->_options[4], $this->_options[5]);
                            
                       }else{
                            $date = $this->_checkPeriod($this->_options[4], 1);
                       }
                       $this->_view->timePeriod = $this->_options[5].$this->_options[4];
                       $data =  $this->_createGraficsData($this->_options[2], $this->_options[3], $period, $rightPeriodForGraf, $date);
                       if(count($data)>1){
                           $data = array_reverse($data);
                       }
                       $this->_view->data = $data;
                       isset($this->_options[6]) ? $this->_view->width = $this->_options[6] : $this->_view->width = 240;
                       isset($this->_options[7]) ? $this->_view->height = $this->_options[7] : $this->_view->height = 150;
                       return $this->_view->render('linechart.phtml');
                   }
                 }
                }
                if($this->_options[1] == 'piechart'){
                    if($this->_options[2] == 'amount'){
                        $this->_view->typeSales = $this->_options[3];
                        $this->_view->typeOfGrafic = $this->_options[2];
                    }
                    if(in_array($this->_options[4], $this->_periodArray)){
                       if(!isset($this->_options[5])){
                            $period = $this->_createTimePeriod(1, $this->_options[4]);
                       }
                       else{
                            $period = $this->_createTimePeriod($this->_options[5], $this->_options[4]);
                       }
                    }
                    if(in_array($this->_options[3], $this->_typeOfPiarChartGraf)){
                        $totalProductsFromPeriod = $this->_changePirchartAmountGrafic($this->_options[3], $period);
                    }
                    $this->_view->dataArray = $totalProductsFromPeriod;
                    isset($this->_options[6]) ? $this->_view->width = $this->_options[6] : $this->_view->width = 240;
                    isset($this->_options[7]) ? $this->_view->height = $this->_options[7] : $this->_view->height = 150;
                    return $this->_view->render('piechart.phtml');
                }
                if($this->_options[1] == 'columnchart'){
                   if($this->_options[2] == 'count' || $this->_options[2] == 'amount'){
                        $this->_view->typeSales = $this->_options[3];
                        $this->_view->typeOfGrafic = $this->_options[2];
                        if($this->_options[2] == 'count'){
                           $this->_view->typeSalesCount = $this->_options[3];
                   }
                   if($this->_options[2] == 'amount'){
                        $this->_view->typeSalesAmount = $this->_options[3];
                   }
                   $totalSales = array('0'=>array('created_at'=>''));
                   $totalQuotes = array('0'=>array('created_at'=>''));
                   if(in_array($this->_options[4], $this->_periodArray)){
                       if(!isset($this->_options[5])){
                            $period = $this->_createTimePeriod(1, $this->_options[4]);
                       }
                       else{
                            $period = $this->_createTimePeriod($this->_options[5], $this->_options[4]);
                       }
                       $rightPeriodForGraf = $this->_rigthGrafhPeriod($this->_options[4]);
                       if(isset($this->_options[4])){
                            $date = $this->_checkPeriod($this->_options[4], $this->_options[5]);
                            
                       }else{
                            $date = $this->_checkPeriod($this->_options[4], 1);
                       }
                       isset($this->_options[6]) ? $this->_view->width = $this->_options[6] : $this->_view->width = 240;
                       isset($this->_options[7]) ? $this->_view->height = $this->_options[7] : $this->_view->height = 150;
                       if($this->_options[3] == 'tag'){
                            $totalTags = $this->_createCountTagGraf($period);
                            $this->_view->data = $totalTags;
                            return $this->_view->render('columnchartTag.phtml');
                       }
                       $data =  $this->_createGraficsData($this->_options[2], $this->_options[3], $period, $rightPeriodForGraf, $date);
                       if(count($data)>1){
                           $data = array_reverse($data);
                       }
                       $this->_view->data = $data;
                       return $this->_view->render('columnchart.phtml');
                  }
               }         
            }
         }
      }
    }
    
    private function _createCountTagGraf($period){
        $totalTags = array('Nothing Found'=>'1');
        $toasterstatsDbTable = new ToasterstatsDbtable();
        $productTagsByPeriod = $toasterstatsDbTable->salesamountByTag($period);
        if(!empty($productTagsByPeriod)){
            $tagResult = array();
            foreach($productTagsByPeriod as $information){
              if(!isset($tagResult[$information['name']])){
                  $tagResult[$information['name']] = 1;
              }
              else{
                  $tagResult[$information['name']] = $tagResult[$information['name']] + 1;
              }
              
            }
         $totalTags = $tagResult;
         
        }
        return $totalTags;
        
    }
    
   public function _makeOptionProducts(){
       if(Tools_Security_Acl::isAllowed(self::RESOURCE_TOASTER_STATS)){
        if(isset($this->_options[1]) && isset($this->_options[2])){
            $toasterstatsDbTable = new ToasterstatsDbtable();
            $this->_view->translator = $this->_translator;
            $totalProducts = array(0=>array('name'=>'Nothing found')); 
            if($this->_options[1] == 'topsellers'){
                if($this->_options[2] == 'today'){
                    $period = $this->_createTimePeriod(1, 'days');
                    isset($this->_options[3]) ? $limit = $this->_options[3] : $limit = 5;
                    $productTop = $toasterstatsDbTable->mostSaledProducts($period, $limit);
                    if(!empty($productTop)){
                        $totalProducts = $productTop;
                    }
                    $this->_view->todayProducts = '';
                }
                else{
                    isset($this->_options[4]) ? $limit = $this->_options[4] : $limit = 5;
                    if(!isset($this->_options[2])){
                        $period = $this->_createTimePeriod(1, $this->_options[2]);
                    }
                    else{
                        $period = $this->_createTimePeriod($this->_options[3], $this->_options[2]);
                    }
                    $productTop = $toasterstatsDbTable->mostSaledProducts($period, $limit);
                    $totalProducts = $this->_prepareProducts($productTop);           
                    $this->_view->productsFromPeriod = '';
                    $this->_view->limitForBestsselers = $limit;
                }
                $this->_view->totalProducts = $totalProducts;
                return $this->_view->render('products.phtml');
           }
        }
      }
   } 
    
   private function _prepareProducts($productArray){
       $totalProducts = array(0=>array('name'=>'Nothing found'));
       if(!empty($productArray)){
           $totalProducts = $productArray;
       }
       $this->_view->translator = $this->_translator;
       $this->_view->productsData = $totalProducts;
       return $this->_view->render('prepareProducts.phtml');
       
   }
    
   public function _makeOptionCustomer(){
        if(Tools_Security_Acl::isAllowed(self::RESOURCE_TOASTER_STATS)){
            if(isset($this->_options[1])){
                $toasterstatsDbTable = new ToasterstatsDbtable();
                $this->_view->translator = $this->_translator;
                if($this->_options[1] == 'new'){
                    isset($this->_options[2]) ? $limit = $this->_options[2] : $limit = 5;
                    $lastCustomers = $toasterstatsDbTable->lastNewCustomers($limit);
                    $customerDataArray = array();
                    if(!empty($lastCustomers)){
                        foreach($lastCustomers as $key=>$custInfo){
                            if($custInfo['state'] == null){$custInfo['state'] = 0;}
                            $state = Tools_Geo::getStateById(intval($custInfo['state']));
                            if($state == ''){
                               $customerDataArray[$key]['state'] = $state; 
                            }else{
                               $customerDataArray[$key]['state'] = $state['name'];
                            }
                            $customerDataArray[$key]['fullName'] = $custInfo['full_name'];
                            $customerDataArray[$key]['city'] = $custInfo['city'];
                            $customerDataArray[$key]['total'] = $custInfo['total'];
                            $customerDataArray[$key]['userId'] = $custInfo['user_id'];
                        }
                        $this->_view->customerData = $customerDataArray;
                    }else{
                      $this->_view->noNewCusomers = $this->_translator->translate('There are any customers exist'); 
                    }
                    return $this->_view->render('customers.phtml');
                }
                if($this->_options[1] == 'orders'){
                     isset($this->_options[2]) ? $limit = $this->_options[2] : $limit = 5;
                     $lastCustomersOrders = $toasterstatsDbTable->lastNewCustomersOrders($limit);
                     $customerOrdersDataArray = array();
                     if(!empty($lastCustomersOrders)){
                        foreach($lastCustomersOrders as $key=>$custInfo){
                            if($custInfo['state'] == null){$custInfo['state'] = 0;}
                                $state = Tools_Geo::getStateById(intval($custInfo['state']));
                            if($state == ''){
                               $customerOrdersDataArray[$key]['state'] = $state; 
                            }else{
                               $customerOrdersDataArray[$key]['state'] = $state['name'];
                            }
                            $customerOrdersDataArray[$key]['fullName'] = $custInfo['full_name'];
                            $customerOrdersDataArray[$key]['city'] = $custInfo['city'];
                            $customerOrdersDataArray[$key]['total'] = $custInfo['total'];
                            $customerOrdersDataArray[$key]['userId'] = $custInfo['user_id'];
                        }
                        $this->_view->customerOrdersData = $customerOrdersDataArray;
                    }else{
                      $this->_view->noNewCustomersOrders = $this->_translator->translate('There are any orders exist'); 
                    }
                     return $this->_view->render('latestCustomersOrders.phtml');
                }
            }
        }
        
    }
        
    public function _makeOptionSales(){
       if(Tools_Security_Acl::isAllowed(self::RESOURCE_TOASTER_STATS)){
           if(isset($this->_options[1])){
               $todaySale = 0;
               $salesFromPeriod = 0;
               $today = 0;
               $toasterstatsDbTable = new ToasterstatsDbtable();
               if($this->_options[1]== 'today'){
                    $period = $this->_createTimePeriod(1, 'days');
                    $todaySales = $toasterstatsDbTable->selectSalesFromPeriod($period);
                    if(isset($todaySales) && $todaySales != null && !empty($todaySales)){
                        $todaySale = count($todaySales);
                    }
                    $this->_view->todaySales = $todaySale;
               }
               if($this->_options[1]== 'total'){
                    $totalSales = $toasterstatsDbTable->selectAllSales();
                    if(isset($totalSales) && $totalSales != null && !empty($totalSales)){
                        $totalSale = count($totalSales);
                    }
                    $this->_view->totalSales = $totalSale;
               }
               if(in_array($this->_options[1], $this->_periodArray)){
                   if(!isset($this->_options[2])){
                       $period = $this->_createTimePeriod(1, $this->_options[1]);
                   }
                   else{
                       $period = $this->_createTimePeriod($this->_options[2], $this->_options[1]);
                   }
                   $totalSalesPeriod = $toasterstatsDbTable->selectSalesFromPeriod($period);
                   if(isset($totalSalesPeriod) && $totalSalesPeriod != null && !empty($totalSalesPeriod)){
                        $salesFromPeriod = count($totalSalesPeriod);
                        
                   }
                   $this->_view->salesFromPeriod = $salesFromPeriod;
                }
                return $this->_view->render('sales.phtml'); 
           }
       }
    }
          
    public function _makeOptionMoney(){
        if(Tools_Security_Acl::isAllowed(self::RESOURCE_TOASTER_STATS)){
           if(isset($this->_options[1])){
               $totalMoney = 0;
               $moneyFromPeriod = 0;
               $today = 0;
               $toasterstatsDbTable = new ToasterstatsDbtable();
               if($this->_options[1]== 'today'){
                    $period = $this->_createTimePeriod(1, 'days');
                    $moneyFromToday = $toasterstatsDbTable->selectAllMoneyFromPeriod($period);
                    if(isset($moneyFromToday) && $moneyFromToday != null && $moneyFromToday[0]['count'] != null){
                        $today = $moneyFromToday[0]['count'];
                    }
                    $this->_view->moneyToday= $today;
               }
               if($this->_options[1]== 'total'){
                   $totalMoneys = $toasterstatsDbTable->selectAllMoney();
                   if(isset($totalMoneys) && $totalMoneys != null && $totalMoneys[0]['count'] != null){
                       $totalMoney =  $totalMoneys[0]['count'];
                   }
                   $this->_view->totalMoney = $totalMoney;
               }
               if(in_array($this->_options[1], $this->_periodArray)){
                   if(!isset($this->_options[2])){
                        $period = $this->_createTimePeriod(1, $this->_options[1]);
                   }
                   else{
                        $period = $this->_createTimePeriod($this->_options[2], $this->_options[1]);
                   }
                        $moneyFromPeriods = $toasterstatsDbTable->selectAllMoneyFromPeriod($period);
                        if(isset($moneyFromPeriods) && $moneyFromPeriods !=null && $moneyFromPeriods[0]['count'] != null){
                            $moneyFromPeriod =  $moneyFromPeriods[0]['count'];
                        }
                        $this->_view->moneyFromPeriod = $moneyFromPeriod;
                }
                return $this->_view->render('money.phtml'); 
                    
            }
           
        }
        
    }
    
    public function _makeOptionQuotes(){
       if(Tools_Security_Acl::isAllowed(self::RESOURCE_TOASTER_STATS)){
           if(isset($this->_options[1])){
               $totalQuote = 0; 
               $quotesFromPeriod = 0;
               $today = 0;
               $toasterstatsDbTable = new ToasterstatsDbtable();
               if($this->_options[1]== 'today'){
                    $period = $this->_createTimePeriod(1, 'days');
                    $quotesFromToday = $toasterstatsDbTable->selectQuotesFromPeriod($period);
                    if(isset($quotesFromToday) && $quotesFromToday != null && $quotesFromToday[0]['count'] != null){
                        $today = $quotesFromToday[0]['count'];
                    }
                    $this->_view->quotesToday= $today;
               }
               if($this->_options[1]== 'new'){
                    $totalQuotes = $toasterstatsDbTable->selectAllQuotes();
                    if(isset($totalQuotes) && $totalQuotes != null && $totalQuotes[0]['count'] != null){
                        $totalQuote = $totalQuotes[0]['count'];
                    }
                    $this->_view->totalQuotes = $totalQuote;
               }
               if(in_array($this->_options[1], $this->_periodArray)){
                   if(!isset($this->_options[2])){
                       $period = $this->_createTimePeriod(1, $this->_options[1]);
                   }
                   else{
                       $period = $this->_createTimePeriod($this->_options[2], $this->_options[1]);
                   }
                   $quotesFromPeriods = $toasterstatsDbTable->selectQuotesFromPeriod($period);
                   if(isset($quotesFromPeriods) && $quotesFromPeriods != null && $quotesFromPeriods[0]['count'] != null){
                        $quotesFromPeriod = $quotesFromPeriods[0]['count'];
                   }
                   $this->_view->quotesFromPeriod = $quotesFromPeriod;
                }
                return $this->_view->render('quotes.phtml'); 
           }
       }
    }
    
    public function _makeOptionOrders(){
       if(Tools_Security_Acl::isAllowed(self::RESOURCE_TOASTER_STATS)){
           if(isset($this->_options[1])){
               $totalOrder = 0; 
               $todayOrder = 0;
               $lastOrder = 0;
               $orderFromPeriod = 0;
               $toasterstatsDbTable = new ToasterstatsDbtable();
               if($this->_options[1]== 'today'){
                    $period = $this->_createTimePeriod(1, 'days');
                    $todayOrders = $toasterstatsDbTable->selectAverageOrdersFromPeriod($period);
                    if(isset($todayOrders) && $todayOrders != null && $todayOrders['0']['count'] != null){
                        $todayOrder = $todayOrders['0']['count'];
                    }
                    $this->_view->todayOrders = $todayOrder; 
               }
               if($this->_options[1]== 'last'){
                    $lastOrders = $toasterstatsDbTable->selectLastOrder();
                    if(isset($lastOrders) && $lastOrders != null && $lastOrders['0']['count'] != null){
                        $lastOrder = $lastOrders['0']['count'];
                    }
                    $this->_view->lastOrders = $lastOrder; 
                    
               }
               if($this->_options[1]== 'total'){
                    $evarageOrders = $toasterstatsDbTable->selectAverageTotalOrder();
                    if(isset($evarageOrders) && $evarageOrders != null && $evarageOrders[0]['count'] != null){
                        $totalOrder =  $evarageOrders[0]['count'];
                    }
                    $this->_view->totalOrder = $totalOrder; 
               }
               if(in_array($this->_options[1], $this->_periodArray)){
                   if(!isset($this->_options[2])){
                       $period = $this->_createTimePeriod(1, $this->_options[1]);
                   }
                   else{
                       $period = $this->_createTimePeriod($this->_options[2], $this->_options[1]);
                   }
                   $evarageOrdersPeriod = $toasterstatsDbTable->selectAverageOrdersFromPeriod($period);
                   if(isset($evarageOrdersPeriod) && $evarageOrdersPeriod != null && $evarageOrdersPeriod[0]['count'] != null){
                        $orderFromPeriod =  $evarageOrdersPeriod[0]['count'];
                   }
                   $this->_view->orderFromPeriod = $orderFromPeriod;
                }
                return $this->_view->render('orders.phtml'); 
           }
       }
    }
    
    
    public function _makeOptionLabel(){
        if(Tools_Security_Acl::isAllowed(self::RESOURCE_TOASTER_STATS)){
            if(isset($this->_options[1])){  
                if(isset($this->_options[2])){
                    $periodLabel = $this->_createLabelForPeriod($this->_options[1], $this->_options[2]);
                }
                else{
                    $periodLabel = $this->_createLabelForPeriod($this->_options[1], 1);
                }
                $this->_view->periodLabel = $periodLabel;
                return $this->_view->render('label.phtml');
          }
        }
   } 
        
    //controls Block//
    public function _makeOptionControl(){
        if(Tools_Security_Acl::isAllowed(self::RESOURCE_TOASTER_STATS)){
            $this->_view->translator = $this->_translator;
            $currencyHandler = Zend_Registry::get('Zend_Currency');
            $currencySymbol = $currencyHandler->getSymbol();
            $this->_view->currencySymbol = $currencySymbol;
            return $this->_view->render('controlStats.phtml');
        }
    }
    
    public function _makeOptionSalescontrol(){
         if(Tools_Security_Acl::isAllowed(self::RESOURCE_TOASTER_STATS)){ 
            $this->_view->translator = $this->_translator;
            $currencyHandler = Zend_Registry::get('Zend_Currency');
            $currencySymbol = $currencyHandler->getSymbol();
            $this->_view->currencySymbol = $currencySymbol;
            return $this->_view->render('salesControl.phtml');
         }
    }
    //controls block end//
    
    private function _changePirchartAmountGrafic($typeOfPierchartGrafic, $period, $includeTax = 0){
   
        $totalProducts = array('Nothing Found'=>'1');
        $toasterstatsDbTable = new ToasterstatsDbtable();
        if($typeOfPierchartGrafic == 'product'){
            if($includeTax == 1){
                $productAmountByPeriod = $toasterstatsDbTable->salesamountByProduct($period);
            }
            else{
                $productAmountByPeriod = $toasterstatsDbTable->salesamountByProductWithoutTax($period);
            }
            if(!empty($productAmountByPeriod)){
               $totalProducts = $this->_calculatePierchartsResult($productAmountByPeriod);
            }
        }
        if($typeOfPierchartGrafic == 'brand'){
            if($includeTax == 1){
                $productAmountByPeriod = $toasterstatsDbTable->salesamountByBrand($period);
            }
            else{
                $productAmountByPeriod = $toasterstatsDbTable->salesamountByBrandWithoutTax($period);
            }
            if(!empty($productAmountByPeriod)){
                   $totalProducts = $this->_calculatePierchartsResult($productAmountByPeriod);
            }
        }
        if($typeOfPierchartGrafic == 'tag'){
            if($includeTax == 1){
                $productAmountByPeriod = $toasterstatsDbTable->salesamountByTag($period);
            }
            else{
                $productAmountByPeriod = $toasterstatsDbTable->salesamountByTagWitoutTax($period);
            }
            if(!empty($productAmountByPeriod)){
                   $totalProducts = $this->_calculatePierchartsResult($productAmountByPeriod);
            }
        }
        if($typeOfPierchartGrafic == 'customer'){
            if($includeTax == 1){
                $totalCutomersFromPeriod = $toasterstatsDbTable->salesamountByCustomer($period);
            }
            else{
                $totalCutomersFromPeriod = $toasterstatsDbTable->salesamountByCustomerWithoutTax($period);
            }
            if(!empty($totalCutomersFromPeriod)){
                $customerResult = array();
                foreach($totalCutomersFromPeriod as $information){
                    if(!isset($customerResult[$information['name']])){
                        $customerResult[$information['name']] = $information['count'];
                    }
                    else{
                        $customerResult[$information['name']] = $customerResult[$information['name']] + $information['count'];
                    }
                }
                $totalProducts = $customerResult;
            }
        }
        if($typeOfPierchartGrafic == 'type'){
            $totalQuotesCart = array();
            if($includeTax == 1){
                $totalQuotesFromPeriod = $toasterstatsDbTable->salesamountByTypeSalesQuotes($period);
            }
            else{
                $totalQuotesFromPeriod = $toasterstatsDbTable->salesamountByTypeSalesQuotesWithoutTax($period);
            }
            if(!empty($totalQuotesFromPeriod) && $totalQuotesFromPeriod[0]['count'] != null){
                $totalQuotesCart['quote'] = $totalQuotesFromPeriod[0]['count']; 
                $totalProducts = $totalQuotesCart;
            }
            if($includeTax == 1){
                $totalCartFromPeriod = $toasterstatsDbTable->salesamountByTypeSalesCart($period);
            }
            else{
                $totalCartFromPeriod = $toasterstatsDbTable->salesamountByTypeSalesCartWithoutTax($period);
            }
            if(!empty($totalCartFromPeriod) && $totalCartFromPeriod[0]['count'] != null){
                $totalQuotesCart['cart'] = $totalCartFromPeriod[0]['count']; 
                $totalProducts = $totalQuotesCart;
            }
            
        }
        return $totalProducts;
    }
    
    private function _calculatePierchartsResult($resultsArray){
        $brandsResult = array();
        foreach($resultsArray as $information){
              if(!isset($brandsResult[$information['name']])){
                  $brandsResult[$information['name']] = $information['count']*$information['tax_price'];
              }
              else{
                  $brandsResult[$information['name']] = $brandsResult[$information['name']] + $information['count']*$information['tax_price'];
              }
         }
         return $brandsResult;
    }
        
    public function changeDashboardDataSalesAction(){
            if(Tools_Security_Acl::isAllowed(self::RESOURCE_TOASTER_STATS)){
                if ($this->_request->isPost()) {
                    $timePeriod =  $this->_request->getParam('timePeriod');
                    $averageAmountGrafic =  $this->_request->getParam('averageAmountGrafic');
                    $countGrafic =  $this->_request->getParam('countGrafic');
                    $typeOfGraficCount = $this->_request->getParam('typeOfGraficCount');
                    $typeOfGraficAverageAmount = $this->_request->getParam('typeOfGraficAverageAmount');
                    $pierchartAmountGrafics  = $this->_request->getParam('pierchartAmountGrafics');
                    $countTagGrafic  = $this->_request->getParam('countTagGrafic');
                    $geoGraf = $this->_request->getParam('geoGraf');
                    $usingTax = $this->_request->getParam('taxesState');
                    $geoGrafData = '';
                    $pierchartGraficDataResult = '';
                    if(preg_match('/\|/',$timePeriod)){
                        $datePikerPeriod = explode('|', $timePeriod);
                        $dateFromRight = date("Y-m-d", strtotime($datePikerPeriod[0]));
                        $dateToRight = date("Y-m-d", strtotime($datePikerPeriod[1]));
                        $dateFrom = new DateTime($dateFromRight);
                        $dateTo = new DateTime($dateToRight);
                        $preparePeriod = $dateFrom->diff($dateTo);
                        $differencePeriod = $preparePeriod->days;
                        if($differencePeriod<31 && $differencePeriod != 31){
                            $unitsPeriod = 'days';
                            $quntityPeriod = $differencePeriod;
                        }
                        if($differencePeriod>30 && $differencePeriod < 365){
                            $unitsPeriod = 'month';
                            $quntityPeriod = round($differencePeriod/30);
                        }
                        if($differencePeriod > 365 ){
                            $unitsPeriod = 'year';
                            $quntityPeriod = round($differencePeriod/365);
                        }
                        $period = "between '".$dateFromRight.' 00:00:00'."' AND '".$dateToRight.' 23:59:59'."'";
                        $pierchartExcist = 0;
                        if(!empty($pierchartAmountGrafics)){
                            foreach($pierchartAmountGrafics as $value){
                                $pierchartAmountGraficsData = $this->_changePirchartAmountGrafic($value,$period, $usingTax);
                                $pierchartGraficDataResult[$value] = $pierchartAmountGraficsData;
                            }
                            $pierchartExcist = 1;
                        }
                        $countTagExcist = 0;
                        if($countTagGrafic == '1'){
                            $countTagGraficsData = $this->_createCountTagGraf($period);
                            $countTagExcist = 1;
                        }
                        if($geoGraf == '1'){
                            $geoGrafData = $this->_geoMapHandler($period);
                        }
                                        
                        $rightPeriodForGraf = $this->_rigthGrafhPeriod($unitsPeriod);
                        $date = $this->_checkDynamicPeriod($unitsPeriod, $quntityPeriod, $dateToRight);
                        $grafsData = $this->_changeDashboardDataSalesTabGrafh($averageAmountGrafic, $countGrafic, $unitsPeriod, $typeOfGraficCount, $typeOfGraficAverageAmount, $rightPeriodForGraf, $date, $period, $usingTax);                          
                        $statisticArray = array('grafData'=>$grafsData, 'typeOfGraficCount'=>ucfirst($typeOfGraficCount), 'typeOfGraficAvarageAmount'=> ucfirst($typeOfGraficAverageAmount), 'pierchartExcist' => $pierchartExcist, 'pierChartData' => $pierchartGraficDataResult, 'countTagExcist' => $countTagExcist, 'countTagGraficsData'=>$countTagGraficsData, 'geoGraf' =>$geoGraf, 'geoGrafData'=>$geoGrafData);
                    }
                    echo  json_encode($statisticArray);
                                   
                }
            }
      
        }
        
        private function _geoMapHandler($period){
             $toasterstatsDbTable = new ToasterstatsDbtable();
             $mapData = $toasterstatsDbTable->salesCustomersCountrysByPeriod($period);
             $typeOfMap = 'World';
             
             if(!empty($mapData)){
                  $usaOrWorld = $this->_checkCountry($mapData);
                  $mapResult = array();
                  if($usaOrWorld == '1'){
                     $typeOfMap  = 'USA'; 
                     $mapData = $toasterstatsDbTable->salesCustomersStatesByPeriod($period);
                     foreach($mapData as $information){
                        if(!isset($mapResult[$information['country'].'-'.$information['state']])){
                            $mapResult[$information['country'].'-'.$information['state']] = 1;
                        }
                        else{
                            $mapResult[$information['country'].'-'.$information['state']] = $mapResult[$information['country'].'-'.$information['state']] + 1;
                        }
              
                     }
                  }else{
                     $typeOfMap = 'World';
                     foreach($mapData as $information){
                        if(!isset($mapResult[$information['country']])){
                            $mapResult[$information['country']] = 1;
                        }
                        else{
                            $mapResult[$information['country']] = $mapResult[$information['country']] + 1;
                        }
              
                     }
                  }
             }
             if(!isset($mapResult)){
                 $mapResult = array('Nothing' =>0); 
             }
             return array('typeofMap' => $typeOfMap, 'mapData' => $mapResult);
        }
        
      
        private function _checkCountry($mapData){
            $resultArray =array();
            foreach($mapData as $data){
               if($data['country'] != 'US'){
                   return 0;
               }
                                 
            }
            return 1;
            
        }
        
        
        private function _changeDashboardDataSalesTabGrafh($averageAmountGrafic, $countGrafic, $unitsPeriod, $typeOfGraficCount, $typeOfGraficAverageAmount, $rightPeriodForGraf, $date, $period, $usingTax = 0){
             $grafDatas = array('countGraficExcist'=>0, 'averageAmountGraficExcist'=>0, 'countGraficData' => '', 'averageAmountGraficData' => '');
             if($averageAmountGrafic == 1){
                $averageAmountGraficData =  $this->_createGraficsData('averageamount', $typeOfGraficAverageAmount, $period, $rightPeriodForGraf, $date, $usingTax);
                if(count($averageAmountGraficData)>1){
                    if($unitsPeriod != 'year'){  
                        $averageAmountGraficData = array_reverse($averageAmountGraficData);
                    }
                }
                $grafDatas['averageAmountGraficExcist'] = 1;
                $grafDatas['averageAmountGraficData'] = $averageAmountGraficData; 
             }
             if($countGrafic == 1){
                $countGraficData =  $this->_createGraficsData('count', $typeOfGraficCount, $period, $rightPeriodForGraf, $date, $usingTax);
                if(count($countGraficData)>1){
                    if($unitsPeriod != 'year'){ 
                        $countGraficData = array_reverse($countGraficData);
                    }
                }
                $grafDatas['countGraficExcist'] = 1;
                $grafDatas['countGraficData'] = $countGraficData;
             }
             return $grafDatas;
         
        }
               
        public function changeDashboardDataAction(){
            if(Tools_Security_Acl::isAllowed(self::RESOURCE_TOASTER_STATS)){
                if ($this->_request->isPost()) {
                    $timePeriod =  $this->_request->getParam('timePeriod');
                    $amountGrafic =  $this->_request->getParam('amountGrafic');
                    $countGrafic =  $this->_request->getParam('countGrafic');
                    $typeOfGraficCount = $this->_request->getParam('typeOfGraficCount');
                    $typeOfGraficAmount = $this->_request->getParam('typeOfGraficAmount');
                    $productTable = $this->_request->getParam('productTable');
                    $periodLabel = $this->_request->getParam('periodLabel');
                    $limitForBestsselers = $this->_request->getParam('limitForBestsselers');
                    $amountGraficData = array('0'=>array('created_at'=>''));
                    $countGraficData = array('0'=>array('created_at'=>''));
                    $countGraficResult = array('excist'=>'0', 'data'=>$countGraficData);
                    $amountGraficResult = array('excist'=>'0', 'data'=>$amountGraficData);
                    $productTableData = array(0=>array('name'=>'Nothing found'));
                    if(preg_match('/month|days|year|week|totalPeriod/', $timePeriod)){
                        $quntityPeriod = preg_replace('/month|days|year|week|totalPeriod/', '', $timePeriod);
                        $unitsPeriod = preg_replace('/\d/', '', $timePeriod); 
                        if(in_array($unitsPeriod, $this->_periodArray)){
                            $toasterstatsDbTable = new ToasterstatsDbtable();
                            $salesFromPeriod = 0;
                            $moneyFromPeriod = 0;
                            $orderFromPeriod = 0;
                            $currencyHandler = Zend_Registry::get('Zend_Currency');
                            $grafsData = $this->_changeDashboardDataGrafh($amountGrafic, $countGrafic, $unitsPeriod, $quntityPeriod, $typeOfGraficCount, $typeOfGraficAmount);                          
                            if($unitsPeriod == 'totalPeriod'){
                                $totalSales = $toasterstatsDbTable->selectAllSales();
                                
                                if(isset($totalSales) && $totalSales != null && !empty($totalSales)){
                                    $salesFromPeriod = count($totalSales);
                                }
                                $totalMoneys = $toasterstatsDbTable->selectAllMoney();
                                if(isset($totalMoneys) && $totalMoneys != null && $totalMoneys[0]['count'] != null){
                                    $moneyFromPeriod =  $totalMoneys[0]['count'];
                                }
                                $evarageOrders = $toasterstatsDbTable->selectAverageTotalOrder();
                                if(isset($evarageOrders) && $evarageOrders != null && $evarageOrders[0]['count'] != null){
                                    $orderFromPeriod =  $evarageOrders[0]['count'];
                                }
                                if($productTable ==1){
                                    $productTop = $toasterstatsDbTable->mostSaledProductsAllTime($limitForBestsselers);
                                    $productTableData = $this->_prepareProducts($productTop);
                                }
                                
                            }
                            else{
                                $period = $this->_createTimePeriod($quntityPeriod, $unitsPeriod);
                                $totalSalesPeriod = $toasterstatsDbTable->selectSalesFromPeriod($period);
                                if(isset($totalSalesPeriod) && $totalSalesPeriod != null && !empty($totalSalesPeriod)){
                                    $salesFromPeriod = count($totalSalesPeriod);
                                }
                                $moneyFromPeriods = $toasterstatsDbTable->selectAllMoneyFromPeriod($period);
                                if(isset($moneyFromPeriods) && $moneyFromPeriods !=null && $moneyFromPeriods[0]['count'] != null){
                                    $moneyFromPeriod =  $moneyFromPeriods[0]['count'];
                                }
                                $evarageOrdersPeriod = $toasterstatsDbTable->selectAverageOrdersFromPeriod($period);
                                if(isset($evarageOrdersPeriod) && $evarageOrdersPeriod != null && $evarageOrdersPeriod[0]['count'] != null){
                                    $orderFromPeriod =  $evarageOrdersPeriod[0]['count'];
                                }
                                if($productTable ==1){
                                    $productTop = $toasterstatsDbTable->mostSaledProducts($period,$limitForBestsselers);
                                    $productTableData = $this->_prepareProducts($productTop);
                                }
                            }
                            $statisticArray = array('salesFromPeriod' =>$salesFromPeriod, 'moneyFromPeriod' => $currencyHandler->toCurrency($moneyFromPeriod), 
                                'orderFromPeriod' =>$currencyHandler->toCurrency($orderFromPeriod), 'grafData'=>$grafsData, 'typeOfGraficCount'=>ucfirst($typeOfGraficCount), 
                                'typeOfGraficAmount'=> ucfirst($typeOfGraficAmount), 'periodLabel'=>$periodLabel, 'productTable'=>$productTable, 'productTableData'=>$productTableData);
                            echo  json_encode($statisticArray);
                            
                        }
                          
                    }
                
                }
            }
        }
        
    private function _changeDashboardDataGrafh($amountGrafic, $countGrafic, $unitsPeriod, $quntityPeriod, $typeOfGraficCount, $typeOfGraficAmount, $usingTax = 0){
         if($unitsPeriod != 'totalPeriod'){
             $rightPeriodForGraf = $this->_rigthGrafhPeriod($unitsPeriod);
             $date = $this->_checkPeriod($unitsPeriod, $quntityPeriod);
             $period = $this->_createTimePeriod($quntityPeriod, $unitsPeriod);
             $grafDatas = array('countGraficExcist'=>0, 'amountGraficExcist'=>0, 'countGraficData' => '', 'amountGraficData' => '');
             if($amountGrafic == 1){
                $amountGraficData =  $this->_createGraficsData('amount', $typeOfGraficAmount, $period, $rightPeriodForGraf, $date, $usingTax);
                if(count($amountGraficData)>1){
                    if($unitsPeriod != 'year'){  
                        $amountGraficData = array_reverse($amountGraficData);
                    }
                }
                $grafDatas['amountGraficExcist'] = 1;
                $grafDatas['amountGraficData'] = $amountGraficData; 
             }
             if($countGrafic == 1){
                $countGraficData =  $this->_createGraficsData('count', $typeOfGraficCount, $period, $rightPeriodForGraf, $date, $usingTax);
                if(count($countGraficData)>1){
                    if($unitsPeriod != 'year'){ 
                        $countGraficData = array_reverse($countGraficData);
                    }
                }
                $grafDatas['countGraficExcist'] = 1;
                $grafDatas['countGraficData'] = $countGraficData;
             }
             return $grafDatas;
         }
         else{
             $unitsPeriod = 'year';
             $rightPeriodForGraf = $this->_rigthGrafhPeriod($unitsPeriod);
             $toasterstatsDbTable = new ToasterstatsDbtable();
             $totalSales = $toasterstatsDbTable->selectAllSales();
             $totalQuotes = $toasterstatsDbTable->selectAllQuotes();
             $periodsArray = array();
             foreach($totalSales as $sale){
                 $periodsArray[$sale['created_at']] = '';
             }
             foreach($totalQuotes as $quote){
                 $periodsArray[$quote['created_at']] = '';
             }
             $minPeriodDate = array_search(min($periodsArray), $periodsArray);
             $totalPeriod = substr($minPeriodDate, 0, $rightPeriodForGraf);
             $now = date('Y');
             if($now == $totalPeriod){
                $totalPeriod = $totalPeriod - 1;
             }
             $quntityPeriod = $now - $totalPeriod;
             $period = $this->_createTimePeriod($quntityPeriod, $unitsPeriod);
             for($i = 0, $j=$totalPeriod; $j<$now; $i++, $j++){
                   $date[date('Y', strtotime('-'.$i.' year'))] = array('sales' => 0, 'quotes' => 0);
                   
             }
             if($amountGrafic == 1){
                $amountGraficData =  $this->_createGraficsData('amount', $typeOfGraficAmount, $period, $rightPeriodForGraf, $date);
                if(count($amountGraficData)>1){
                     $amountGraficData = array_reverse($amountGraficData);
                }
                $grafDatas['amountGraficExcist'] = 1;
                $grafDatas['amountGraficData'] = $amountGraficData; 
             }
             if($countGrafic == 1){
                $countGraficData =  $this->_createGraficsData('count', $typeOfGraficCount, $period, $rightPeriodForGraf, $date);
                if(count($countGraficData)>1){
                     $countGraficData = array_reverse($countGraficData);
                }
                $grafDatas['countGraficExcist'] = 1;
                $grafDatas['countGraficData'] = $countGraficData;
             }
             return $grafDatas;
       }
           
    }
       
   private function _createGraficsData($typeGrafic, $typeColumn, $period, $rightPeriodForGraf, $date, $usingTax = 0){
        $toasterstatsDbTable = new ToasterstatsDbtable();
        if(preg_match('/\|/', $typeColumn)){
            $typeOfGraf  = explode('|', $typeColumn);
             if($typeGrafic == 'count'){
                 foreach($typeOfGraf as $type){
                     if($type== 'sales'){
                          $totalSalesPeriod = $toasterstatsDbTable->selectSalesFromPeriod($period);
                          if(isset($totalSalesPeriod) && $totalSalesPeriod != null && !empty($totalSalesPeriod)){
                                $totalSales = $totalSalesPeriod;
                                foreach($totalSales as $key => $value){
                                    $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                                    $date[$temDate]['sales']++;
                                }
                           }
                     }
                     if($type== 'quotes'){
                          $totalQuotesPeriod = $toasterstatsDbTable->selectQuotesFromPeriodWithoutStatus($period);
                          if(isset($totalQuotesPeriod) && $totalQuotesPeriod != null && !empty($totalQuotesPeriod)){
                                $totalQuotes = $totalQuotesPeriod;
                                foreach($totalQuotes as $key => $value){
                                    $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                                    $date[$temDate]['quotes']++;
                                }
                         }
                    }
                 }
             }
             if($typeGrafic == 'amount'){
                foreach($typeOfGraf as $type){
                    if($type== 'sales'){
                        $totalSalesPeriod = $toasterstatsDbTable->selectAmountFromPeriod($period);
                        if(isset($totalSalesPeriod) && $totalSalesPeriod != null && !empty($totalSalesPeriod)){
                             $totalSales = $totalSalesPeriod;
                             foreach($totalSales as $key => $value){
                                 $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                                 $date[$temDate]['sales'] = $date[$temDate]['sales']+$value['count'];
                             }
                        }
                    }
                    if($type== 'quotes'){
                          $totalQuotesPeriod = $toasterstatsDbTable->selectAllQuotesAmountFromPeriod($period);
                          if(isset($totalQuotesPeriod) && $totalQuotesPeriod != null && !empty($totalQuotesPeriod)){
                                $totalQuotes = $totalQuotesPeriod;
                                foreach($totalQuotes as $key => $value){
                                    $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                                    $date[$temDate]['quotes'] = $date[$temDate]['quotes']+$value['count'];
                                }
                         }
                    }
                }
            }
            if($typeGrafic == 'averageamount'){
              foreach($typeOfGraf as $type){
                   if($type == 'sales'){
                        if($usingTax == 1){
                            $totalSalesPeriod = $toasterstatsDbTable->selectAmountFromPeriod($period);
                        }
                        if($usingTax == 0){
                            $totalSalesPeriod = $toasterstatsDbTable->selectAmountFromPeriodWithoutTax($period);
                        }
                        if(isset($totalSalesPeriod) && $totalSalesPeriod != null && !empty($totalSalesPeriod)){
                            $totalSales = $totalSalesPeriod;
                            $date1 = $date;
                            foreach($totalSales as $key => $value){
                                $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                                $date1[$temDate]['sales'] = $date1[$temDate]['sales']+$value['count'];
                            }
                            $date2 = $date;
                            foreach($totalSales as $key => $value){
                                $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                                $date2[$temDate]['sales']++;
                            }
                            foreach($date1 as $key=>$salesInformation){
                                if($date2[$key]['sales'] != 0){
                                    $date[$key]['sales'] = $date1[$key]['sales']/$date2[$key]['sales'];
                                }
                            }
                        }
                   
                   
                }
              if($type == 'quotes'){
                   if($usingTax == 1){
                        $totalQuotesPeriod = $toasterstatsDbTable->selectAllQuotesAmountFromPeriod($period);
                   }
                   if($usingTax == 0){
                       $totalQuotesPeriod = $toasterstatsDbTable->selectAllQuotesAmountFromPeriodWithoutTax($period);
                   }
                   if(isset($totalQuotesPeriod) && $totalQuotesPeriod != null && !empty($totalQuotesPeriod)){
                       $totalQuotes = $totalQuotesPeriod;
                       $date1 = $date;
                       foreach($totalQuotes as $key => $value){
                           $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                           $date1[$temDate]['quotes'] = $date1[$temDate]['quotes']+$value['count'];
                       }
                       $date2 = $date;
                       foreach($totalQuotes as $key => $value){
                           $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                           $date2[$temDate]['quotes']++;
                       }
                   
                       foreach($date1 as $key=>$salesInformation){
                            if($date2[$key]['quotes'] != 0){
                                $date[$key]['quotes'] = $date1[$key]['quotes']/$date2[$key]['quotes'];
                            }
                       }
                  }
              }     
          }
                                       
         }
        }
         else{
             if($typeGrafic == 'count'){
                 if($typeColumn== 'sales'){
                    $totalSalesPeriod = $toasterstatsDbTable->selectSalesFromPeriod($period);
                    if(isset($totalSalesPeriod) && $totalSalesPeriod != null && !empty($totalSalesPeriod)){
                        $totalSales = $totalSalesPeriod;
                        foreach($totalSales as $key => $value){
                            $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                            $date[$temDate]['sales']++;
                        }
                    }
                 }
                 if($typeColumn == 'quotes'){
                       $totalQuotesPeriod = $toasterstatsDbTable->selectQuotesFromPeriodWithoutStatus($period);
                       if(isset($totalQuotesPeriod) && $totalQuotesPeriod != null && !empty($totalQuotesPeriod)){
                            $totalQuotes = $totalQuotesPeriod;
                            foreach($totalQuotes as $key => $value){
                                $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                                 $date[$temDate]['quotes']++;
                            }
                       }
                 }
          }
          if($typeGrafic == 'amount'){
              if($typeColumn== 'sales'){
                   $totalSalesPeriod = $toasterstatsDbTable->selectAmountFromPeriod($period);
                   if(isset($totalSalesPeriod) && $totalSalesPeriod != null && !empty($totalSalesPeriod)){
                         $totalSales = $totalSalesPeriod;
                         foreach($totalSales as $key => $value){
                             $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                             $date[$temDate]['sales'] = $date[$temDate]['sales']+$value['count'];
                         }
                   }
              }
              if($typeColumn == 'quotes'){
                   $totalQuotesPeriod = $toasterstatsDbTable->selectAllQuotesAmountFromPeriod($period);
                   if(isset($totalQuotesPeriod) && $totalQuotesPeriod != null && !empty($totalQuotesPeriod)){
                       $totalQuotes = $totalQuotesPeriod;
                       foreach($totalQuotes as $key => $value){
                           $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                           $date[$temDate]['quotes'] = $date[$temDate]['quotes']+$value['count'];
                       }
                   }
              }
          }
          if($typeGrafic == 'averageamount'){
              if($typeColumn== 'sales'){
                   if($usingTax == 1){
                         $totalSalesPeriod = $toasterstatsDbTable->selectAmountFromPeriod($period);
                   }
                   if($usingTax == 0){
                         $totalSalesPeriod = $toasterstatsDbTable->selectAmountFromPeriodWithoutTax($period);
                   }
                   if(isset($totalSalesPeriod) && $totalSalesPeriod != null && !empty($totalSalesPeriod)){
                         $totalSales = $totalSalesPeriod;
                         $date1 = $date;
                         foreach($totalSales as $key => $value){
                             $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                             $date1[$temDate]['sales'] = $date1[$temDate]['sales']+$value['count'];
                         }
                         $date2 = $date;
                         foreach($totalSales as $key => $value){
                            $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                            $date2[$temDate]['sales']++;
                         }
                         foreach($date1 as $key=>$salesInformation){
                            if($date2[$key]['sales'] != 0){
                                $date[$key]['sales'] = $date1[$key]['sales']/$date2[$key]['sales'];
                            }
                         }
                 }
                   
              }
              if($typeColumn == 'quotes'){
                   if($usingTax == 1){
                        $totalQuotesPeriod = $toasterstatsDbTable->selectAllQuotesAmountFromPeriod($period);
                   }
                   if($usingTax == 0){
                       $totalQuotesPeriod = $toasterstatsDbTable->selectAllQuotesAmountFromPeriodWithoutTax($period);
                   }
                   if(isset($totalQuotesPeriod) && $totalQuotesPeriod != null && !empty($totalQuotesPeriod)){
                       $totalQuotes = $totalQuotesPeriod;
                       $date1 = $date;
                       foreach($totalQuotes as $key => $value){
                           $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                           $date1[$temDate]['quotes'] = $date1[$temDate]['quotes']+$value['count'];
                       }
                       $date2 = $date;
                       foreach($totalQuotes as $key => $value){
                            $temDate = substr($value['created_at'], 0, $rightPeriodForGraf);
                            $date2[$temDate]['quotes']++;
                       }
                       foreach($date1 as $key=>$salesInformation){
                            if($date2[$key]['quotes'] != 0){
                                $date[$key]['quotes'] = $date1[$key]['quotes']/$date2[$key]['quotes'];
                            }
                       }
                   }
              }
          }
      }
      return $date;
    }
    
    private function _createTimePeriod($period, $periodUnits){
		$now = date('Y-m-d H:i:s');
        if($period == '1'){
            switch ($periodUnits) {
                case 'days':
                    $endDate = date('Y-m-d');
                    break;
                case 'week':
                    $endDate = date('Y-m-d', strtotime("Last Sunday"));
                    break;
                case 'month':
                    $endDate = date('Y-m-d', strtotime("first day of this month"));
                    break;
                case 'year':
                    $endDate = date('Y-m-d', strtotime("first day of January"));
                    break;
                default:
                    $endDate = $now;
                    break;
            }
       }else{
            $period = $period - 1;
            switch ($periodUnits) {
                case 'days':
                    $endDate = date('Y-m-d', strtotime('-'.$period. 'day'));
                    break;
                case 'week':
                    $endDate = date('Y-m-d', strtotime('this week -'.$period.' week'));
                    break;
                case 'month':
                    $endDate = date('Y-m-d', strtotime('first day of this month -'.$period. 'month'));
                    break;
                case 'year':
                    $interval = strtotime('1/1 this year -'.$period. 'year');
                    $endDate = date('Y-m-d', $interval);
                	break;
                default:
                    $endDate = $now;
                    break;
            }
        }
        return "between '".$endDate.' 00:00:00'."' AND '".$now."'";
	}
    
    private function _createTimePeriodForDatepicker($period, $periodUnits){
		$now = date('Y-m-d');
        if($period == '1'){
            switch ($periodUnits) {
                case 'days':
                    $endDate = date('Y-m-d');
                    break;
                case 'week':
                    $endDate = date('Y-m-d', strtotime("Last Sunday"));
                    break;
                case 'month':
                    $endDate = date('Y-m-d', strtotime("first day of this month"));
                    break;
                case 'year':
                    $endDate = date('Y-m-d', strtotime("first day of January"));
                    break;
                default:
                    $endDate = $now;
                    break;
            }
            
        }else{
            $period = $period - 1;
            switch ($periodUnits) {
                case 'days':
                    $endDate = date('Y-m-d', strtotime('-'.$period. 'day'));
                    break;
                case 'week':
                    $endDate = date('Y-m-d', strtotime('this week -'.$period.' week'));
                    break;
                case 'month':
                    $endDate = date('Y-m-d', strtotime('first day of this month -'.$period. 'month'));
                    break;
                case 'year':
                    $interval = strtotime('1/1 this year -'.$period. 'year');
                    $endDate = date('Y-m-d', $interval);
                	break;
                default:
                    $endDate = $now;
                    break;
            }
        }
        return $endDate.'|'.$now;
	}
    
    private function _rigthGrafhPeriod($periodUnits){
        switch ($periodUnits) {
                case 'days':
                    $rightPeriod = 10;
                    break;
                case 'week':
                    $rightPeriod = 10;
                    break;
                case 'month':
                    $rightPeriod = 7;
                    break;
                case 'year':
                    $rightPeriod = 4;
                    break;
            }
        return $rightPeriod;
        
    }
      
    private function _checkPeriod($periodUnits, $period){
        switch ($periodUnits) {
                case 'days':
                    if($period == '1'){
                        $period = 2;
                    }
                    for($i = 0; $i<$period;$i++){
                         $date[date('Y-m-d', strtotime('-'.$i.' day'))] = array('sales' => 0, 'quotes' => 0);
                    }
                    break;
                case 'week':
                    $period = $period*7;
                    for($i = 0; $i<$period;$i++){
                        $date[date('Y-m-d',  strtotime('-'.$i.' day'))] = array('sales' => 0, 'quotes' => 0);
                    }
                    break;
                case 'month':
                    for($i = 0; $i<$period;$i++){
                        $date[date('Y-m', strtotime('-'.$i.' month'))] = array('sales' => 0, 'quotes' => 0);
                    }
                    break;
                case 'year':
                    if($period == '1'){
                        $date[date('Y', strtotime("first day of January"))] = array('sales' => 0, 'quotes' => 0);
                    }
                    else{
                        for($i = 0; $i<$period;$i++){
                            $date[date('Y', strtotime('-'.$period.' year'))] = array('sales' => 0, 'quotes' => 0);
                        }
                    }
                    break;
            }
        return $date;
                    
    }
    
    private function _checkDynamicPeriod($periodUnits, $period, $endDate){
        if($period == 0){
            $period = $period+1;
        }
        if($period == 1 || $period>1){
            $period = $period+1;
        }
        switch ($periodUnits) {
                case 'days':
                    for($i = 0; $i<$period;$i++){
                        $date[date('Y-m-d', strtotime(date("Y-m-d", strtotime($endDate)) . " -$i day"))] = array('sales' => 0, 'quotes' => 0);
                    }
                    break;
                case 'month':
                    for($i = 0; $i<$period;$i++){
                        $date[date('Y-m', strtotime(date("Y-m-d", strtotime($endDate)) . " -$i month"))] = array('sales' => 0, 'quotes' => 0);
                    }
                    break;
                case 'year':
                    if($period == '1'){
                        $date[date('Y', strtotime("first day of January"))] = array('sales' => 0, 'quotes' => 0);
                    }
                    else{
                        for($i = 0; $i<$period;$i++){
                            $date[date('Y', strtotime(date("Y-m-d", strtotime($endDate)) . " -$i year"))] = array('sales' => 0, 'quotes' => 0);
                        }
                    }
                    break;
              
            }
        return $date;
                    
    }
    
    private function _createLabelForPeriod($unitsPeriod, $period){
        if($unitsPeriod == 'days' && $period == 1){
            $periodLabel = 'Today';
        }
        if($unitsPeriod != 'days' && $period == 1){
            $periodLabel = 'This '.$unitsPeriod;
        }
        if($unitsPeriod != 'days' && $period != 1){
            $periodLabel = 'Past '.$period.' '.$unitsPeriod.'s';
        }
        if($unitsPeriod == 'days' && $period != 1){
            $periodLabel = 'Past '.$period.' '.$unitsPeriod;
        }
        return $periodLabel;
    }
    
    ////////Site Statistic Block////////
    
    private function _quantityOfPages(){
         $pageMapper = Application_Model_Mappers_PageMapper::getInstance();
         $tosterStatsDbTable = new ToasterstatsDbtable();
         $staticMenuPages = $tosterStatsDbTable->quantityOfStaticMenuPages();
         $allNomenuPages = $tosterStatsDbTable->quantityOfNomenuPages();
         $allDraftPages = $pageMapper->fetchAllDraftPages();
         $quantityPages  = 0;
         $quantityDraftPages = 0;
         if(isset($staticMenuPages[0]['id']) && !empty($staticMenuPages) && $staticMenuPages != null){
            $quantityPages = $quantityPages + $staticMenuPages[0]['id'];
         }
         if(isset($allNomenuPages[0]['id']) && !empty($allNomenuPages) && $allNomenuPages != null){
            $quantityPages = $quantityPages + $allNomenuPages[0]['id'];
         }
         if(isset($allDraftPages) && !empty($allDraftPages) && $allDraftPages != null){
             $quantityDraftPages = count($allDraftPages);
         }
         return array('quantityPages'=>$quantityPages, 'quantityDraftPages'=>$quantityDraftPages);
        
    }
    
    private function _quantityBrands(){
        $quantityBrands  = 0;
        $brandMapper = Models_Mapper_Brand::getInstance();
        $brands = $brandMapper->fetchAll();
        if(isset($brands) && !empty($brands) && $brands != null){
            $quantityBrands = count($brands);
        }
        return $quantityBrands;
    }
    
    private function _quantityProducts(){
        $quantityProducts  = 0;
        $tosterStatsDbTable = new ToasterstatsDbtable();
        $product = $tosterStatsDbTable->quantityOfProducts();
        if(isset($product[0]['id']) && !empty($product) && $product != null){
              $quantityProducts = $product[0]['id'];
        }
        return $quantityProducts;
    }
    
    private function _pluginInformation(){
        $pluginMapper = Application_Model_Mappers_PluginMapper::getInstance(); 
        $quantityPlugins  = 0;
        $quantityEnabledPlugins  = 0;
        $allPlugins = $pluginMapper->fetchAll();
        $enabledPlugins = $pluginMapper->findEnabled();
        if(isset($allPlugins) && !empty($allPlugins) && $allPlugins != null){
            $quantityPlugins = count($allPlugins);
        }
        if(isset($enabledPlugins) && !empty($enabledPlugins) && $enabledPlugins != null){
            $quantityEnabledPlugins = count($enabledPlugins);
        }
        return array('quantityPlugins'=>$quantityPlugins, 'quantityEnabledPlugins'=>$quantityEnabledPlugins);
        
    }
    
    private function _quantityUsers(){
        $userMapper = Application_Model_Mappers_UserMapper::getInstance();
        $selectAdmins = 'role_id="admin"';
        $allUsers = $userMapper->fetchAll();
        $allAdmins = $userMapper->fetchAll($selectAdmins);
        $quantityUsers = 0;
        $quantityAdmins = 0;
        if(isset($allUsers) && !empty($allUsers) && $allUsers != null){
            $quantityUsers = count($allUsers);
        }
        if(isset($allAdmins) && !empty($allAdmins) && $allAdmins != null){
            $quantityAdmins = count($allAdmins);
        }
        return array('quantityUsers'=>$quantityUsers, 'quantityAdmins'=>$quantityAdmins);
    }
    
    private function _quantityTemplates(){
        $templateMapper = Application_Model_Mappers_TemplateMapper::getInstance();
        $allTemplates = $templateMapper->fetchAll();
        $quantityTemplates = 0;
        if(isset($allTemplates) && !empty($allTemplates) && $allTemplates != null){
            $quantityTemplates = count($allTemplates);
        }
        return $quantityTemplates;
    }
    
    private function _quantityQuotes(){
        $quoteMapper  = Quote_Models_Mapper_QuoteMapper::getInstance();
        $allQuotes = $quoteMapper->fetchAll();
        $quantityQuotes = 0;
        if(isset($allQuotes) && !empty($allQuotes) && $allQuotes != null){
            $quantityQuotes = count($allQuotes);
        }
        return $quantityQuotes;
    }
    
    private function _quantitySales(){
        $toasterStatsDbTable  = new ToasterstatsDbtable();
        $allCompletedSales = $toasterStatsDbTable ->selectAllSales();
        $allNewSales = $toasterStatsDbTable->selectAllNewSales();
        $quantityCompletedSales = 0;
        $quantityNewSales = 0;
        if(isset($allCompletedSales) && !empty($allCompletedSales) && $allCompletedSales != null){
            $quantityCompletedSales = count($allCompletedSales);
        }
        if(isset($allNewSales) && !empty($allNewSales) && $allNewSales != null){
            $quantityNewSales = count($allNewSales);
        }
        return array('quantityCompletedSales' => $quantityCompletedSales, 'quantityNewSales'=>$quantityNewSales);
    }
    
    public function _makeOptionSitestatistic(){
        if(Tools_Security_Acl::isAllowed(self::RESOURCE_TOASTER_STATS)){
              $quantityPages = $this->_quantityOfPages();
              $quantityBrands = $this->_quantityBrands();
              $quantityProducts = $this->_quantityProducts();
              $quantityPlugins = $this->_pluginInformation();
              $quantityUsers = $this->_quantityUsers();
              $quantityTemplates = $this->_quantityTemplates();
              $quantityQuotes = $this->_quantityQuotes();
              $quantitySales = $this->_quantitySales();
              $this->_view->quantityPages = $quantityPages['quantityPages']; 
              $this->_view->quantityDraftPages = $quantityPages['quantityDraftPages'];
              $this->_view->quantityBrands = $quantityBrands;
              $this->_view->quantityProducts = $quantityProducts;
              $this->_view->quantityPlugins = $quantityPlugins['quantityPlugins'];
              $this->_view->quantityEnabledPlugins = $quantityPlugins['quantityEnabledPlugins'];
              $this->_view->quantityUsers = $quantityUsers['quantityUsers'];
              $this->_view->quantityAdmins = $quantityUsers['quantityAdmins'];
              $this->_view->quantityTemplates = $quantityTemplates;
              $this->_view->quantityQuotes = $quantityQuotes;
              $this->_view->quantityCompletedSales = $quantitySales['quantityCompletedSales'];
              $this->_view->quantityNewSales = $quantitySales['quantityNewSales'];
              $this->_view->translator = $this->_translator;
              return $this->_view->render('table.phtml');
        }
    }
    
    ////////Site Statistic Block End////////
}


