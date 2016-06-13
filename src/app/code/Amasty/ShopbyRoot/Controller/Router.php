<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\ShopbyRoot\Controller;

use Magento\Framework\Module\Manager;

class Router implements \Magento\Framework\App\RouterInterface
{
    /** @var \Magento\Framework\App\ActionFactory */
    protected $actionFactory;

    /** @var \Magento\Framework\App\ResponseInterface */
    protected $response;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /** @var  Manager */
    protected $moduleManager;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Manager $moduleManager)
    {
        $this->actionFactory = $actionFactory;
        $this->response = $response;
        $this->scopeConfig = $scopeConfig;
        $this->moduleManager = $moduleManager;
    }

    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if(!$this->scopeConfig->isSetFlag('amshopby_root/general/enabled',  \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return null;
        }
        $shopbyPageUrl = $this->scopeConfig->getValue('amshopby_root/general/url',  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $identifier = trim($request->getPathInfo(), '/');
        $identifier = current(explode("/", $identifier));

        if($identifier == $shopbyPageUrl) {
            // Forward Shopby
            $request->setModuleName('amshopby')->setControllerName('index')->setActionName('index');
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
            return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
        }

        if ($this->moduleManager->isEnabled('Amasty_ShopbySeo') && $this->scopeConfig->getValue('amasty_shopby_seo/url/mode')) {
            // Forward to very short brand-like url
            $request->setPathInfo($shopbyPageUrl . '/' . $identifier);
            return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
        }
    }
}
