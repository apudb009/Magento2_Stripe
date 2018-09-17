<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Apurba\Stripe\Model;
use Magento\Framework\Exception\StateException as Exception;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use Stripe\Stripe;
/**
 * SalesRule Coupon Model
 *
 * @api
 * @since 100.0.2
 */
class Savecard extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    const KEY_SECRATE = "payment/apurba_stripe/api_key";
    protected $config;
    public function __construct(ScopeConfigInterface $config)
    {
        $this->_init(\Apurba\Stripe\Model\ResourceModel\Savecard::class);
        $this->config = $config;
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $api = $this->config->getValue(self::KEY_SECRATE, $storeScope);
        \Stripe\Stripe::setApiKey($api);
    }
    
    public function getSaveCardByCustomerId($customer){
        
        $customerStripe = $this->getCollection()->addFieldToFilter('customer_id',$customer)->getFirstItem();
        $allCards = null;
        if($customerStripe->getId()){
            try{
               $customer = \Stripe\Customer::retrieve($customerStripe->getStripeCustomer());
                $allCards = $customer->sources;
                if(!empty($allCards)){
                    $params['object'] = 'card';
                    return $allCards->all($params)->data;
                }else
                    return null;
            }catch(\Exception $e){
                return null;
            }
            
        }
    }
    
    public function deleteCard($card){
        
        $stripeCustomer = $this->getCollection()->addFieldToFilter('stripe_card_id',$card)->getFirstItem()->getStripeCustomer();
        if($stripeCustomer){
            try{
                $customer = \Stripe\Customer::retrieve($stripeCustomer);
                $cardData = $customer->sources->retrieve($card)->delete();
                return $cardData->deleted;
            }catch(\Exception $e){
                return false;
            }
        }
        return false;
    }
}
