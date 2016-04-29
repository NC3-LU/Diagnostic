<?php
namespace Diagnostic\Model;

/**
 * Diagnostic Entity
 *
 * @package Diagnostic\Model
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class DiagnosticEntity
{
    /**
     * Maturity
     */
    public $maturity;

    /**
     * Maturity target
     */
    public $maturityTarget;

    /**
     * Recommandation
     */
    public $recommandation;

    /**
     * Gravity
     */
    public $gravity;

    /**
     * Notes
     */
    public $notes;

    /**
     * @return mixed
     */
    public function getMaturity()
    {
        return $this->maturity;
    }

    /**
     * @param mixed $maturity
     * @return DiagnosticEntity
     */
    public function setMaturity($maturity)
    {
        $this->maturity = $maturity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaturityTarget()
    {
        return $this->maturityTarget;
    }

    /**
     * @param mixed $maturityTarget
     * @return DiagnosticEntity
     */
    public function setMaturityTarget($maturityTarget)
    {
        $this->maturityTarget = $maturityTarget;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRecommandation()
    {
        return $this->recommandation;
    }

    /**
     * @param mixed $recommandation
     * @return DiagnosticEntity
     */
    public function setRecommandation($recommandation)
    {
        $this->recommandation = $recommandation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGravity()
    {
        return $this->gravity;
    }

    /**
     * @param mixed $gravity
     * @return DiagnosticEntity
     */
    public function setGravity($gravity)
    {
        $this->gravity = $gravity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     * @return DiagnosticEntity
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
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

        $this->maturity  = (isset($data['maturity'])) ? $data['maturity'] : null;
        $this->maturityTarget  = (isset($data['maturityTarget'])) ? $data['maturityTarget'] : null;
        $this->recommandation  = (isset($data['recommandation'])) ? $data['recommandation'] : null;
        $this->gravity  = (isset($data['gravity'])) ? $data['gravity'] : null;
        $this->notes  = (isset($data['notes'])) ? $data['notes'] : null;
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