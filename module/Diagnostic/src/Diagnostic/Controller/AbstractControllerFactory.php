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

class AbstractControllerFactory implements FactoryInterface
{
    protected $forms = [];
    protected $services = [];
    protected $entities = [];

    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $class = substr(get_class($this), 0, -7);

        if (class_exists($class)) {
            $sm = $serviceLocator->getServiceLocator();

            $controller = new $class();
            $controller->set('dbAdapter', $sm->get('Zend\Db\Adapter\Adapter'));
            $controller->set('config', $sm->get('Config'));
            $controller->set('translator', $sm->get('translator'));

            foreach ($this->forms as $form) {
                $controller->set($form . 'Form', $sm->get('formElementManager')->get(ucfirst($form) . 'Form'));
            }

            foreach ($this->services as $service) {
                $controller->set($service . 'Service', $sm->get('Diagnostic\Service\\' . ucfirst($service) . 'Service'));
            }

            foreach ($this->entities as $entity) {
                $controller->set($entity . 'Entity', $sm->get('Diagnostic\Model\\' . ucfirst($entity) . 'Entity'));
            }

            return $controller;
        } else {
            return false;
        }
    }
}
