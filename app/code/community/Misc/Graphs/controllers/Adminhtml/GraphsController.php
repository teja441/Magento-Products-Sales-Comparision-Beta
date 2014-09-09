<?php

class Misc_Graphs_Adminhtml_GraphsController extends Mage_Adminhtml_Controller_Action {
    
    /**
     * Axis labels
     *
     * @var array
     */
    protected $_axisLabels = array();

    /**
     * Simple encoding chars
     *
     * @var string
     */
    protected $_simpleEncoding = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    /**
     * Extended encoding chars
     *
     * @var string
     */
    protected $_extendedEncoding = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';


	public function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('report/graphs');

        return $this;
    }

	public function indexAction()
	{	
       	    $this->_initAction();

			$this->_addContent($this->getLayout()->createBlock('graphs/adminhtml_graph'));
			$this->_addContent($this->getLayout()->createBlock('graphs/adminhtml_graphresponse'));		
			$this->renderLayout();
		
	}

	public function getcategoriesAction()
	{	

	$storeId=$this->getRequest()->getPost('storeid');

	$categories=Mage::Helper('graphs')->getCategoryNames($storeId);

	echo $categories;
		
	}

	public function getproductsAction()
	{	

	$categoryId=$this->getRequest()->getPost('category');

	$products=Mage::Helper('graphs')->getProductNames($categoryId);

	echo $products;
		
	}

	public function getgraphAction(){

		/*
		 * visit https://developers.google.com/chart/image/docs/chart_params to know
		 * more about the chart params
		 *  
		*/
       $params = array(
            'cht'  => 'lc',    					// chart type
            'chf'  => 'bg,s,f4f4f4|c,lg,90,ffffff,0.1,ededed,0', //background fills for the chart
         //   'chm'  => 'B,f4d4b2,0,0,0|B,FF0000,1,1,0|B,00FF00,2,2,0', 		// line fills
            'chco' => 'db4814,1919D1',					//Series Colors
            'chs'  => '587x300',				// chart size <width>x<height>
			'chxt' => 'x,y',					// visible axes
			//'chof' => 'validate'				// output format (png,gif,json,html{when chof='validate'})
        );
        
	$post=$this->getRequest()->getPost();
	
	
	$timezoneLocal = Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);

		list ($dateStart, $dateEnd) = Mage::getResourceModel('reports/order_collection')
																->getDateRange($post['range'], '', '', true);

        $dateStart->setTimezone($timezoneLocal);
        $dateEnd->setTimezone($timezoneLocal);
	
		$dates = array();
        $datas = array();

        while($dateStart->compare($dateEnd) < 0){
            switch ($post['range']) {
                case '24h':
                    $d = $dateStart->toString('yyyy-MM-dd HH:00');
                    $dateStart->addHour(1);
                    break;
                case '7d':
                case '1m':
                    $d = $dateStart->toString('yyyy-MM-dd');
                    $dateStart->addDay(1);
                    break;
                case '1y':
                case '2y':
                    $d = $dateStart->toString('yyyy-MM');
                    $dateStart->addMonth(1);
                    break;
            }

            $dates[] = $d;
        }

		$graphData=array();
		
		$product_list=array('product1'=>$post['product'],
							'product2'=>$post['compare2']
							);	
		$graphData=Mage::Helper('graphs')->getGraphData($dates,$product_list);

        /**
         * setting skip step
         */
         if (count($dates) > 8 && count($dates) < 15) {
            $c = 1;
        } else if (count($dates) >= 15){
            $c = 2;
        } else {
            $c = 0;
        }
        /**
         * skipping some x labels for good reading
         */
        $i=0;
        foreach ($dates as $k => $d) {
            if ($i == $c) {
                $dates[$k] = $d;
                $i = 0;
            } else {
                $dates[$k] = '';
                $i++;
            }
        }
        
        $this->_axisLabels['x'] = $dates;
		$this->_axisLabels['y'] = $graphData['quantity'][$post['product']];
		
		foreach ($graphData['quantity'] as $index => $serie) {
            $localmaxlength[$index] = sizeof($serie);
            $localmaxvalue[$index] = max($serie);
            $localminvalue[$index] = min($serie);
        }
        
		$maxvalue = max($localmaxvalue);
		$minvalue = min($localminvalue);

       $yrange = 0;
        $yLabels = array();
        $miny = 0;
        $maxy = 0;
        $yorigin = 0;

        $maxlength = max($localmaxlength);
        if ($minvalue >= 0 && $maxvalue >= 0) {
            $miny = 0;
            if ($maxvalue > 10) {
                $p = pow(10, $this->_getPow($maxvalue));
                $maxy = (ceil($maxvalue/$p))*$p;
                $yLabels = range($miny, $maxy, $p);
            } else {
                $maxy = ceil($maxvalue+1);
                $yLabels = range($miny, $maxy, 1);
            }
            $yrange = $maxy;
            $yorigin = 0;
        }

        
			
			$params['chd'] = "e:";
            $dataDelimiter = "";
            $dataSetdelimiter = ",";
            $dataMissing = "__";
            
			// EXTENDED ENCODING
			foreach($product_list as $productid){
				$chartdata = array();	
                for ($j = 0; $j < sizeof($graphData['quantity'][$productid]); $j++) {
                    $currentvalue = $graphData['quantity'][$productid][$j];
                    
                    if (is_numeric($currentvalue)) {
                        if ($yrange) {
                         $ylocation = (4095 * ($yorigin + $currentvalue) / $yrange);

                        } else {
                          $ylocation = 0;
                        }
                        $firstchar = floor($ylocation / 64);
                        $secondchar = $ylocation % 64;
                        $mappedchar = substr($this->_extendedEncoding, $firstchar, 1)
                            . substr($this->_extendedEncoding, $secondchar, 1);
                        array_push($chartdata, $mappedchar . $dataDelimiter);
                    } else {
                        array_push($chartdata, $dataMissing . $dataDelimiter);
                    }

			  }
			        $buffer = implode('', $chartdata);			        
					$buffer = rtrim($buffer, $dataSetdelimiter);
					$buffer = rtrim($buffer, $dataDelimiter);
					$buffer = str_replace(($dataDelimiter . $dataSetdelimiter), $dataSetdelimiter, $buffer);
					
					$params['chd'] .= $buffer.',';
					$buffer=null;
					$chartdata=null;
				}
					$labelBuffer = "";
					$valueBuffer = array();
					$rangeBuffer = "";
 
        if (sizeof($this->_axisLabels) > 0) {
            $params['chxt'] = implode(',', array_keys($this->_axisLabels));
            $indexid = 0;
            foreach ($this->_axisLabels as $idx=>$labels){
				
                if ($idx == 'x') {
                    /**
                     * Format date
                     */
                    foreach ($this->_axisLabels[$idx] as $_index=>$_label) {
                        if ($_label != '') {
                            switch ($post['range']) {
                                case '24h':
                                    $this->_axisLabels[$idx][$_index] = $this->formatTime(
                                        new Zend_Date($_label, 'yyyy-MM-dd HH:00'), 'short', false
                                    );
                                    break;
                                case '7d':
                                case '1m':
                                    $this->_axisLabels[$idx][$_index] = $this->formatDate(
                                        new Zend_Date($_label, 'yyyy-MM-dd')
                                    );
                                    break;
                                case '1y':
                                case '2y':
									
                                    $formats = Mage::app()->getLocale()->getTranslationList('datetime');
                                    $format = isset($formats['yyMM']) ? $formats['yyMM'] : 'MM/yyyy';
                                    $format = str_replace(array("yyyy", "yy", "MM"), array("Y", "y", "m"), $format);
                                    $this->_axisLabels[$idx][$_index] = date($format, strtotime($_label));
                                    break;
                            }
                        } else {
                            $this->_axisLabels[$idx][$_index] = '';
                        }

                    }
										
                    $tmpstring = implode('|', $this->_axisLabels[$idx]);

                    $valueBuffer[] = $indexid . ":|" . $tmpstring;
                    if (sizeof($this->_axisLabels[$idx]) > 1) {
                        $deltaX = 100/(sizeof($this->_axisLabels[$idx])-1);
                    } else {
                        $deltaX = 100;
                    }
                } else if ($idx == 'y') {
                    $valueBuffer[] = $indexid . ":|" . implode('|', $yLabels);
                    if (sizeof($yLabels)-1) {
                        $deltaY = 100/(sizeof($yLabels)-1);
                    } else {
                        $deltaY = 100;
                    }
                    // setting range values for y axis
                    $rangeBuffer = $indexid . "," . $miny . "," . $maxy . "|";
                }
                $indexid++;
            }
            $params['chxl'] = implode('|', $valueBuffer);
        }			
					
		if (isset($deltaX) && isset($deltaY)) {
            $params['chg'] = $deltaX . ',' . $deltaY . ',1,0';
        }
        

			$p = array();
            foreach ($params as $name => $value) {
                $p[] = $name . '=' .urlencode($value);
            }

            $url= Mage_Adminhtml_Block_Dashboard_Graph::API_URL . '?' . implode('&', $p);

				echo $url;
	}

	protected function _getPow($number)
    {
        $pow = 0;
        while ($number >= 10) {
            $number = $number/10;
            $pow++;
        }
        return $pow;
    }
    
    public function formatDate($date = null, $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showTime = false)
    {
        return Mage::Helper('core')->formatDate($date, $format, $showTime);
    }   
    
    public function formatTime($time = null, $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showDate = false)
    {
        return Mage::Helper('core')->formatTime($time, $format, $showDate);
    } 
}


