<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Diagnostic\Controller\AbstractControllerFactory;

class IndexControllerFactory extends AbstractControllerFactory
{
    protected $resources = [
        'userService' => 'Diagnostic\Service\UserService',
        'userTokenService' => 'Diagnostic\Service\UserTokenService',
        'questionService' => 'Diagnostic\Service\QuestionService',
    ];

    protected $forms = [
        'user', 'adminQuestion'
    ];
}