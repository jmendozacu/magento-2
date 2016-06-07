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
        \Magento\Tax\Api\Data\TaxRuleInterfaceFactory $ruleFactory
    ) {
        $appState->setAreaCode('adminhtml');

        $this->websiteFactory    = $websiteFactory;
        $this->storeGroupFactory = $storeGroupFactory;
        $this->storeFactory      = $storeFactory;
        $this->taxRateFactory    = $taxRateFactory;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->ruleFactory       = $ruleFactory;
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

        $taxRule = $this->ruleFactory->create();
        $taxRule->setCode($this->defaultRuleTax['code'])
            ->setTaxRateIds([$rateModel->getId()])
            ->setCustomerTaxClassIds([$this->defaultRuleTax['tax_customer_class']])
            ->setProductTaxClassIds([$this->defaultRuleTax['tax_product_class']])
            ->setPriority($this->defaultRuleTax['priority'])
            ->setCalculateSubtotal($this->defaultRuleTax['calculate_subtotal'])
            ->setPosition($this->defaultRuleTax['position']);
        $this->taxRuleRepository->save($taxRule);
    }
}
