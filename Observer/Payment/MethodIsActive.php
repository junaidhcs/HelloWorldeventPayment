<?php
namespace Mytask\HelloWorldeventPayment\Observer\Payment;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Event\ObserverInterface;

class MethodIsActive implements ObserverInterface {
	protected $_cart;
	protected $_checkoutSession;
	protected $productRepository;
	public function __construct(
		\Magento\Checkout\Model\Cart $cart,
		\Magento\Checkout\Model\Session $checkoutSession,
		ProductRepositoryInterface $productRepository
	) {
		$this->_cart = $cart;
		$this->_checkoutSession = $checkoutSession;
		$this->productRepository = $productRepository;
	}
	/**
	 * Execute observer
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return void
	 */
	public function execute(\Magento\Framework\Event\Observer $observer) {
		$quote = $this->getCheckoutSession()->getQuote();
		$categoryID = 5; //Add your category ID
		$items = $quote->getAllItems();
		$flag = false;
		foreach ($items as $item) {
			$product = $this->getProduct($item->getProductId());
			$categoryIds = $product->getCategoryIds();
			if (in_array($categoryID, $categoryIds)) {
				$flag = true;
				break;
			}
		}

		// you can replace "checkmo" with your required payment method code
		if ($flag == false && $observer->getEvent()->getMethodInstance()->getCode() == "cashondelivery") {
			$checkResult = $observer->getEvent()->getResult();
			$checkResult->setData('is_available', false);
		} else if ($flag == true && $observer->getEvent()->getMethodInstance()->getCode() == "checkmo") {
			$checkResult = $observer->getEvent()->getResult();
			$checkResult->setData('is_available', false);
		}
	}
	public function getProduct($productId) {
		return $product = $this->productRepository->getById($productId);
	}
	public function getCart() {
		return $this->_cart;
	}

	public function getCheckoutSession() {
		return $this->_checkoutSession;
	}
}
