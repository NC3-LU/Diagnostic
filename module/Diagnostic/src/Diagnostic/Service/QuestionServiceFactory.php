<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Diagnostic\Service;


class QuestionServiceFactory extends AbstractServiceFactory
{
    protected $resources = [
        'gateway' => 'Diagnostic\Gateway\QuestionGateway',
        'entity' => 'Diagnostic\Model\QuestionEntity',
        'config' => 'Config',
    ];
}