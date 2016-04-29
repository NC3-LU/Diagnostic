<?php
namespace Diagnostic\Validator;

use Zend\Validator\AbstractValidator;

/**
 * Password Strength
 *
 * @package Diagnostic\Validator
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class PasswordStrength extends AbstractValidator
{
    const LENGTH = 'length';
    const UPPER  = 'upper';
    const LOWER  = 'lower';
    const DIGIT  = 'digit';
    const SPECIAL  = 'special';

    protected $messageTemplates = array(
        self::LENGTH => "password must be at least 8 characters in length",
        self::UPPER  => "password must contain at least one uppercase letter",
        self::LOWER  => "password must contain at least one lowercase letter",
        self::DIGIT  => "password must contain at least one digit character",
        self::SPECIAL  => "password must contain at least one special character"
    );

    /**
     * Is Valid
     *
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $this->setValue($value);

        $isValid = true;

        if (strlen($value) < 8 ) {
            $this->error(self::LENGTH);
            $isValid = false;
        }

        if (!preg_match('/[A-Z]/', $value)) {
            $this->error(self::UPPER);
            $isValid = false;
        }

        if (!preg_match('/[a-z]/', $value)) {
            $this->error(self::LOWER);
            $isValid = false;
        }

        if (!preg_match('/\d/', $value)) {
            $this->error(self::DIGIT);
            $isValid = false;
        }

        if (!preg_match('/[$&#@*%£"\[\](){}?;+=]/', $value)) {
            $this->error(self::SPECIAL);
            $isValid = false;
        }

        return $isValid;
    }
}

