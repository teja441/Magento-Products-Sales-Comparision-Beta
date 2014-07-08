<?php 
class Misc_Graphs_Block_Adminhtml_Graph extends Mage_Adminhtml_Block_Widget_Form
{

   protected function _prepareForm()
    {

 $form = new Varien_Data_Form();
        
        $fieldset = $form->addFieldset('form_form',  array('legend'=>Mage::helper('graphs')->__('Graphs/Charts')));
          
	 $range=array();
	 $range[0]="Select Period";
	 $range=array_merge($range,$this->helper('adminhtml/dashboard_data')->getDatePeriods());

 	$fieldset->addField('stores', 'select', array(
          'label'     => Mage::helper('graphs')->__('Select Your Stores'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'stores',
          'onclick' => "",
          'onchange' => "getValues(this.value)",
          'value'  => '0',
          'values' => Mage::helper('graphs')->getstoreIds(),
          'disabled' => false,
          'readonly' => false,
          'tabindex' => 1,
	  'class'  => 'ajax'	
          ))->setAfterElementHtml("<script type='text/javascript'>
                            function getValues(store){ 
			     var reloadurl = '". $this->getUrl('*/*/getcategories')."';  
			    new Ajax.Request(reloadurl, {  
			    type: 'post',  
			    parameters: {storeid: store},  
			    onLoading: function (transport) {
                           $('categories').update('Searching...');
	                    },
			    onComplete: function(transport) { 
		           var response = transport.responseText;
                           $('categories').update(response);
 				}  
			   });
                            }
                         </script>"); 

        $fieldset->addField('categories', 'select', array(
          'label'     => Mage::helper('graphs')->__('Select Category'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'categories',
          'onclick' => "",
	  'onchange' => "getProducts(this.value)",
          'value'  => '0',
          'values' => '',
          'disabled' => false,
          'readonly' => false,
          'after_element_html' => '<small>Comments</small>',
          'tabindex' => 1,
	  'class'  => 'ajax'	
          ))->setAfterElementHtml("<script type='text/javascript'>
                            function getProducts(category){
			     var reloadurl = '". $this->getUrl('*/*/getproducts')."';  
			     //var div = document.createElement('div');
				 //div.className = 'graphresponse';
				//document.getElementById('anchor-content').appendChild(div);
			    new Ajax.Request(reloadurl, {  
			    type: 'post',  
			    parameters: {category: category},  
			    onLoading: function (transport) {
                           $('products').update('Searching...');
	                    },
			    onComplete: function(transport) { 
		           var response = transport.responseText;
                           $('products').update(response);
 				}  
			   });
                            }
                         </script>");
 
	$fieldset->addField('products', 'select', array(
          'label'     => Mage::helper('graphs')->__('Select Product'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'products',
          'onclick' => "",
          'onchange' => "",
          'value'  => '0',
          'values' => '',
          'disabled' => false,
          'readonly' => false,
          'tabindex' => 1
          ))->setAfterElementHtml();
                         
	$fieldset->addField('period', 'select', array(
          'label'     => Mage::helper('graphs')->__('Select Period'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'period',
          'onclick' => "",
          'onchange' => "getgraph(this.value)",
          'value'  => '0',
          'values' => $range,
          'disabled' => false,
          'readonly' => false,
          'tabindex' => 1
          ))->setAfterElementHtml("<script type='text/javascript'>
                            function getgraph(range){
					if(range!='0'){			
			     var reloadurl = '". $this->getUrl('*/*/getgraph')."';  
				 var product = document.getElementsByName('products')[0].value; 	
				//alert(product)	;alert(range)	;	 
			    new Ajax.Request(reloadurl, {  
			    type: 'post',  
			    parameters: {product: product,range:range},  			    
			    onComplete: function(transport) { 
		           var response = transport.responseText; 
		           //alert(response);
				document.getElementById('graphimage').src=response;
				document.getElementById('graphimage').show();
 				}  
			   }); }
			   else{
				   alert('Select Period');
				   }
               }
                         </script>");

        $this->setForm($form);

        return parent::_prepareForm();
    }

}



?>
