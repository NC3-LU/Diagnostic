<?php

namespace Admin\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Template Form Factory
 *
 * @package Admin\Factory
 * @author Romain DESJARDINS
 */
class TemplateFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $form = new TemplateForm();
        return $form;
    }
}
