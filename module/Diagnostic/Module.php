<?php
/**
 * Diagnostic (https://github.com/CASES-LU/diagnostic)
 *
 * @link      https://github.com/CASES-LU/diagnostic for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   Diagnostic is licensed under the GNU Affero GPL v3
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
