<?php

namespace Potato\Zendesk\Block\Adminhtml\System\Config\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Widget\Button;
/**
 * Class GenerateButton
 */
class GenerateButton extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Potato_Zendesk::system/config/field/generateButton.phtml';

    /**
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Remove scope label
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            Button::class
        )->setData(
            [
                'id' => 'generate_token',
                'label' => __('Generate new token'),
                'on_click' => "setLocation('" . $this->getUrl('po_zendesk/system/generate') . "')",
            ]
        );

        return $button->toHtml();
    }
}
