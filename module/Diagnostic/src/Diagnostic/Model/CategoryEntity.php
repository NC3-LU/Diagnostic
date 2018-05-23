<?php
namespace Diagnostic\Model;

/**
 * Question Entity
 *
 * @package Diagnostic\Model
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class CategoryEntity
{
    /**
     * Id
     */
    public $id;

    /**
     * Translation Key
     */
    public $translation_key;

    /**
     * New
     */
    public $new = false;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return QuestionEntity
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTranslationKey()
    {
        return $this->translation_key;
    }

    /**
     * @param mixed $translation_key
     * @return QuestionEntity
     */
    public function setTranslationKey($translation_key)
    {
        $this->translation_key = $translation_key;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNew()
    {
        return $this->new;
    }

    /**
     * @param mixed $new
     * @return QuestionEntity
     */
    public function setNew($new)
    {
        $this->new = $new;
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

        //id
        if (isset($data['id'])) {
            $this->id = $data['id'];
        } else {
            $this->id = null;
        }

        $this->translation_key = (isset($data['translation_key'])) ? $data['translation_key'] : null;
        $this->new = (isset($data['new'])) ? $data['new'] : false;
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
