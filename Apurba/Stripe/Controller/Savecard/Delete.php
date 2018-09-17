<?php
namespace Apurba\Stripe\Controller\Savecard;
use Apurba\Stripe\Model\SavecardFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlInterface;

class Delete extends \Magento\Framework\App\Action\Action{
	protected $_saveCard;
	protected $_customerSession;
	protected $_urlBulider;

	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        SavecardFactory $savecard,
        SessionFactory $customer,
         UrlInterface $url
    ) {
        $this->_saveCard = $savecard;
        $this->_customerSession = $customer;
        $this->_urlBulider = $url;
        parent::__construct($context);
    }

    public function execute(){
    	$this->redirectIfNotLoggedIn();
        $cardId = $this->getRequest()->getPost('id');
	$card = null;
	if($cardId){
		$card = $this->_saveCard->create()->deleteCard($cardId);
	}
	$this->getResponse()->setBody(json_encode($card));
    }

    public function redirectIfNotLoggedIn()
    {
        $cSess = $this->_customerSession->create();
        if (!$cSess->isLoggedIn()) {
            $cSess->setAfterAuthUrl($this->_urlBulider->getCurrentUrl());
            $cSess->authenticate();
        }
    }
}