<?php
declare(strict_types=1);

namespace Qonfi\Qonfi\Block;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\Locale\Resolver;

class WysiwygBlock extends Template implements BlockInterface
{
    /**
     * @var Resolver
     */
    protected $localeResolver;

    /**
     * WysiwygBlock constructor.
     *
     * @param Template\Context $context
     * @param Resolver $localeResolver
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Resolver $localeResolver,  // Correct injection
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setTemplate('Qonfi_Qonfi::block_wysiwyg.phtml');
        $this->localeResolver = $localeResolver;  // Assign to property
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
     * GetQonfiWysiwygContentHtml
     *
     * Retrieve the Qonfi WYSIWYG content HTML based on the view type
     *
     * @return string
     */
    public function getQonfiContentHtml() : string
    {
        $html =  "<div class=\"qonfi-wysiwyg\">".$this->getData('data_qonfi_content_wysiwyg')."</div>";

        return $html;
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
     * GetQonfiProductId
     *
     * Retrieve the current product ID if on a product page
     *
     * @return int|null
     */
    public function getQonfiProductId(): ?int
    {
        if ($this->getRequest()->getFullActionName() !== 'catalog_product_view') {
            return null;
        }

        $productId = (int)$this->getRequest()->getParam('id');
        if (!$productId) {
            return null;
        }

        $product = $this->productRepository->getById($productId);
        $productTypeId = $product->getTypeId();
        
        if ($productTypeId === 'configurable') {
            $usedProducts = $product->getTypeInstance()->getUsedProducts($product);
            foreach ($usedProducts as $child) {
                return (int)$child->getId();
            }
        }

        return (int)$product->getId();
    }

    /**
     * GetLanguage
     *
     * Retrieve the Qonfi locale (language code)
     *
     * @return string
     */
    public function getLanguage()
    {
        return substr($this->localeResolver->getLocale(), 0, 2);
    }
}
