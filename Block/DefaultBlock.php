<?php
declare(strict_types=1);

namespace Qonfi\Qonfi\Block;

use Magento\Catalog\Api\ProductRepositoryInterface;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\Locale\Resolver;

class DefaultBlock extends Template implements BlockInterface
{
    /**
     * ScopeConfigInterface instance
     *
     */
    protected $scopeConfig;

    /**
     * Registry instance
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var Resolver
     */
    protected $localeResolver;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * DefaultBlock constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     * @param Template\Context $context
     * @param Resolver $localeResolver
     * @param array $data
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        Resolver $localeResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setTemplate('Qonfi_Qonfi::block.phtml');
        $this->productRepository = $productRepository;
        $this->localeResolver = $localeResolver;
    }
    
    /**
     * GetQonfiUuid
     *
     * Retrieve the Qonfi UUID
     *
     * @return string
     */
    public function getQonfiUuid() : string
    {
        return $this->getData('data_qonfi_uuid');
    }

    /**
     * IsQonfiEnabled
     *
     * Check if Qonfi is enabled in the configuration
     *
     * @return bool
     */
    public function isQonfiEnabled()
    {
        return $this->_scopeConfig->isSetFlag(
            'qonfi_section/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCurrentProduct(): ?\Magento\Catalog\Api\Data\ProductInterface
    {
        $productId = (int) $this->getRequest()->getParam('id');
        return $productId ? $this->productRepository->getById($productId) : null;
    }
    
    

    /**
     * GetQonfiViewType
     *
     * Retrieve the Qonfi view type
     *
     * @return string
     */
    public function getQonfiViewType() : string
    {
        return $this->getData('data_qonfi_view_type');
    }

    /**
     * GetQonfiEnableProductCheck
     *
     * Retrieve the setting to enable product check
     *
     * @return string
     */
    public function getQonfiProductCheck() : bool
    {
        if ($this->getData('data_qonfi_product_check') !== null) {
            return $this->getData('data_qonfi_product_check') == 1;
        }
        return false;
    }

    /**
     * GetQonfiContentType
     *
     * Retrieve the Qonfi content type
     *
     * @return string
     */
    public function getQonfiContentType()
    {
        return $this->getData('data_qonfi_content_type');
    }

    /**
     * GetQonfiProductId
     *
     * Retrieve the current product ID if on a product page
     *
     * @return int|null
     */
    public function getQonfiProductId(): ?int
    {
        if ($this->getRequest()->getFullActionName() === 'catalog_product_view') {
            $productId = (int) $this->getRequest()->getParam('id');
            return $productId ?: null;
        }
        return null;
    }
    

    /**
     * GetLocale
     *
     * Retrieve the current locale
     *
     * @return int|null
     */
    public function getLanguage()
    {
        return substr($this->localeResolver->getLocale(), 0, 2);
    }
}
