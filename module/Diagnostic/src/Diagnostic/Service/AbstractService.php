<?php
namespace Diagnostic\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * AbstractService
 *
 * @package Diagnostic\Service
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
abstract class AbstractService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $gateway;
    protected $entity;

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