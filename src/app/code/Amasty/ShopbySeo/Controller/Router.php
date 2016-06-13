<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\ShopbySeo\Controller;


use Amasty\ShopbySeo\Helper\Data;
use Amasty\ShopbySeo\Helper\Url;

class Router implements \Magento\Framework\App\RouterInterface
{
    const ALIAS_DELIMITER = '-';

    /** @var \Magento\Framework\App\ActionFactory */
    protected $actionFactory;

    /** @var \Magento\Framework\App\ResponseInterface */
    protected $_response;

    /** @var  Data */
    protected $seoHelper;

    /** @var  Url */
    protected $urlHelper;

    /** @var  \Magento\Framework\Registry */
    protected $registry;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\Registry $registry,
        Data $seoHelper,
        Url $urlHelper
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
        $this->registry = $registry;
        $this->seoHelper = $seoHelper;
        $this->urlHelper = $urlHelper;
    }

    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->urlHelper->isSeoUrlEnabled()) {
            return;
        }

        $identifier = trim($request->getPathInfo(), '/');
        if (!preg_match('@^(.*)/([^/]+)@', $identifier, $matches))
            return;

        $aliases = $this->urlHelper->removeCategorySuffix($matches[2]);
        $category = ($aliases == $matches[2]) ? $matches[1] : $this->urlHelper->addCategorySuffix($matches[1]);

        $aliases = explode(static::ALIAS_DELIMITER, $aliases);
        $params = $this->parseAliasesRecursively($aliases);
        if ($params === false) {
            return;
        }

        $this->registry->register('amasty_shopby_seo_parsed_params', $params);

        $params = array_merge($params, $request->getParams());
        $request->setParams($params);

        $request->setPathInfo($category);

        /*
         * We have match and now we will forward action
         */
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }

    /**
     * @param array $aliases
     * @return array|false
     */
    protected function parseAliasesRecursively($aliases)
    {
        $optionsData = $this->seoHelper->getOptionsSeoData();
        $unparsedAliases = [];
        while ($aliases) {
            $currentAlias = implode(static::ALIAS_DELIMITER, $aliases);
            foreach ($optionsData as $optionId => $option) {
                if ($option['alias'] === $currentAlias) {
                    // Continue DFS
                    $params = $unparsedAliases ? $this->parseAliasesRecursively($unparsedAliases) : [];

                    if ($params !== false) {
                        // Local solution found
                        $params = $this->addParsedOptionToParams($optionId, $option['attribute_code'], $params);
                        return $params;
                    }
                }
            }

            $unparsedAliases[] = array_pop($aliases);
        }

        return false;
    }

    protected function addParsedOptionToParams($value, $paramName, $params)
    {
        if (array_key_exists($paramName, $params)) {
            $params[$paramName] .= ',' . $value;
        } else {
            $params[$paramName] = '' . $value;
        }

        return $params;
    }
}
