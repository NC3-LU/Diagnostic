<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin;

return [
    'controllers' => [
        'factories' => [
            'Admin\Controller\Index' => 'Admin\Controller\IndexControllerFactory',
        ],
    ],

    'router' => [
        'routes' => [
            'admin' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/admin[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Admin\Controller\Index',
                        'action' => 'users',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'service_manager' => [
        'invokables' => [],
    ],

    'form_elements' => [
        'factories' => [
            'AdminQuestionForm' => 'Admin\Form\QuestionFormFactory',
            'AdminCategoryForm' => 'Admin\Form\CategoryFormFactory',
            'AdminLanguageForm' => 'Admin\Form\LanguageFormFactory',
            'AdminAddTranslationForm' => 'Admin\Form\AddTranslationFormFactory',
        ],
        'invokables' => [
            'UserForm' => 'Admin\Form\UserForm',
            'NewQuestionForm' => 'Admin\Form\QuestionForm',
            'NewCategoryForm' => 'Admin\Form\CategoryForm',
            'NewLanguageForm' => 'Admin\Form\LanguageForm',
            'NewAddTranslationForm' => 'Admin\Form\AddTranslationForm',
        ],
    ],
];
