<?php
namespace Qonfi\Qonfi\Controller\Add;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\DataObject;

/**
 * Qonfi Add to Cart Controller
 *
 * This controller handles adding products to the cart via a REST API endpoint.
 * It supports simple products and uses Magento's built-in session and quote management.
 */
class Index implements ActionInterface
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    /**
     * @var ResultFactory
     */
    protected $resultFactory;
    /**
     * @var MessageManager
     */
    protected $messageManager;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var CartRepository
     */
    protected $cartRepository;

    /**
     * Index constructor.
     * @param CheckoutSession $checkoutSession
     * @param ProductRepository $productRepository
     * @param ResultFactory $resultFactory
     * @param ManagerInterface $messageManager
     * @param RequestInterface $request
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        ProductRepository $productRepository,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        RequestInterface $request,
        CartRepositoryInterface $cartRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->productRepository = $productRepository;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Add product to cart by ID and quantity.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $productId = (int)$this->request->getParam('id');
        $quantity = (int)$this->request->getParam('quantity', 1);
        
        if ($quantity < 1) {
            $quantity = 1;
        }

        $product = null;
        try {
            $product = $this->productRepository->getById($productId);

            // Only support simple products in this example
            if ($product->getTypeId() !== 'simple') {
                throw new \Exception(__('This endpoint only supports simple products.'));
            }

            // Use DataObject for buy request (Magento best practice)
            $buyRequest = new DataObject([
                'product' => $productId,
                'qty' => $quantity
            ]);

            $quote = $this->checkoutSession->getQuote();
            $quote->addProduct($product, $buyRequest);
            $quote->collectTotals();
            $this->cartRepository->save($quote);

            $this->messageManager->addSuccessMessage(__('Product "%1" added to cart.', $product->getName()));
        } catch (\Exception $e) {
            $productName = $product && $product->getId() ? $product->getName() : __('unknown product');
            $this->messageManager->addErrorMessage(
                __('Could not add "%1" to cart. Error: %2', $productName, $e->getMessage())
            );
            
            // Configure response
            $response->setHttpResponseCode(404);
            $response->setHeader('Status', '404 product not found', true);
        }

        return $response;
    }
}
