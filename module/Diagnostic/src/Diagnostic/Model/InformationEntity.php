<?php
namespace Diagnostic\Model;

/**
 * Information Entity
 *
 * @package Diagnostic\Model
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class InformationEntity
{
    /**
     * Information
     */
    public $information;

    /**
     * @return mixed
     */
    public function getInformation()
    {
        return $this->information;
    }

    /**
     * @param mixed $information
     * @return InformationEntity
     */
    public function setInformation($information)
    {
        $this->information = $information;
        return $this;
    }

    /**
     * @param array $data
     */
    public function exchangeArray($data)
    {
        if (is_object($data)) {
            $data = (array)$data;
        }

        $this->information = (isset($data['information'])) ? $data['information'] : null;
    }

    /**
     * Get Array Copy
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}