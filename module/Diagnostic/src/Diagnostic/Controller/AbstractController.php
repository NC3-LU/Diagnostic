<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
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