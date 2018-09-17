<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Apurba\Stripe\Model\ResourceModel\Savecard;

/**
 * SalesRule Model Resource Coupon_Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Apurba\Stripe\Model\Savecard::class, \Apurba\Stripe\Model\ResourceModel\Savecard::class);
    }
}
