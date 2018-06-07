<?php
/**
 * Diagnostic (https://github.com/CASES-LU/diagnostic)
 *
 * @link      https://github.com/CASES-LU/diagnostic for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   Diagnostic is licensed under the GNU Affero GPL v3
 */

namespace Diagnostic;

return [
    'controllers' => [
        'factories' => [
            'Diagnostic\Controller\Index' => 'Diagnostic\Controller\IndexControllerFactory',
        ],
    ],

    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => 'Diagnostic\Controller\Index',
                        'action' => 'index',
                    ],
                ],
            ],
            'diagnostic' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/diagnostic[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Diagnostic\Controller\Index',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],

    'service_manager' => [
        'invokables' => [
            'Diagnostic\Model\DiagnosticEntity' => 'Diagnostic\Model\DiagnosticEntity',
            'Diagnostic\Model\InformationEntity' => 'Diagnostic\Model\InformationEntity',
            'Diagnostic\Model\QuestionEntity' => 'Diagnostic\Model\QuestionEntity',
	    'Diagnostic\Model\CategoryEntity' => 'Diagnostic\Model\CategoryEntity',
            'Diagnostic\Model\UserEntity' => 'Diagnostic\Model\UserEntity',
            'Diagnostic\Model\UserTokenEntity' => 'Diagnostic\Model\UserTokenEntity',
            'Diagnostic\Service\Mime\Part' => 'Zend\Mime\Part',
            'Diagnostic\Service\Mime\Message' => 'Zend\Mime\Message',
            'Diagnostic\Service\Mail\Message' => 'Zend\Mail\Message',
            'Diagnostic\Service\Mail\Transport\Smtp' => 'Zend\Mail\Transport\Smtp',
            'Diagnostic\Service\Mail\Transport\SmtpOptions' => 'Zend\Mail\Transport\SmtpOptions',
        ],
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'factories' => [
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'Diagnostic\Service\QuestionService' => 'Diagnostic\Service\QuestionServiceFactory',
	    'Diagnostic\Service\CategoryService' => 'Diagnostic\Service\CategoryServiceFactory',
	    'Diagnostic\Service\LanguageService' => 'Diagnostic\Service\LanguageServiceFactory',
            'Diagnostic\Service\CalculService' => 'Diagnostic\Service\CalculServiceFactory',
            'Diagnostic\Service\MailService' => 'Diagnostic\Service\MailServiceFactory',
            'Diagnostic\Service\UserService' => 'Diagnostic\Service\UserServiceFactory',
            'Diagnostic\Service\UserTokenService' => 'Diagnostic\Service\UserTokenServiceFactory',
        ],
        'shared' => [
            'Diagnostic\Model\QuestionEntity' => false,
	    'Diagnostic\Model\CategoryEntity' => false,
        ],
    ],
    'form_elements' => [
        'invokables' => [
            'UploadForm' => 'Diagnostic\Form\UploadForm',
            'LoginForm' => 'Diagnostic\Form\LoginForm',
            'PasswordForgottenForm' => 'Diagnostic\Form\PasswordForgottenForm',
            'NewPasswordForm' => 'Diagnostic\Form\NewPasswordForm',
            'QuestionForm' => 'Diagnostic\Form\QuestionForm',
            'InformationForm' => 'Diagnostic\Form\InformationForm',
            'AddQuestionForm' => 'Diagnostic\Form\AddQuestionForm',
            'DownloadForm' => 'Diagnostic\Form\DownloadForm',
            'LinkDownloadForm' => 'Diagnostic\Form\LinkDownloadForm',
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    // Placeholder for console routes
    'console' => [
        'router' => [
            'routes' => [],
        ],
    ],

    'translator' => [
        'locale' => 'en',
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../../../language',
                'pattern' => '%s.mo',
            ],
        ],
    ],

    'navigation' => [
        'default' => [
            [
                'label' => '__users',
                'route' => 'admin',
                'action' => 'users',
            ],
            [
                'label' => '__questions',
                'route' => 'admin',
                'action' => 'questions',
            ],
	    [
                'label' => '__categories',
                'route' => 'admin',
                'action' => 'categories',
            ],
	    /*[
                'label' => '__languages',
                'route' => 'admin',
                'action' => 'languages',
            ],*/
        ],
    ],
];
