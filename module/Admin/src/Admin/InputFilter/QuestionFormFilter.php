<?php
namespace Admin\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Hostname;

/**
 * Question Form Filter
 *
 * @package Admin\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class QuestionFormFilter extends InputFilter
{
    public function __construct($adapter)
    {
        $this->add(array(
            'name'       => 'translation_key',
            'required'   => true,
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'min' => 6
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'       => 'category_id',
            'required'   => true,
            'validators' => array(
                array(
                    'name'    => 'Db\RecordExists',
                    'options' => array(
                        'table' => 'categories',
                        'field' => 'id',
                        'adapter' => $adapter
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'       => 'threshold',
            'required'   => true,
            'validators' => array(
                array(
                    'name' => 'Digits',
                ),
            ),
        ));
    }
}

