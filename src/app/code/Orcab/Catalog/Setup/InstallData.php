<?php
/**
 * Orcab Catalog setup
 *
 * @category  Orcab
 * @author    Matthieu Prigent <mapri@smile.fr>
 * @package   Orcab\Catalog
 */
namespace Orcab\Catalog\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Orcab\Catalog\Rewrite\Model\Product\Link;

class InstallData implements InstallDataInterface
{
    /**
     * Installs data
     *
     * @param ModuleDataSetupInterface $setup   Setup
     * @param ModuleContextInterface   $context Context
     *
     * @return void
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {

        /**
         * Install product link types
         */
        $data = [
            ['link_type_id' => Link::LINK_TYPE_PACK, 'code' => 'pack'],
            ['link_type_id' => Link::LINK_TYPE_SUBSTITUTED, 'code' => 'substituted'],
            ['link_type_id' => Link::LINK_TYPE_SUBSTITUTE, 'code' => 'substitute'],
            ['link_type_id' => Link::LINK_TYPE_SUBSTITUTION, 'code' => 'substitution'],
        ];

        foreach ($data as $bind) {
            $setup->getConnection()
                ->insertForce($setup->getTable('catalog_product_link_type'), $bind);
        }

        /**
         * install product link attributes
         */
        $data = [
            [
                'link_type_id' => Link::LINK_TYPE_PACK,
                'product_link_attribute_code' => 'position',
                'data_type' => 'int',
            ],
            [
                'link_type_id' => Link::LINK_TYPE_SUBSTITUTED,
                'product_link_attribute_code' => 'position',
                'data_type' => 'int',
            ],
            [
                'link_type_id' => Link::LINK_TYPE_SUBSTITUTE,
                'product_link_attribute_code' => 'position',
                'data_type' => 'int',
            ],
            [
                'link_type_id' => Link::LINK_TYPE_SUBSTITUTION,
                'product_link_attribute_code' => 'position',
                'data_type' => 'int',
            ],
        ];

        $setup->getConnection()
            ->insertMultiple($setup->getTable('catalog_product_link_attribute'), $data);
    }
}
