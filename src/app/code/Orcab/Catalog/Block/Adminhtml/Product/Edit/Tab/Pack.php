<?php
/**
 * Tab for product packs grid
 *
 * @category  Orcab
 * @author    Matthieu Prigent <mapri@smile.fr>
 * @copyright 2016 Orcab
 */
namespace Orcab\Catalog\Block\Adminhtml\Product\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\LinkFactory;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Directory\Model\Currency;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as SetsFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

class Pack extends Extended
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Product link.
     *
     * @var \Magento\Catalog\Model\Product\LinkFactory
     */
    protected $linkFactory;

    /**
     * Attribute sets factory.
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $setsFactory;

    /**
     * Product factory.
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $type;

    /**
     * Status attribute source.
     *
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $status;

    /**
     * Product visibility.
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $visibility;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\Product\LinkFactory $linkFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        LinkFactory $linkFactory,
        SetsFactory $setsFactory,
        ProductFactory $productFactory,
        ProductType $type,
        Status $status,
        Visibility $visibility,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->linkFactory = $linkFactory;
        $this->setsFactory = $setsFactory;
        $this->productFactory = $productFactory;
        $this->type = $type;
        $this->status = $status;
        $this->visibility = $visibility;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('pack_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);

        if ($this->getProduct() && $this->getProduct()->getId()) {
            $this->setDefaultFilter(['in_products' => 1]);
        }

        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

    /**
     * Get the current product.
     *
     * @return \Orcab\Catalog\Rewrite\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }

    /**
     * {@inheritdoc}
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        /* @var $collection \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection */
        $collection = $this->linkFactory->create()
            ->usePackLinks()
            ->getProductCollection()
            ->setProduct($this->getProduct())
            ->addAttributeToSelect('*');

        if ($this->isReadonly()) {
            $productIds = $this->getSelectedProducts();
            if (empty($productIds)) {
                $productIds = [0];
            }
            $collection->addFieldToFilter('entity_id', ['in' => $productIds]);
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Check whether this block is readonly.
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getProduct() && $this->getProduct()->getPackReadonly();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        if (!$this->isReadonly()) {
            $this->addColumn(
                'in_products',
                [
                    'type' => 'checkbox',
                    'name' => 'in_products',
                    'values' => $this->getSelectedProducts(),
                    'align' => 'center',
                    'index' => 'entity_id',
                    'header_css_class' => 'col-select',
                    'column_css_class' => 'col-select'
                ]
            );
        }

        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'type',
            [
                'header' => __('Type'),
                'index' => 'type_id',
                'type' => 'options',
                'options' => $this->type->getOptionArray(),
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type'
            ]
        );

        $sets = $this->setsFactory->create()->setEntityTypeFilter(
            $this->productFactory->create()->getResource()->getTypeId()
        )->load()->toOptionHash();

        $this->addColumn(
            'set_name',
            [
                'header' => __('Attribute Set'),
                'index' => 'attribute_set_id',
                'type' => 'options',
                'options' => $sets,
                'header_css_class' => 'col-attr-name',
                'column_css_class' => 'col-attr-name'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->status->getOptionArray(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );

        $this->addColumn(
            'visibility',
            [
                'header' => __('Visibility'),
                'index' => 'visibility',
                'type' => 'options',
                'options' => $this->visibility->getOptionArray(),
                'header_css_class' => 'col-visibility',
                'column_css_class' => 'col-visibility'
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku',
                'header_css_class' => 'col-sku',
                'column_css_class' => 'col-sku'
            ]
        );

        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'currency_code' => (string) $this->_scopeConfig->getValue(
                    Currency::XML_PATH_CURRENCY_BASE,
                    ScopeInterface::SCOPE_STORE
                ),
                'index' => 'price',
                'header_css_class' => 'col-price',
                'column_css_class' => 'col-price'
            ]
        );

        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'name' => 'position',
                'type' => 'number',
                'validate_class' => 'validate-number',
                'index' => 'position',
                'editable' => !$this->isReadonly(),
                'edit_only' => !$this->getProduct()->getId(),
                'header_css_class' => 'col-position',
                'column_css_class' => 'col-position',
                'filter_condition_callback' => [$this, 'filterByColumn']
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getGridUrl()
    {
        return $this->_getData('grid_url') ?: $this->getUrl('orcab_catalog/*/packGrid', ['_current' => true]);
    }

    /**
     * Retrieve selected pack products.
     *
     * @return array
     */
    protected function getSelectedProducts()
    {
        $products = $this->getProductsPack();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedPackProducts());
        }

        return $products;
    }

    /**
     * Retrieve pack products.
     *
     * @return array
     */
    public function getSelectedPackProducts()
    {
        $products = [];

        foreach ($this->coreRegistry->registry('current_product')->getPackProducts() as $product) {
            $products[$product->getId()] = [
                'position' => $product->getPosition()
            ];
        }

        return $products;
    }

    /**
     * Apply a filter to the grid on the specified column.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection $collection $collection
     * @param \Magento\Backend\Block\Widget\Grid\Column\Extended $column
     * @return $this
     */
    public function filterByColumn($collection, $column)
    {
        $collection->addLinkAttributeToFilter($column->getIndex(), $column->getFilter()->getCondition());

        return $this;
    }
}