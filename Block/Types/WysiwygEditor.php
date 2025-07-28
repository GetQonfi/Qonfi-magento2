<?php
/**
 * Qonfi Block wysiwyg editor block
 *
 * @package Qonfi\Qonfi
 * @author Ivar Post <ivar@getqonfi.nl>
 * @license Open Software License (OSL 3.0)
 * @link https://gitlab.com/jos17/qonfimagento
 */

declare(strict_types=1);

namespace Qonfi\Qonfi\Block\Types;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;

/**
 * Class WysiwygEditor
 *
 * This class provides a WYSIWYG editor for use in Magento forms.
 * It extends the Template class and uses the ElementFactory to create editor elements.
 */
class WysiwygEditor extends Template
{
    /**
     * @var ElementFactory
     */
    private $elementFactory;

    /**
     * @var WysiwygConfig
     */
    private $wysiwygConfig;

    /**
     * WysiwygEditor constructor.
     *
     * @param Template\Context $context
     * @param ElementFactory $elementFactory
     * @param WysiwygConfig $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ElementFactory $elementFactory,
        WysiwygConfig $wysiwygConfig,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        $this->wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    /**
     * Prepare the HTML for the WYSIWYG editor element
     *
     * @param AbstractElement $element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        $editor = $this->elementFactory->create('editor', ['data' => $element->getData()]);
        $editor->setId($element->getId());
        $editor->setForm($element->getForm());
        $editor->setClass('widget-option input-text admin__control admin__control-wysiwyg');
        $editor->setWysiwyg(true);
        $editor->setConfig($this->wysiwygConfig->getConfig([
            'add_variables' => true,
            'add_widgets' => true,
            'add_images' => false,
        ]));

        $element->setData('after_element_html', $editor->getElementHtml());

        return $element;
    }
}
