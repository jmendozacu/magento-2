<?php
/**
 * Abstract product action
 *
 * @category  Orcab
 * @author    Matthieu Prigent <mapri@smile.fr>
 * @copyright 2016 Orcab
 */
namespace Orcab\Catalog\Controller\Adminhtml\Product;

use Magento\Catalog\Controller\Adminhtml\Product;

abstract class AbstractAction extends Product
{
    /**
     * Authorization level for this module.
     */
    const ADMIN_RESOURCE = 'Magento_Catalog::products';

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
