<?php
/**
 * Cayan Payments
 *
 * @package Cayan\Payment
 * @author Igor Miura
 * @author Joseph Leedy
 * @copyright Copyright (c) 2017 Cayan (https://cayan.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

namespace Apurba\Stripe\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Database Schema Installer
 *
 * @package Cayan\Payment\Setup
 * @author Igor Miura
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Create database tables used by the module
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable($setup->getTable('apurba_stripe'))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer Id'
            )->addColumn(
                'stripe_customer',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Stripe Customer Id'
            )->addColumn(
                'stripe_card_id',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Stripe Card Id'
            )->setComment(
                'Stripe Customer'
            )->addIndex(
                $setup->getIdxName('apurba_stripe', ['customer_id', 'stripe_customer', 'stripe_card_id']),
                ['customer_id', 'stripe_customer', 'stripe_card_id']
            )->setComment(
                'apurba_stripe'
            );

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
