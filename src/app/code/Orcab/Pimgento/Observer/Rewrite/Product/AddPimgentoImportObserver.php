<?php
/**
 * Override Pimgento product observer
 *
 * @category  Orcab
 * @author    Matthieu Prigent <mapri@smile.fr>
 * @copyright 2016 Orcab
 */
namespace Orcab\Pimgento\Observer\Rewrite\Product;

use \Magento\Framework\DataObject;
use \Pimgento\Product\Observer\AddPimgentoImportObserver as BaseAddPimgentoImportObserver;

class AddPimgentoImportObserver extends BaseAddPimgentoImportObserver
{

    /**
     * Remove call to setRelated
     * {@inheritdoc}
     */
    protected function getStepsDefinition()
    {
        $stepsBefore = array(
            array(
                'comment' => __('Create temporary table'),
                'method'  => 'createTable',
            ),
            array(
                'comment' => __('Fill temporary table'),
                'method'  => 'insertData',
            ),
            array(
                'comment' => __('Add product required data'),
                'method'  => 'addRequiredData',
            ),
            array(
                'comment' => __('Create configurable product'),
                'method'  => 'createConfigurable',
            ),
            array(
                'comment' => __('Match code with Magento ID'),
                'method'  => 'matchEntity',
            ),
            array(
                'comment' => __('Match family code with Magento id'),
                'method'  => 'updateAttributeSetId',
            ),
            array(
                'comment' => __('Update column values for options'),
                'method'  => 'updateOption',
            ),
            array(
                'comment' => __('Create or update product entities'),
                'method'  => 'createEntities',
            ),
        );

        $responseUpdateEntities = new DataObject();
        $responseUpdateEntities->setUpdateEntitiesSteps([]);

        $this->eventManager->dispatch(
            'pimgento_product_import_update_entities_add_steps',
            ['response' => $responseUpdateEntities]
        );

        $afterEntitiesCreationSteps = array(
            array(
                'comment' => __('Set values to attributes'),
                'method'  => 'setValues',
            ),
            array(
                'comment' => __('Link configurable with children'),
                'method'  => 'linkConfigurable',
            ),
            array(
                'comment' => __('Set products to websites'),
                'method'  => 'setWebsites',
            ),
            array(
                'comment' => __('Set products to categories'),
                'method'  => 'setCategories',
            ),
            array(
                'comment' => __('Init stock'),
                'method'  => 'initStock',
            ),
            array(
                'comment' => __('Set Url Rewrite'),
                'method'  => 'setUrlRewrite',
            ),
        );

        $responseFinalSteps = new DataObject();
        $responseFinalSteps->setFinalSteps([]);

        $this->eventManager->dispatch(
            'pimgento_product_import_add_final_steps',
            ['response' => $responseFinalSteps]
        );

        $stepsAfter = array(
            array(
                'comment' => __('Drop temporary table'),
                'method'  => 'dropTable',
            ),
            array(
                'comment' => __('Clean cache'),
                'method'  => 'cleanCache',
            )
        );

        return array_merge(
            $stepsBefore,
            $responseUpdateEntities->getUpdateEntitiesSteps(),
            $afterEntitiesCreationSteps,
            $responseFinalSteps->getFinalSteps(),
            $stepsAfter
        );
    }
}
