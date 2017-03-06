<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Diagnostic\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractServiceFactory implements FactoryInterface
{
    protected $resources = [];

    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $class = substr(get_class($this), 0, -7);

        if (class_exists($class)) {
            $service = new $class();
            foreach($this->resources as $key => $resource) {
                $service->set($key, $serviceLocator->get($resource));
            }

            return $service;
        } else {
            return false;
        }
    }
}