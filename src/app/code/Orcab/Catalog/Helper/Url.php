<?php
/**
 * Orcab URL helper
 *
 * @category  Orcab
 * @author    Matthieu Prigent <mapri@smile.fr>
 * @package   Orcab\Catalog
 */
namespace Orcab\Catalog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Backend\Model\UrlInterface;

class Url extends AbstractHelper
{
    /**
     * Backend URL model.
     *
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     */
    public function __construct(
        Context $context,
        UrlInterface $backendUrl
    ) {
        parent::__construct($context);

        $this->backendUrl = $backendUrl;
    }

    /**
     * Get the pack grid URL.
     *
     * @return string
     */
    public function getPackGridUrl()
    {
        return $this->backendUrl->getUrl('orcab_catalog/product/pack', ['_current' => true]);
    }

    /**
     * Get the substituted grid URL.
     *
     * @return string
     */
    public function getSubstitutedGridUrl()
    {
        return $this->backendUrl->getUrl('orcab_catalog/product/substituted', ['_current' => true]);
    }

    /**
     * Get the substitute grid URL.
     *
     * @return string
     */
    public function getSubstituteGridUrl()
    {
        return $this->backendUrl->getUrl('orcab_catalog/product/substitute', ['_current' => true]);
    }

    /**
     * Get the substitution grid URL.
     *
     * @return string
     */
    public function getSubstitutionGridUrl()
    {
        return $this->backendUrl->getUrl('orcab_catalog/product/substitution', ['_current' => true]);
    }
}
