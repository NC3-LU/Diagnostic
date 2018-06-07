<?php
/**
 * Diagnostic (https://github.com/CASES-LU/diagnostic)
 *
 * @link      https://github.com/CASES-LU/diagnostic for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   Diagnostic is licensed under the GNU Affero GPL v3
 */

namespace Admin\Controller;

use Diagnostic\Controller\AbstractControllerFactory;

class IndexControllerFactory extends AbstractControllerFactory
{
    protected $resources = [
        'userService' => 'Diagnostic\Service\UserService',
        'userTokenService' => 'Diagnostic\Service\UserTokenService',
        'questionService' => 'Diagnostic\Service\QuestionService',
	'categoryService' => 'Diagnostic\Service\CategoryService',
	'languageService' => 'Diagnostic\Service\LanguageService',
    ];

    protected $forms = [
        'user', 'adminQuestion', 'adminCategory', 'adminLanguage'
    ];
}
