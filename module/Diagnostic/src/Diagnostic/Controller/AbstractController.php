<?php
/**
 * Diagnostic (https://github.com/CASES-LU/diagnostic)
 *
 * @link      https://github.com/CASES-LU/diagnostic for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Cases is a registered trademark of SECURITYMADEIN.LU
 * @license   Diagnostic is licensed under the GNU Affero GPL v3
 */

namespace Diagnostic\Controller;

use Zend\Mvc\Controller\AbstractActionController;

abstract class AbstractController extends AbstractActionController
{
    protected $dbAdapter;

    /**
     * Get
     *
     * @param $value
     * @return mixed
     */
    public function get($value) {
        return $this->$value;
    }

    /**
     * Set
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value) {
        $this->$key = $value;
        return $this;
    }
}