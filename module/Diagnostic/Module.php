<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Diagnostic;

use Diagnostic\Gateway\CategoryGateway;
use Diagnostic\Gateway\QuestionGateway;
use Diagnostic\Gateway\UserGateway;
use Diagnostic\Gateway\UserTokenGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Session\Container;
use Zend\Validator\AbstractValidator;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $translator = $e->getApplication()->getServiceManager()->get('translator');
        AbstractValidator::setDefaultTranslator($translator);

        $container = new Container('diagnostic');
        if ($container->offsetExists('language')) {
            $translator->setLocale($container->language);
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'Diagnostic\Gateway\QuestionGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype($sm->get('Diagnostic\Model\QuestionEntity'));

                    $tableGateway = new TableGateway('questions', $dbAdapter, null, $resultSetPrototype);
                    $table = new QuestionGateway($tableGateway);

                    return $table;
                },
                'Diagnostic\Gateway\UserGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype($sm->get('Diagnostic\Model\UserEntity'));

                    $tableGateway = new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                    $table = new UserGateway($tableGateway);

                    return $table;
                },
                'Diagnostic\Gateway\UserTokenGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype($sm->get('Diagnostic\Model\UserTokenEntity'));

                    $tableGateway = new TableGateway('users_token', $dbAdapter, null, $resultSetPrototype);
                    $table = new UserTokenGateway($tableGateway);

                    return $table;
                },
            ],
        ];
    }
}
