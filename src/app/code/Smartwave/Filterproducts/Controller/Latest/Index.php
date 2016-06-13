<?php

namespace Smartwave\Filterproducts\Controller\Latest;

class Index extends \Magento\Framework\App\Action\Action {

    /**
     * Core registry
     *
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /*
     * Result page factory
     * 
     */
    protected $resultPageFactory;
    
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     */
    
    public function __construct(
    \Magento\Framework\App\Action\Context $context, 
            \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, 
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, 
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
            \Magento\Store\Model\StoreManagerInterface $storeManager, 
            \Magento\Framework\Registry $registry
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /*
     * load list.phtml file in frontend and set breadcrumb
     */

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $this->_objectManager->get('Smartwave\Filterproducts\Helper\Data')->getBreadcrumbs($resultPage, "Latest Product");
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
