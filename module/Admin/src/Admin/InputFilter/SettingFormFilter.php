<?php
namespace Admin\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Hostname;

/**
 * Setting Form Filter
 *
 * @package Admin\Form
 * @author Romain Desjardins
 */
class SettingFormFilter extends InputFilter
{
    public function __construct($adapter)
    {
        $this->add([
            'name' => 'encryption_key',
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 3
                    ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'diagnosis_stat',
            'validators' => [
                [
                    'name' => 'Digits',
                ],
            ],
        ]);
    }
}
