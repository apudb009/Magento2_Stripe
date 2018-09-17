<?php

namespace Apurba\Stripe\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

class DataAssignObserver extends AbstractDataAssignObserver
{
    const SOURCE = 'source';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::SOURCE,
    ];

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }

        if(isset($additionalData['save_card'])){
            $additionalData['save_card'] = ($additionalData['save_card'] == 1) ? true : false;
            $paymentInfo->setAdditionalInformation('save_card', $additionalData['save_card']);
        }

        if(isset($additionalData['card_number'])){
            $paymentInfo->setAdditionalInformation('card_number', $additionalData['card_number']);
        }

        if(isset($additionalData['card_cvc'])){
            $paymentInfo->setAdditionalInformation('card_cvc', $additionalData['card_cvc']);
        }
        if(isset($additionalData['exp_month'])){
            $paymentInfo->setAdditionalInformation('exp_month', $additionalData['exp_month']);
        }
        if(isset($additionalData['exp_year'])){
            $paymentInfo->setAdditionalInformation('exp_year', $additionalData['exp_year']);
        }
        if(isset($additionalData['token'])){
            $paymentInfo->setAdditionalInformation('token', $additionalData['token']);
        }
    }
}
