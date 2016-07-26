<?php
/**
 * Product initialization helper plugin
 *
 * @category  Orcab
 * @author    Matthieu Prigent <mapri@smile.fr>
 * @copyright 2016 Orcab
 */
namespace Orcab\Catalog\Plugin\Controller\Adminhtml\Product\Initialization;

use Magento\Backend\Helper\Js as JsHelper;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper as Subject;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;

class Helper
{
    /**
     * HTTP Request.
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * JS helper.
     *
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Backend\Helper\Js $jsHelper
     */
    public function __construct(
        RequestInterface $request,
        JsHelper $jsHelper
    ) {
        $this->request = $request;
        $this->jsHelper = $jsHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function afterInitialize(Subject $subject, Product $product)
    {
        // Set pack link data
        $links = $this->request->getPost('links');

        if (isset($links['pack']) && !$product->getPackReadonly()) {
            $packLinkData = $this->jsHelper->decodeGridSerializedInput($links['pack']);
            $product->setPackLinkData($packLinkData);
        }

        if (isset($links['substituted']) && !$product->getSubstitutedReadonly()) {
            $substitutedLinkData = $this->jsHelper->decodeGridSerializedInput($links['substituted']);
            $product->setSubstitutedLinkData($substitutedLinkData);
        }

        if (isset($links['substitute']) && !$product->getSubstituteReadonly()) {
            $substituteLinkData = $this->jsHelper->decodeGridSerializedInput($links['substitute']);
            $product->setSubstituteLinkData($substituteLinkData);
        }

        if (isset($links['substitution']) && !$product->getSubstitutionReadonly()) {
            $substitutionLinkData = $this->jsHelper->decodeGridSerializedInput($links['substitution']);
            $product->setSubstitutionLinkData($substitutionLinkData);
        }

        return $product;
    }
}
