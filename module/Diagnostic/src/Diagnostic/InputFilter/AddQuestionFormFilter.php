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
        $this->add([
            'name' => 'question',
            'required' => true,
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 6
                    ],
                ],
            ],
        ]);
    }
}