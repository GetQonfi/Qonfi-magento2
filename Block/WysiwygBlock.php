<?php
declare(strict_types=1);

namespace Qonfi\Qonfi\Block;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magento\Framework\Locale\Resolver;

class WysiwygBlock extends Template implements BlockInterface
{
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $filterProvider = $objectManager->get(\Magento\Cms\Model\Template\FilterProvider::class);

        $html =  "<div class=\"qonfi-wysiwyg\">".$this->getData('data_qonfi_content_wysiwyg')."</div>";

        return $filterProvider->getPageFilter()->filter($html);
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
     * Retrieve the Qonfi product ID from the current product registry
     *
     * @return int|null
     */
    public function getQonfiProductId()
    {
        $objectManager = ObjectManager::getInstance();
        $product = $objectManager->get(Registry::class)->registry('current_product');

        return $product ? $product->getId() : null;
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
