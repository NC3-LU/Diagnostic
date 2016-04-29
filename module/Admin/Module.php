<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
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
        $eventManager        = $e->getApplication()->getEventManager();
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

                $url = $e->getRouter()->assemble(array(), array('name' => 'diagnostic'));

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
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(

        );
    }
}
