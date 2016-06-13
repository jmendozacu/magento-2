<?php

namespace Smartwave\Filterproducts\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;

class LatestList extends \Magento\Catalog\Block\Product\ListProduct {

    /**
     * Product collection model
     *
     * @var Magento\Catalog\Model\Resource\Product\Collection
     */
    protected $_collection;

    /**
     * Product collection model
     *
     * @var Magento\Catalog\Model\Resource\Product\Collection
     */
    protected $_productCollection;

    /**
     * Image helper
     *
     * @var Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;
    
    /**
     * Catalog Layer
     *
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * Initialize
     *
     * @param Magento\Catalog\Block\Product\Context $context
     * @param Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Magento\Framework\Url\Helper\Data $urlHelper
     * @param Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
    \Magento\Catalog\Block\Product\Context $context, 
            \Magento\Framework\Data\Helper\PostHelper $postDataHelper, 
            \Magento\Catalog\Model\Layer\Resolver $layerResolver, 
            CategoryRepositoryInterface $categoryRepository, 
            \Magento\Framework\Url\Helper\Data $urlHelper, 
            \Magento\Catalog\Model\ResourceModel\Product\Collection $collection, 
            array $data = []
    ) {
        $this->imageBuilder = $context->getImageBuilder();
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->urlHelper = $urlHelper;
        $this->_collection = $collection;
        $this->_imageHelper = $context->getImageHelper();

        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);

        $this->pageConfig->getTitle()->set(__($this->getPageTitle()));
    }

    /**
     * Get product collection
     */
    protected function getProducts() {
        $collection = $this->_collection
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('rating_summary')
                ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
                ->addUrlRewrite()
                ->addAttributeToFilter('is_saleable', 1, 'left')
                ->addAttributeToSort('created_at','desc');

        // Set Pagination Toolbar for list page
        $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'filterproducts.grid.record.pager')->setCollection($collection);
        $this->setChild('pager', $pager); // set pager block in layout
        $this->_productCollection = $collection;
        return $this->_productCollection;
    }

    /*
     * Load and return product collection 
     */
    public function getLoadedProductCollection() {
        return $this->getProducts();
    }

    /*
     * Get product toolbar
     */
    public function getToolbarHtml() {
        return $this->getChildHtml('pager');
    }

    /*
     * Get grid mode
     */
    public function getMode() {
        return 'grid';
    }

    /**
     * Get image helper
     */
    public function getImageHelper() {
        return $this->_imageHelper;
    }

    /**
     * Get module configuration
     */
    public function getConfig() {
        return $this->_scopeConfig->getValue('filterproducts/latest');
    }

    /**
     * Check that module is enabled or not
     * @return int
     */
    public function getSectionStatus() {
        return $this->_scopeConfig->getValue('filterproducts/latest/enable');
    }

    /**
     * Get the configured limit of products
     * @return int
     */
    public function getProductLimit() {
        $limit = $this->getData("product_count");
        if(!$limit)
            $limit = $this->_scopeConfig->getValue('filterproducts/latest/limit');
        return $limit;
    }

    /**
     * Get the configured title of section
     * @return int
     */
    public function getPageTitle() {
        return $this->_scopeConfig->getValue('filterproducts/latest/title');
    }

}
