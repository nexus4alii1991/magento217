<?php
namespace Mageplaza\Currentproduct\Block;
class CategoryCollection extends \Magento\Framework\View\Element\Template
{    
    protected $_categoryCollectionFactory;
    protected $_productRepository;
    protected $_registry;
        
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,

        \Magento\Framework\Registry $registry,
        array $data = []
        )
        {
            $this->_categoryCollectionFactory = $categoryCollectionFactory;
            $this->_productRepository = $productRepository;
            $this->_registry = $registry;
            $this->_categoryFactory = $categoryFactory;
            parent::__construct($context, $data);
    }
    
    /**
     * Get category collection
     *
     * @param bool $isActive
     * @param bool|int $level
     * @param bool|string $sortBy
     * @param bool|int $pageSize
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection or array
     */
    public function getCategoryCollection($isActive = true, $level = false, $sortBy = false, $pageSize = false)
    {
        $collection = $this->_categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');        
        
        // select only active categories
        if ($isActive) {
            $collection->addIsActiveFilter();
        }
                
        // select categories of certain level
        if ($level) {
            $collection->addLevelFilter($level);
        }
        
        // sort categories by some value
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }
        
        // select certain number of categories
        if ($pageSize) {
            $collection->setPageSize($pageSize); 
        }    
        
        return $collection;
    }
    
    public function getProductById($id)
    {        
        return $this->_productRepository->getById($id);
    }
    
    public function getCurrentProduct()
    {        
        return $this->_registry->registry('current_product');
    }
    public function getCategoryByTitle($categoryTitle){
        $collection = $this->_categoryFactory->create()->getCollection()->addAttributeToFilter('name',$categoryTitle)->setPageSize(1);

        if ($collection->getSize()) {
            return $collection;
            //return $categoryId = $collection->getFirstItem()->getId();
        }
    }
    Public function getUrlKey($title){
        $categories = $this->_categoryCollectionFactory->create();
        $categories->addAttributeToFilter('name', $title)
                    ->addAttributeToSelect('url_key')
                    ->addUrlRewriteToResult();
        foreach($categories as $category){
        return $urlkey = $category->getUrlKey();
        }
    }

}
?>