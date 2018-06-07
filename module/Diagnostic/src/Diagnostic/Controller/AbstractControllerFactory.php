<?php
/**
 * Diagnostic (https://github.com/CASES-LU/diagnostic)
 *
 * @link      https://github.com/CASES-LU/diagnostic for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   Diagnostic is licensed under the GNU Affero GPL v3
 */

namespace Diagnostic\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractControllerFactory implements FactoryInterface
{
    protected $forms = [];
    protected $resources = [];

    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $class = substr(get_class($this), 0, -7);

        if (class_exists($class)) {
            $controller = new $class();
            $controller->set('dbAdapter', $serviceLocator->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            foreach($this->resources as $key => $resource) {
                $controller->set($key, $serviceLocator->getServiceLocator()->get($resource));
            }
            foreach ($this->forms as $form) {
                $controller->set($form . 'Form', $serviceLocator->getServiceLocator()->get('formElementManager')->get(ucfirst($form) . 'Form'));
            }

            return $controller;
        } else {
            return false;
        }
    }
}
