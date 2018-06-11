<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
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
