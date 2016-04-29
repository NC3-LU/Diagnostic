<?php
namespace Diagnostic\InputFilter;

use Zend\InputFilter\InputFilter;

/**
 * Download Form Filter
 *
 * @package Diagnostic\Form
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class DownloadFormFilter extends InputFilter
{
    public function __construct($adapter)
    {
        $this->add(array(
            'name'       => 'document',
            'required'   => true,
        ));

        $this->add(array(
            'name'       => 'company',
            'required'   => true,
        ));

        $this->add(array(
            'name'       => 'version',
            'required'   => true,
        ));

        $this->add(array(
            'name'       => 'state',
            'required'   => true,
        ));

        $this->add(array(
            'name'       => 'classification',
            'required'   => true,
        ));

        $this->add(array(
            'name'       => 'consultant',
            'required'   => true,
        ));

        $this->add(array(
            'name'       => 'client',
            'required'   => true,
        ));
    }
}

