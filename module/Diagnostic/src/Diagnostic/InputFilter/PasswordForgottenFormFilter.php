<?php
namespace Diagnostic\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Hostname;

/**
 * Password Forgotten Form Filter
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class PasswordForgottenFormFilter extends InputFilter
{
    public function __construct($adapter)
    {
        $this->add(array(
            'name'       => 'email',
            'required'   => true,
            'validators' => array(
                array(
                    'name'    => 'EmailAddress',
                    'options' => array(
                        'allow' => Hostname::ALLOW_DNS,
                        'useMxCheck' => true
                    ),
                ),
            ),
        ));
    }
}

