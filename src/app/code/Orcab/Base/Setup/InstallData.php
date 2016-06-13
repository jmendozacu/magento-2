<?php
/**
 * Orcab Base setup
 *
 * @category  Orcab
 * @package   Orcab\Base
 * @author    Maxime Queneau <maxime.queneau@smile.fr>
 */
namespace Orcab\Base\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Install Data for Orcab Base
 *
 * @package   Orcab\Base\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * Website factory
     *
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * Store group factory
     *
     * @var \Magento\Store\Model\GroupFactory
     */
    protected $storeGroupFactory;

    /**
     * Store factory
     *
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     * Tax rate factory
     *
     * @var \Magento\Tax\Model\Calculation\RateFactory
     */
    protected $taxRateFactory;

    /**
     * Tax rule repository
     *
     * @var \Magento\Tax\Api\TaxRuleRepositoryInterface
     */
    protected $taxRuleRepository;

    /**
     * Tax rule factory
     *
     * @var \Magento\Tax\Api\Data\TaxRuleInterfaceFactory
     */
    protected $ruleFactory;


    /**
     * Resource config
     *
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $resourceConfig;

    /**
     * Default website
     *
     * @var array
     */
    protected $defaultWebsite = array(
        'id'   => 1,
        'code' => 'bac',
        'name' => 'Site bac.orcab.coop',
    );

    /**
     * Default Store group
     *
     * @var array
     */
    protected $defaultStoreGroup = array(
        'id'                => 1,
        'website_id'        => 1,
        'name'              => 'Orcab',
        'root_category_id'  => 2,
        'default_store_id'  => 1,
    );

    /**
     * Default Store
     *
     * @var array
     */
    protected $defaultStore = array(
        'id'       => 1,
        'group_id' => 1,
        'code'     => 'bac_fr',
        'name'     => 'Orcab FR',
    );

    /**
     * Tax definition
     *
     * @var array
     */
    protected $defaultProductTax = array(
        'code'           => 'TVA France 20%',
        'tax_country_id' => 'FR',
        'tax_region_id'  => '0',
        'tax_postcode'   => '*',
        'rate'           => '20.00',
    );

    /**
     * Tax definition
     *
     * @var array
     */
    protected $defaultRuleTax = array(
        'id'                 => 1,
        'code'               => 'TVA France 20%',
        'tax_customer_class' => 3,
        'tax_product_class'  => 2,
        'priority'           => 0,
        'calculate_subtotal' => false,
        'position'           => 0
    );

    /**
     * PHP Constructor
     * @param \Magento\Framework\App\State                  $appState
     * @param \Magento\Store\Model\WebsiteFactory           $websiteFactory
     * @param \Magento\Store\Model\GroupFactory             $storeGroupFactory
     * @param \Magento\Store\Model\StoreFactory             $storeFactory
     * @param \Magento\Tax\Model\Calculation\RateFactory    $taxRateFactory
     * @param \Magento\Tax\Api\TaxRuleRepositoryInterface   $taxRuleRepository
     * @param \Magento\Tax\Api\Data\TaxRuleInterfaceFactory $ruleFactory
     *
     * @return InstallData
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Store\Model\GroupFactory $storeGroupFactory,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Tax\Model\Calculation\RateFactory $taxRateFactory,
        \Magento\Tax\Api\TaxRuleRepositoryInterface $taxRuleRepository,
        \Magento\Tax\Api\Data\TaxRuleInterfaceFactory $ruleFactory,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig
    ) {
        $appState->setAreaCode('adminhtml');

        $this->websiteFactory    = $websiteFactory;
        $this->storeGroupFactory = $storeGroupFactory;
        $this->storeFactory      = $storeFactory;
        $this->taxRateFactory    = $taxRateFactory;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->ruleFactory       = $ruleFactory;
        $this->resourceConfig    = $resourceConfig;
    }

    /**
     * Installs data
     *
     * @param ModuleDataSetupInterface $setup   Setup
     * @param ModuleContextInterface   $context Context
     *
     * @return void
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        /**
         * WEBSITE / STORE GROUP / STORE
         */
        $websiteModel = $this->websiteFactory->create()->load($this->defaultWebsite['id']);
        $websiteModel->addData($this->defaultWebsite);
        $websiteModel->save();

        $GroupModel = $this->storeGroupFactory->create()->load($this->defaultStoreGroup['id']);
        $GroupModel->addData($this->defaultStoreGroup);
        $GroupModel->save();

        $StoreModel = $this->storeFactory->create()->load($this->defaultStore['id']);
        $StoreModel->addData($this->defaultStore);
        $StoreModel->save();


        /**
         * TAX
         */
        /** @var $rateModel \Magento\Tax\Model\Calculation\Rate */
        $rateModel = $this->taxRateFactory->create()->loadByCode($this->defaultProductTax['code']);
        $rateModel->addData($this->defaultProductTax);
        $rateModel->save();

        $taxRule = $this->ruleFactory->create()->load($this->defaultRuleTax['id']);
        $taxRule->setCode($this->defaultRuleTax['code'])
            ->setTaxRateIds([$rateModel->getId()])
            ->setCustomerTaxClassIds([$this->defaultRuleTax['tax_customer_class']])
            ->setProductTaxClassIds([$this->defaultRuleTax['tax_product_class']])
            ->setPriority($this->defaultRuleTax['priority'])
            ->setCalculateSubtotal($this->defaultRuleTax['calculate_subtotal'])
            ->setPosition($this->defaultRuleTax['position']);
        $this->taxRuleRepository->save($taxRule);


        // Define theme
        $this->resourceConfig->saveConfig('design/theme/theme_id', 4, 'default', 0);

        // Desactive stock
        $this->resourceConfig->saveConfig('cataloginventory/item_options/manage_stock', 0, 'default', 0);

        // Hide checkout
        $this->resourceConfig->saveConfig('advanced/modules_disable_output/Magento_Checkout', 1, 'default', 0);

        // Add category limit for menu
        $this->resourceConfig->saveConfig('catalog/navigation/max_depth', 3, 'default', 0);

        // Display category in product url
        $this->resourceConfig->saveConfig('catalog/seo/product_use_categories', 1, 'default', 0);

        // State, locale...
        $this->resourceConfig->saveConfig('general/country/allow', 'FR', 'default', 0);
        $this->resourceConfig->saveConfig('general/locale/code', 'fr_FR', 'default', 0);
        $this->resourceConfig->saveConfig('general/locale/timezone', 'Europe/Paris', 'default', 0);
        $this->resourceConfig->saveConfig('general/locale/weight_unit', 'kgs', 'default', 0);
        $this->resourceConfig->saveConfig('general/locale/firstday', 1, 'default', 0);
        $this->resourceConfig->saveConfig('general/locale/weekend', '0,6', 'default', 0);
        $this->resourceConfig->saveConfig('general/store_information/name', 'ORCAB', 'default', 0);
        $this->resourceConfig->saveConfig('general/store_information/country_id', 'FR', 'default', 0);
        $this->resourceConfig->saveConfig('general/store_information/postcode', '85620', 'default', 0);
        $this->resourceConfig->saveConfig('general/store_information/city', 'Rocheservière', 'default', 0);
        $this->resourceConfig->saveConfig('general/store_information/street_line1', 'Zone Artisanale Genêts', 'default', 0);

        // Tax, price
        $this->resourceConfig->saveConfig('tax/defaults/country', 'FR', 'default', 0);
        $this->resourceConfig->saveConfig('tax/display/type', 2, 'default', 0);
        $this->resourceConfig->saveConfig('tax/display/shipping', 2, 'default', 0);

        // Design
        $this->resourceConfig->saveConfig('porto_settings/category/rating_star', 0, 'default', 0);
        $this->resourceConfig->saveConfig('catalog/frontend/grid_per_page_values', '12,24,36', 'default', 0);
        $this->resourceConfig->saveConfig('catalog/frontend/grid_per_page', '12', 'default', 0);


        // Image
        $this->resourceConfig->saveConfig('catalog/fields_masks/img', 'https://*/*/*|L400|*', 'default', 0);
        $this->resourceConfig->saveConfig('catalog/fields_masks/thumb', 'https://*/*/*|L150|*', 'default', 0);
        $this->resourceConfig->saveConfig('catalog/fields_masks/zoom', 'https://*/*/*|L1200|*', 'default', 0);
    }
}
