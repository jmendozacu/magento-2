<?php

namespace Orcab\Pimgento\Model\Rewrite\Category\Factory;

use \Pimgento\Category\Model\Factory\Import as baseImport;
use \Magento\Catalog\Model\Category;
use \Zend_Db_Expr as Expr;

class Import extends baseImport
{
    /**
     * Set Categories structure
     */
    public function setStructure()
    {
        $connection = $this->_entities->getResource()->getConnection();
        $tmpTable = $this->_entities->getTableName($this->getCode());

        $connection->addColumn($tmpTable, 'level', 'INT(11) NOT NULL DEFAULT 0');
        $connection->addColumn($tmpTable, 'path', 'VARCHAR(255) NOT NULL DEFAULT ""');
        $connection->addColumn($tmpTable, 'parent_id', 'INT(11) NOT NULL DEFAULT 0');

        $stores = $this->_helperConfig->getStores('lang');

        /**
         * ORCAB: Change level
         */
        $values = array(
            'level'     => 2,
            'path'      => new Expr('CONCAT("1/2", "/", `_entity_id`)'),
            'parent_id' => 2,
        );
        $connection->update($tmpTable, $values, 'parent = ""');

        $updateRewrite = array();

        foreach ($stores as $local => $affected) {
            if ($connection->tableColumnExists($tmpTable, 'url_key-' . $local)) {
                $connection->addColumn($tmpTable, '_url_rewrite-' . $local, 'VARCHAR(255) NOT NULL DEFAULT ""');
                $updateRewrite[] = 'c1.`_url_rewrite-' . $local . '` =
                    TRIM(BOTH "/" FROM CONCAT(c2.`_url_rewrite-' . $local . '`, "/", c1.`url_key-' . $local . '`))';
            }
        }

        $depth = 10;
        for ($i = 1; $i <= $depth; $i++) {
            $connection->query('
                UPDATE `' . $tmpTable . '` c1
                INNER JOIN `' . $tmpTable . '` c2 ON c2.`code` = c1.`parent`
                SET ' . (!empty($updateRewrite) ? join(',', $updateRewrite) . ',' : '') . '
                    c1.`level` = c2.`level` + 1,
                    c1.`path` = CONCAT(c2.`path`, "/", c1.`_entity_id`),
                    c1.`parent_id` = c2.`_entity_id`
                WHERE c1.`level` <= c2.`level` - 1
            ');
        }
    }
}