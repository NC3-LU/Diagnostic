<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Diagnostic\Controller;

class IndexControllerFactory extends AbstractControllerFactory
{
    protected $resources = [
        'config' => 'Config',
        'translator' => 'translator',
        'questionService' => 'Diagnostic\Service\QuestionService',
        'categoryService' => 'Diagnostic\Service\CategoryService',
        'userService' => 'Diagnostic\Service\UserService',
        'userTokenService' => 'Diagnostic\Service\UserTokenService',
        'mailService' => 'Diagnostic\Service\MailService',
        'calculService' => 'Diagnostic\Service\CalculService',
        'diagnosticEntity' => 'Diagnostic\Model\DiagnosticEntity',
        'informationEntity' => 'Diagnostic\Model\InformationEntity',
        'questionEntity' => 'Diagnostic\Model\QuestionEntity',
        'categoryEntity' => 'Diagnostic\Model\CategoryEntity',
    ];

    protected $forms = [
        'upload', 'login', 'question', 'information', 'addQuestion',
        'passwordForgotten', 'newPassword', 'linkDownload', 'download'
    ];

}
