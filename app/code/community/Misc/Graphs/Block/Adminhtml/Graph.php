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
                           $('categories2').update(response);
 				}  
			   });
                            }
                         </script>"); 

        $fieldset->addField('categories', 'select', array(
          'label'     => Mage::helper('graphs')->__('Select Category One'),
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
          'label'     => Mage::helper('graphs')->__('Select Product One'),
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
    
      
   $fieldset->addField('categories2', 'select', array(
          'label'     => Mage::helper('graphs')->__('Select Category Two'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'categories2',
          'onclick' => "",
		  'onchange' => "getcompare(this.value)",
          'value'  => '0',
          'values' => '',
          'disabled' => false,
          'readonly' => false,
          'after_element_html' => '<small>Comments</small>',
          'tabindex' => 1,
	  'class'  => 'ajax'	
          ))->setAfterElementHtml("<script type='text/javascript'>
                            function getcompare(category){
			     var reloadurl = '". $this->getUrl('*/*/getproducts')."';  
			    
			    new Ajax.Request(reloadurl, {  
			    type: 'post',  
			    parameters: {category: category},  
			    onLoading: function (transport) {
                           $('compare2').update('Searching...');
	                    },
			    onComplete: function(transport) { 
		           var response = transport.responseText;
                           $('compare2').update(response);
 				}  
			   });
                            }
                         </script>");
                         
     $fieldset->addField('compare2', 'select', array(
          'label'     => Mage::helper('graphs')->__('Select Product Two'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'compare2',
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
          'value'  => '0',
          'values' => $range,
          'disabled' => false,
          'readonly' => false,
          'tabindex' => 1
          ));

	$fieldset->addField('submit', 'submit', array(
//          'label'     => Mage::helper('graphs')->__('Submit'),
          'type'     => 'button',
		  'onclick' => "getgraph()",
          'required'  => true,
          'value'  => 'Submit',
          'tabindex' => 1
        ))->setAfterElementHtml("<script type='text/javascript'>
                            function getgraph(){
					var period = document.getElementsByName('period')[0].value; 	
					var compare2 = document.getElementsByName('compare2')[0].value; 
					if(period!='0'){			
			     var reloadurl = '". $this->getUrl('*/*/getgraph')."';  
				 var product = document.getElementsByName('products')[0].value; 					 
				//alert(product)	;alert(range)	;	 
			    new Ajax.Request(reloadurl, {  
			    type: 'post',
			    parameters: {product: product,range:period,compare2:compare2},  			    
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
