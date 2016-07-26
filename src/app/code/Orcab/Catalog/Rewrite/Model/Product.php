<?php
/**
 * Product model
 *
 * @category  Orcab
 * @author    Matthieu Prigent <mapri@smile.fr>
 * @copyright 2016 Orcab
 */
namespace Orcab\Catalog\Rewrite\Model;

use Magento\Catalog\Model\Product as BaseProduct;

class Product extends BaseProduct
{
    /**
     * Retrieve array of pack products.
     *
     * @return array
     */
    public function getPackProducts()
    {
        if (!$this->hasPackProducts()) {
            $products = [];
            foreach ($this->getPackProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setPackProducts($products);
        }

        return $this->getData('pack_products');
    }

    /**
     * Get the collection of pack products.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function getPackProductCollection()
    {
        $collection = $this->getLinkInstance()->usePackLinks()->getProductCollection()->setIsStrongMode();
        $collection->setProduct($this);

        return $collection;
    }

    /**
     * Get the collection of pack links.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Collection
     */
    public function getPackLinkCollection()
    {
        $collection = $this->getLinkInstance()->usePackLinks()->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();

        return $collection;
    }

    /**
     * Retrieve array of substituted products.
     *
     * @return array
     */
    public function getSubstitutedProducts()
    {
        if (!$this->hasSubstitutedProducts()) {
            $products = [];
            foreach ($this->getSubstitutedProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setSubstitutedProducts($products);
        }

        return $this->getData('substituted_products');
    }

    /**
     * Get the collection of substituted products.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function getSubstitutedProductCollection()
    {
        $collection = $this->getLinkInstance()->useSubstitutedLinks()->getProductCollection()->setIsStrongMode();
        $collection->setProduct($this);

        return $collection;
    }

    /**
     * Get the collection of substituted links.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Collection
     */
    public function getSubstitutedLinkCollection()
    {
        $collection = $this->getLinkInstance()->useSubstitutedLinks()->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();

        return $collection;
    }

    /**
     * Retrieve array of substitute products.
     *
     * @return array
     */
    public function getSubstituteProducts()
    {
        if (!$this->hasSubstituteProducts()) {
            $products = [];
            foreach ($this->getSubstituteProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setSubstituteProducts($products);
        }

        return $this->getData('substitute_products');
    }

    /**
     * Get the collection of substitute products.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function getSubstituteProductCollection()
    {
        $collection = $this->getLinkInstance()->useSubstituteLinks()->getProductCollection()->setIsStrongMode();
        $collection->setProduct($this);

        return $collection;
    }

    /**
     * Get the collection of substitute links.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Collection
     */
    public function getSubstituteLinkCollection()
    {
        $collection = $this->getLinkInstance()->useSubstituteLinks()->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();

        return $collection;
    }

    /**
     * Retrieve array of substitution products.
     *
     * @return array
     */
    public function getSubstitutionProducts()
    {
        if (!$this->hasSubstitutionProducts()) {
            $products = [];
            foreach ($this->getSubstitutionProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setSubstitutionProducts($products);
        }

        return $this->getData('substitution_products');
    }

    /**
     * Get the collection of substitution products.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function getSubstitutionProductCollection()
    {
        $collection = $this->getLinkInstance()->useSubstitutionLinks()->getProductCollection()->setIsStrongMode();
        $collection->setProduct($this);

        return $collection;
    }

    /**
     * Get the collection of substitution links.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Collection
     */
    public function getSubstitutionLinkCollection()
    {
        $collection = $this->getLinkInstance()->useSubstitutionLinks()->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();

        return $collection;
    }
}