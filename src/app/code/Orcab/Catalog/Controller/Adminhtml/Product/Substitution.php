<?php
/**
 * Substitution tab action
 *
 * @category  Orcab
 * @author    Matthieu Prigent <mapri@smile.fr>
 * @copyright 2016 Orcab
 */
namespace Orcab\Catalog\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Controller\Adminhtml\Product\Builder as ProductBuilder;
use Magento\Framework\View\Result\LayoutFactory;

class Substitution extends AbstractAction
{
    /**
     * Result layout factory.
     *
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Context $context,
        ProductBuilder $productBuilder,
        LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context, $productBuilder);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->productBuilder->build($this->getRequest());
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('catalog.product.edit.tab.substitution')
            ->setSubstitutionProducts($this->getRequest()->getPost('substitution_products', null));

        return $resultLayout;
    }
}
