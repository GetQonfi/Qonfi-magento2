<?php
namespace Qonfi\Qonfi\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Enabledisable implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Enabled')],
            ['value' => 0, 'label' => __('Disabled')]
        ];
    }
}
