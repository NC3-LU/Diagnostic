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
    protected $forms = [
        'upload', 'login', 'question', 'information', 'addQuestion',
        'passwordForgotten', 'newPassword', 'linkDownload', 'download'
    ];
    protected $services = ['question', 'user', 'userToken', 'mail', 'calcul'];
    protected $entities = ['diagnostic', 'information', 'question'];
}
