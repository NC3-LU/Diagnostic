<?php
namespace Admin\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Hostname;

/**
 * Add translation Form Filter
 *
 * @package Admin\Form
 * @author Romain DESJARDINS
 */
class AddTranslationFormFilter extends InputFilter
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
