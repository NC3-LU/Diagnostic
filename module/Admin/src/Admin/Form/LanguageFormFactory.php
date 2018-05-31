<?php

namespace Admin\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Language Form Factory
 *
 * @package Admin\Factory
 * @author Romain DESJARDINS
 */
class LanguageFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $languages = $serviceLocator->getServiceLocator()->get('Diagnostic\Service\LanguageService')->getLanguages();
	$languages_ref = $serviceLocator->getServiceLocator()->get('Diagnostic\Service\LanguageService')->getLanguagesRef();

        $form = new LanguageForm();
        $form->setLanguages($languages);
        $form->setLanguagesRef($languages_ref);

        return $form;
    }
}
