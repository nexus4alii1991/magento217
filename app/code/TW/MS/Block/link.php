<?php

namespace TW\MS\Block;
 
class Link extends \Magento\Framework\View\Element\Html\Link
{
/**
* Render block HTML.
*
* @return string
*/
protected function _toHtml()
    {
     if (false != $this->getTemplate()) {
     return parent::_toHtml();
     }
     if($this->getLabel() == "Return & Exchange"){ 
     	$class = "return_link"; 
     }else{ 
     	$class = "cancel_link";
     }
     return '<li><a class ="'.$class.'" ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml($this->getLabel()) . '</a></li>';
    }
}