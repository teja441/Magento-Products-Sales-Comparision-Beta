<?php

class Misc_Graphs_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getProductNames($categoryid){
	
	    $collection = Mage::getResourceModel('catalog/product_collection');

		$category = new Mage_Catalog_Model_Category();
	        $category->load($categoryid);

		    $collection = $category->getProductCollection()
					   ->addAttributeToSelect('*');
		
		$products=$collection->getData();
	
		$prod_name="<option value='select'>Select Product</option>";
		foreach($products as $product):
		
			$_product=Mage::getModel('catalog/product')->load($product['entity_id']);
//			$prod_name[$_product->getId()]=$_product->getName();
			$prod_name.="<option value=".$_product->getId().">".$_product->getName()."</option>";					

		endforeach;
	    return $prod_name;
	}

	public function getCategoryNames($storeId){

		$collection = Mage::getModel('catalog/category')
				    ->getCollection()
				    ->addAttributeToSelect('*')
				    ->setStoreId($storeId)
				    ->addIsActiveFilter();
		$categories=$collection->getData();
//		$cat_name=array();
		$cat_name="<option value='select'>Select Category</option>";
		foreach($categories as $category):
		$_category=Mage::getModel('catalog/category')->load($category['entity_id']);
		//$cat_name[$_category->getId()]=$_category->getName();
		$cat_name.="<option value=".$_category->getId().">".$_category->getName()."</option>";
		endforeach;

		return $cat_name;
	}

	public function getstoreIds(){

		$storeNames=array();
		$storeNames['store']="Select Store";
		$allStores = Mage::app()->getStores();
		foreach ($allStores as $_eachStoreId => $val){


		$storeNames[Mage::app()->getStore($_eachStoreId)->getId()] = Mage::app()->getStore($_eachStoreId)->getName();

		}
	
		return $storeNames;
	}

	public function getGraphData($dates,$product_id){

		$graphdata=array();
		$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$tableName = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
		Mage::log($tableName,3,"select_query.log");
			foreach($dates as $date):		
						/*$select = $connection->select()
												->from($tableName, count('*')) // select * from tablename or use array('id','title') selected values
												->where('created_at',array('like' => '%{$date}%'));         */      // where id =1					
			$select="select count(*) from {$tableName} where created_at like '%{$date}%'  and product_id='{$product_id}'";
			Mage::log($select,3,"select_query.log");
			$graphdata['quantity'][] = $connection->fetchOne($select); // return all rows
		endforeach;
		Mage::log($graphdata,3,"graph_collection_data.log");
		
				return $graphdata;
	}

}
