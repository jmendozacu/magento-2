<?php

namespace Orcab\Pimgento\Model\Rewrite\Product\Factory;

use \Pimgento\Product\Model\Factory\Import as baseImport;
use \Zend_Db_Expr as Expr;

class Import extends baseImport
{
    protected $linkColumns = array(
        1 => 'RELATION-products',
        3 => 'SUBSTITUTION-products',
        4 => 'UPSELL-products',
        5 => 'X_SELL-products',
    );

    /**
     * Set values to attributes
     */
    public function setValues()
    {
        $connection = $this->_entities->getResource()->getConnection();
        $tmpTable = $this->_entities->getTableName($this->getCode());

        $stores = array_merge(
            $this->_helperConfig->getStores(array('lang')), // en_US
            $this->_helperConfig->getStores(array('channel_code')), // channel
            $this->_helperConfig->getStores(array('lang', 'channel_code')), // en_US-channel
            $this->_helperConfig->getStores(array('currency')), // USD
            $this->_helperConfig->getStores(array('channel_code', 'currency')), // channel-USD
            $this->_helperConfig->getStores(array('lang', 'channel_code', 'currency')) // en_US-channel-USD
        );

        $columns = array_keys($connection->describeTable($tmpTable));

        $except = array(
            '_entity_id',
            '_is_new',
            '_status',
            '_type_id',
            '_options_container',
            '_tax_class_id',
            '_attribute_set_id',
            '_visibility',
            '_children',
            '_axis',
            'sku',
            'categories',
            'family',
            'groups',
            'enabled',
        );

        $values = array(
            0 => array(
                'options_container' => '_options_container',
                'tax_class_id'      => '_tax_class_id',
                'visibility'        => '_visibility',
            )
        );

        if ($connection->tableColumnExists($tmpTable, 'enabled')) {
            $values[0]['status'] = '_status';
        }

        foreach ($columns as $column) {
            if (in_array($column, $except)) {
                continue;
            }

            if ($connection->tableColumnExists($tmpTable, $column.'-unit')) {
                $connection->update($tmpTable, array($column.'-unit' => $connection->getConcatSql(array('`'.$column.'`', '" "', '`'.$column.'-unit`'))));
            }

            $columnPrefix = explode('-', $column);
            $columnPrefix = reset($columnPrefix);

            $values[0][$columnPrefix] = $column;

            foreach ($stores as $suffix => $affected) {
                if (preg_match('/' . $suffix . '$/', $column)) {
                    foreach ($affected as $store) {
                        if (!isset($values[$store['store_id']])) {
                            $values[$store['store_id']] = array();
                        }
                        $values[$store['store_id']][$columnPrefix] = $column;
                    }
                }
            }
        }

        foreach($values as $storeId => $data) {
            $this->_entities->setValues(
                $this->getCode(), $connection->getTableName('catalog_product_entity'), $data, 4, $storeId, 1
            );
        }

        /**
         * Links product
         */
        foreach ($this->linkColumns as $linkTypeId => $linkColumn) {
            if ($connection->tableColumnExists($tmpTable, $linkColumn)) {
                $this->addLink($linkColumn, $linkTypeId, $connection, $tmpTable);
            }
        }
    }

    /**
     * Add links product
     *
     * @param string                                 $column
     * @param int                                    $linkTypeId
     * @param Magento\Framework\DB\Adapter\Pdo\Mysql $connection
     * @param string                                 $tmpTable
     */
    public function addLink($column, $linkTypeId, $connection, $tmpTable)
    {
        $select = $connection->select()
            ->from(
                array(
                    'e' =>  $connection->getTableName('catalog_product_entity')
                ),
                array()
            )
            ->joinInner(
                array('p' => $tmpTable),
                "FIND_IN_SET(e.`sku`, p.`{$column}`)",
                array(
                    'product_id'        => 'p._entity_id',
                    'linked_product_id' => 'e.entity_id',
                    'link_type_id'      => new Expr($linkTypeId)
                )
            );

        $connection->query(
            $connection->insertFromSelect(
                $select,  $connection->getTableName('catalog_product_link'), array('product_id', 'linked_product_id', 'link_type_id'), 2
            )
        );
    }
}