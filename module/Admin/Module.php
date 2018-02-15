<?php
/**
 * Diagnostic (https://github.com/CASES-LU/diagnostic)
 *
 * @link      https://github.com/CASES-LU/diagnostic for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   Diagnostic is licensed under the GNU Affero GPL v3
 */

namespace Admin;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        //verify user is admin
        $sm = $e->getApplication()->getServiceManager();
        $userService = $sm->get('Diagnostic\Service\UserService');
        $admin = $userService->isAdmin();

        //module name
        $request = $e->getRouter()->match($e->getRequest());
        if ($request) {
            $params = $request->getParams();
            $controller = $params['controller'];
            $module_array = explode('\\', $controller);
            $moduleName = $module_array[0];


            if ((!$admin) && ($moduleName == 'Admin')) {

                $url = $e->getRouter()->assemble([], ['name' => 'diagnostic']);

                $response = $e->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(302);
                $response->sendHeaders();

                $stopCallBack = function ($event) use ($response) {
                    $event->stopPropagation();
                    return $response;
                };

                $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE, $stopCallBack, -10000);

                return $response;
            }
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
        return [];
    }
}
