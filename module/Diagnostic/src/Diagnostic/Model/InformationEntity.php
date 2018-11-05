<?php
namespace Diagnostic\Model;

/**
 * Information Entity
 *
 * @package Diagnostic\Model
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 * @author Romain Desjardins
 */
class InformationEntity
{
    /**
     * Information
     */
    public $information;

    /**
     * Information
     */
    public $activity;

    /**
     * Information
     */
    public $nb_employees;

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
     * @return mixed
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param mixed $information
     * @return InformationEntity
     */
    public function setActivity($information)
    {
        $this->activity = $information;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNb_employees()
    {
        return $this->nb_employees;
    }

    /**
     * @param mixed $information
     * @return InformationEntity
     */
    public function setNb_employees($information)
    {
        $this->nb_employees = $information;
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
        $this->activity = (isset($data['activity'])) ? $data['activity'] : null;
        $this->nb_employees = (isset($data['nb_employees'])) ? $data['nb_employees'] : null;
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
