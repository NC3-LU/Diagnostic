<?php

namespace Admin\Form;

use Admin\InputFilter\SettingFormFilter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Setting Form Factory
 *
 * @package Admin\Factory
 * @author Romain Desjardins
 */
class SettingFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $languages_ref = $serviceLocator->getServiceLocator()->get('Diagnostic\Service\LanguageService')->getLanguagesRef();

        $form = new SettingForm();

        $form->setLanguagesRef($languages_ref);

        $settingFormFilter = new SettingFormFilter($serviceLocator->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->setInputFilter($settingFormFilter);

        return $form;
    }
}
