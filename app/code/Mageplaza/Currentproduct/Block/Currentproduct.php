<?php
namespace Mageplaza\Currentproduct\Block;
class Currentproduct extends \Magento\Framework\View\Element\Template
{
    protected $_registry;

    protected $_productRepository;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        array $data = []
    )
    {        
        $this->_productRepository = $productRepository;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }
    
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getCurrentProduct()
    {        
        return $this->_registry->registry('current_product');
    } 
    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }
    
    public function getProductBySku($sku)
    { 
        return $this->_productRepository->get($sku);
    }   
    
}
?>