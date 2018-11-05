<?php

namespace Admin\Form;

use Admin\InputFilter\AddTranslationFormFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Add Translation Form Factory
 *
 * @package Admin\Factory
 * @author Romain Desjardins
 */
class AddTranslationFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $form = new AddTranslationForm();

        $addTranslationFormFilter = new AddTranslationFormFilter($serviceLocator->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->setInputFilter($addTranslationFormFilter);

        return $form;
    }
}
