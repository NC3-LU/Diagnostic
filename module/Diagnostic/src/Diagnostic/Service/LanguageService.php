<?php
namespace Diagnostic\Service;

use Zend\Crypt\BlockCipher;
use Zend\Session\Container;

/**
 * QuestionService
 *
 * @package Diagnostic\Service
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class LanguageService extends AbstractService
{
    protected $config;

    /**
     * Get language
     *
     * @return array
     * @throws \Exception
     */
    public function getLanguages()
    {
	$languages = ['test', 'test1'];
        return $languages;
    }
}
