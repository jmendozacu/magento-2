<?php
/**
 * Filter renderer block
 *
 * @category  Orcab
 * @author    Matthieu Prigent <mapri@smile.fr>
 * @copyright 2016 Orcab
 */
namespace Orcab\LayeredNavigation\Rewrite\Block\Navigation;

use Amasty\Shopby\Helper\FilterSetting;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\View\Element\Template\Context;
use Amasty\Shopby\Api\Data\FilterSettingInterface;
use Amasty\Shopby\Model\Source\DisplayMode;

class FilterRenderer extends \Amasty\Shopby\Block\Navigation\FilterRenderer
{
    /** @var CategoryFactory $categoryFactory */
    protected $categoryFactory;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        Context $context,
        CategoryFactory $categoryFactory,
        FilterSetting $settingHelper,
        array $data = [])
    {
        parent::__construct($context, $settingHelper, $data);
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Get category url
     *
     * @param $id
     *
     * @return null|string
     */
    public function getCategoryUrl($id)
    {
        $category = $this->categoryFactory->create()->load($id);
        if ($category->getId()) {
            return $category->getUrl();
        }
        return null;
    }

    /**
     * Overwrite to set good templates
     *
     * {@inheritdoc}
     */
    protected function getTemplateByFilterSetting(FilterSettingInterface $filterSetting)
    {
        switch($filterSetting->getDisplayMode()) {
            case DisplayMode::MODE_SLIDER:
                $template = "Amasty_Shopby::layer/filter/slider.phtml";
                break;
            case DisplayMode::MODE_DROPDOWN:
                $template = "Amasty_Shopby::layer/filter/dropdown.phtml";
                break;
            default:
                $template = "Amasty_Shopby::layer/filter/default.phtml";
                break;
        }
        return $template;
    }
}
