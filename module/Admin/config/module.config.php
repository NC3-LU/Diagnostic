<?php
/**
 * Diagnostic (https://github.com/CASES-LU/diagnostic)
 *
 * @link      https://github.com/CASES-LU/diagnostic for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   Diagnostic is licensed under the GNU Affero GPL v3
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
        ],
        'invokables' => [
            'UserForm' => 'Admin\Form\UserForm',
            'NewQuestionForm' => 'Admin\Form\QuestionForm',
	    'NewCategoryForm' => 'Admin\Form\CategoryForm',
	    'NewLanguageForm' => 'Admin\Form\LanguageForm',
        ],
    ],
];
