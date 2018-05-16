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
class CategoryFormFilter extends InputFilter
{
    public function __construct($adapter)
    {
        $this->add([
            'name' => 'translation_key',
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