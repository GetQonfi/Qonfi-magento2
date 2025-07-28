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
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

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
     * @var Logger
     */
    protected $logger;
    /**
     * @var UrlBuilder
     */
    protected $urlBuilder;

    /**
     * Index constructor.
     * @param CheckoutSession $checkoutSession
     * @param ProductRepository $productRepository
     * @param ResultFactory $resultFactory
     * @param ManagerInterface $messageManager
     * @param RequestInterface $request
     * @param CartRepositoryInterface $cartRepository
     * @param UrlInterface $urlBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        ProductRepository $productRepository,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        RequestInterface $request,
        CartRepositoryInterface $cartRepository,
        UrlInterface $urlBuilder,
        LoggerInterface $logger
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->productRepository = $productRepository;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->cartRepository = $cartRepository;
        $this->urlBuilder = $urlBuilder;
        $this->logger = $logger;
    }

    /**
     * Add product to cart by ID and quantity.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $productId = (int)$this->request->getParam('id');
        $quantity = (int)$this->request->getParam('quantity', 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        // Use Magento's URL builder for robust cart URL generation
        $redirectUrl = $this->request->getParam('redirect_url');
        if (!$redirectUrl || strpos($redirectUrl, '/') !== 0 || strpos($redirectUrl, '//') === 0) {
            $redirectUrl = $this->urlBuilder->getUrl('checkout/cart');
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
            $productName = $product && $product->getId() ? $product->getName() : __('(unknown product)');
            $this->messageManager->addErrorMessage(__('Could not add "%1" to cart.', $productName));
            $this->logger->error('Qonfi_AddToCart: Exception: ' . $e->getMessage());
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($redirectUrl);
        return $resultRedirect;
    }
}
