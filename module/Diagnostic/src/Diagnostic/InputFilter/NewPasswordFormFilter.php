<?php
namespace Diagnostic\InputFilter;

use Zend\InputFilter\InputFilter;

/**
 * New Password Form Filter
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class NewPasswordFormFilter extends InputFilter
{
    public function __construct($adapter)
    {
        $this->add([
            'name' => 'password',
            'required' => true,
            'validators' => [
                [
                    'name' => 'Diagnostic\Validator\PasswordStrength',
                ],
            ],
        ]);

        $this->add([
            'name' => 'password2',
            'required' => true,
            'validators' => [
                [
                    'name' => 'Diagnostic\Validator\PasswordStrength',
                ],
                [
                    'name' => 'Identical',
                    'options' => [
                        'token' => 'password',
                    ],
                ],
            ],
        ]);
    }
}