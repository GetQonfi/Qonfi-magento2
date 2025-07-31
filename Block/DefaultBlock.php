<?php
declare(strict_types=1);

namespace Qonfi\Qonfi\Block;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
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
     * DefaultBlock constructor.
     *
     * @param Template\Context $context
     * @param Resolver $localeResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Resolver $localeResolver,
        array $data = []
    ) {
        $this->setTemplate('Qonfi_Qonfi::block.phtml');
        parent::__construct($context, $data);
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
     * GetLocale
     *
     * Retrieve the Qonfi category ID from the current category registry
     *
     * @return int|null
     */
    public function getLanguage()
    {
        return substr($this->localeResolver->getLocale(), 0, 2);
    }
}
