<?php
namespace Admin\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Hostname;

/**
 * Setting Form Filter
 *
 * @package Admin\Form
 * @author Romain DESJARDINS
 */
class SettingFormFilter extends InputFilter
{
    public function __construct($adapter)
    {
        $this->add([
            'name' => 'encryption_key',
            'required' => true,
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 8
                    ],
                ],
            ],
        ]);
    }
}
