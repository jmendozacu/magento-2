<?php
/**
 * Product link model
 *
 * @category  Orcab
 * @author    Matthieu Prigent <mapri@smile.fr>
 * @copyright 2016 Orcab
 */
namespace Orcab\Catalog\Rewrite\Model\Product;

use Magento\Catalog\Model\Product\Link as BaseLink;

class Link extends BaseLink
{
    /**
     * Pack link type.
     */
    const LINK_TYPE_PACK = 6;
    const LINK_TYPE_SUBSTITUTED = 7;
    const LINK_TYPE_SUBSTITUTE = 8;
    const LINK_TYPE_SUBSTITUTION = 9;

    /**
     * Use pack links.
     *
     * @return $this
     */
    public function usePackLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_PACK);

        return $this;
    }

    /**
     * Use substituted links.
     *
     * @return $this
     */
    public function useSubstitutedLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_SUBSTITUTED);

        return $this;
    }

    /**
     * Use substitute links.
     *
     * @return $this
     */
    public function useSubstituteLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_SUBSTITUTE);

        return $this;
    }

    /**
     * Use substitution links.
     *
     * @return $this
     */
    public function useSubstitutionLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_SUBSTITUTION);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function saveProductRelations($product)
    {
        parent::saveProductRelations($product);

        $data = $product->getPackLinkData();
        if ($data !== null) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_PACK);
        }

        $data = $product->getSubstitutedLinkData();
        if ($data !== null) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_SUBSTITUTED);
        }

        $data = $product->getSubstituteLinkData();
        if ($data !== null) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_SUBSTITUTE);
        }

        $data = $product->getSubstitutionLinkData();
        if ($data !== null) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_SUBSTITUTION);
        }

        return $this;
    }
}