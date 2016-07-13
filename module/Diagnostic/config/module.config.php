<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Diagnostic;

return array(
    'controllers' => array(
        'invokables' => array(
            'Diagnostic\Controller\Index' => 'Diagnostic\Controller\IndexController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Diagnostic\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'diagnostic' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/diagnostic[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Diagnostic\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'service_manager' => array(
        'invokables' => array(
            'Diagnostic\Model\QuestionEntity' => 'Diagnostic\Model\QuestionEntity',
            'Diagnostic\Model\UserEntity' => 'Diagnostic\Model\UserEntity',
            'Diagnostic\Model\DiagnosticEntity' => 'Diagnostic\Model\DiagnosticEntity',
            'Diagnostic\Model\InformationEntity' => 'Diagnostic\Model\InformationEntity',
            'Diagnostic\Model\UserTokenEntity' => 'Diagnostic\Model\UserTokenEntity',
            'Diagnostic\Service\UserService' => 'Diagnostic\Service\UserService',
            'Diagnostic\Service\QuestionService' => 'Diagnostic\Service\QuestionService',
            'Diagnostic\Service\UserTokenService' => 'Diagnostic\Service\UserTokenService',
            'Diagnostic\Service\MailService' => 'Diagnostic\Service\MailService',
            'Diagnostic\Service\CalculService' => 'Diagnostic\Service\CalculService',
            'Diagnostic\Service\Mime\Part' => 'Zend\Mime\Part',
            'Diagnostic\Service\Mime\Message' => 'Zend\Mime\Message',
            'Diagnostic\Service\Mail\Message' => 'Zend\Mail\Message',
            'Diagnostic\Service\Mail\Transport\Smtp' => 'Zend\Mail\Transport\Smtp',
            'Diagnostic\Service\Mail\Transport\SmtpOptions' => 'Zend\Mail\Transport\SmtpOptions',
        ),
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
        'shared' => array(
            'Diagnostic\Model\QuestionEntity' => false,
        ),
    ),
    'form_elements' => array(
        'invokables' => array(
            'UploadForm' => 'Diagnostic\Form\UploadForm',
            'LoginForm' => 'Diagnostic\Form\LoginForm',
            'PasswordForgottenForm' => 'Diagnostic\Form\PasswordForgottenForm',
            'NewPasswordForm' => 'Diagnostic\Form\NewPasswordForm',
            'QuestionForm' => 'Diagnostic\Form\QuestionForm',
            'InformationForm' => 'Diagnostic\Form\InformationForm',
            'AddQuestionForm' => 'Diagnostic\Form\AddQuestionForm',
            'DownloadForm' => 'Diagnostic\Form\DownloadForm',
            'LinkDownloadForm' => 'Diagnostic\Form\LinkDownloadForm',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),

    'translator' => array(
        'locale' => 'fr_FR',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../../../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),

    'navigation' => array(
        'default' => array(
            array(
                'label' => '__users',
                'route' => 'admin',
                'action' => 'users',
            ),
            array(
                'label' => '__questions',
                'route' => 'admin',
                'action' => 'questions',
            ),
        ),
    ),
);
