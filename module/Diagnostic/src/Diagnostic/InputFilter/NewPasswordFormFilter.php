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
        $this->add(array(
            'name' => 'password',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Diagnostic\Validator\PasswordStrength',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'password2',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Diagnostic\Validator\PasswordStrength',
                ),
                array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'password',
                    ),
                ),
            ),
        ));
    }
}