<?php

namespace Admin\Form;

use Admin\InputFilter\AddLanguageFormFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Add Language Form Factory
 *
 * @package Admin\Factory
 * @author Romain DESJARDINS
 */
class AddLanguageFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $form = new AddLanguageForm();

        $addLanguageFormFilter = new AddLanguageFormFilter($serviceLocator->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->setInputFilter($addLanguageFormFilter);

        return $form;
    }
}
