<?php
/**
 * Diagnostic (https://github.com/CASES-LU/diagnostic)
 *
 * @link      https://github.com/CASES-LU/diagnostic for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   Diagnostic is licensed under the GNU Affero GPL v3
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