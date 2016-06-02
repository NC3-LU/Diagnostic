<?php
namespace Diagnostic\InputFilter;

use Zend\InputFilter\InputFilter;

/**
 * Add Question Form Filter
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class AddQuestionFormFilter extends InputFilter
{
    public function __construct($adapter)
    {
        $this->add(array(
            'name'       => 'question',
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
    }
}

