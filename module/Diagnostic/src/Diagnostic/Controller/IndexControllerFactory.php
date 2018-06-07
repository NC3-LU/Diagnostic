<?php
/**
 * Diagnostic (https://github.com/CASES-LU/diagnostic)
 *
 * @link      https://github.com/CASES-LU/diagnostic for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   Diagnostic is licensed under the GNU Affero GPL v3
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
	'categoryEntity' => 'Diagnostic\Model\CategoryEntity'
    ];

    protected $forms = [
        'upload', 'login', 'question', 'information', 'addQuestion',
        'passwordForgotten', 'newPassword', 'linkDownload', 'download'
    ];

}
