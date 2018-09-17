<?php

    namespace Apurba\Stripe\Block\Savecard;
    
    class Card extends \Magento\Framework\View\Element\Template{
        protected $_customerSession;
        protected $_saveCard;
        
        public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $customerSession,
        \Apurba\Stripe\Model\SavecardFactory $saveCard,
        array $data = []
        )
        {
            parent::__construct(
                $context,
                $data
            );
            $this->_customerSession = $customerSession;
            $this->_saveCard = $saveCard;
        }
        
        public function getCustomerSavedCards(){
            $customerId = $this->_customerSession->getCustomerId();
            if(!$customerId)
                return null;
            $stripeCard = $this->_saveCard->create();
            $collection = $stripeCard->getSaveCardByCustomerId($customerId);
            return $collection;
        }
        public function hasSavecard(){
            return (count($this->getCustomerSavedCards()) > 0)  ? true : false;
        }
        
        public function getDeleteUrl(){
            return $this->getUrl('stripe/savecard/delete',['_secure' => true]);
        }
        
        protected function _prepareLayout()
        {
            
        }
    }