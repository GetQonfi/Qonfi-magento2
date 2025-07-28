<?php

declare(strict_types=1);

namespace Qonfi\Qonfi\Block\Types;

/**
 * Qonfi Block colorpicker
 */
class ColorPicker extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * Prepare the HTML for the color picker element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Generate the input field manually
        $inputId = $element->getHtmlId();
        $inputName = $element->getName();
        $value = $element->getData('value') ?: '';
        $inputHtml = "<input type=\"text\" id=\"$inputId\" name=\"$inputName\" value=\"$value\" class=\"input-text\"/>";

        // Attach the color picker
        $inputHtml .= '<script type="text/javascript">
            require(["jquery", "jquery/colorpicker/js/colorpicker"], function ($) {
                $(function () {
                    var $el = $("#' . $inputId . '");
                    $el.css("backgroundColor", "' . $value . '");
                    $el.ColorPicker({
                        color: "' . $value . '",
                        onChange: function (hsb, hex, rgb) {
                            $el.css("backgroundColor", "#" + hex).val("#" + hex);
                        }
                    });
                });
            });
        </script>';

        // Set the field HTML
        $element->setData('after_element_html', $inputHtml);
        return $element;
    }
}
