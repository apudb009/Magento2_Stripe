<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Apurba\Stripe\Model\ResourceModel;

/**
 * SalesRule Resource Coupon
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Savecard extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('apurba_stripe', 'entity_id');
    }

}
