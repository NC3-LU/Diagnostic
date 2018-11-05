<?php
namespace Admin\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Hostname;

/**
 * Email Not Exist Filter
 *
 * @package Admin\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class EmailNotExistFilter extends InputFilter
{
    public function __construct($adapter)
    {
        $this->add([
            'name' => 'email',
            'required' => true,
            'validators' => [
                [
                    'name' => 'Db\NoRecordExists',
                    'options' => [
                        'table' => 'users',
                        'field' => 'email',
                        'adapter' => $adapter
                    ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'email',
            'required' => true,
            'validators' => [
                [
                    'name' => 'EmailAddress',
                    'options' => [
                        'allow' => Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
                    ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'admin',
            'required' => false,
            'validators' => [
                [
                    'name' => 'Digits',
                ],
            ],
        ]);
    }
}
