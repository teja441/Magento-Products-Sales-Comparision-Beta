<?php 
class Misc_Graphs_Block_Adminhtml_Graphresponse extends Mage_Core_Block_Template
{

 protected function _toHtml()
    {
        $html ='<div class="graphResponse" id="graphresponse">
			<img src="" style="display:none" id="graphimage" alt="chart/graph" >
        </div>';
       
        return $html;
    }

}




