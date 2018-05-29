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
	$languages = [];
	$file_temp = fopen('/var/www/diagnostic/language/code_country.txt', 'r');
	while (!feof($file_temp)) {
	    $temp_lang = fgets($file_temp, 4096);
	    array_push($languages, substr($temp_lang, 0, -1));
	}
	fclose($file_temp);

	// delete the break line
	array_pop($languages);

        return $languages;
    }

    /**
     * Get language
     *
     * @return array
     * @throws \Exception
     */
    public function getLanguagesRef()
    {
	$languages_ref = [];

	$file_temp = fopen('/var/www/diagnostic/language/languages.txt', 'r');
	while (!feof($file_temp)) {
	    $temp_lang = fgets($file_temp, 4096);
	    array_push($languages_ref, substr($temp_lang, 0, -1));
	}
	fclose($file_temp);

	array_pop($languages_ref);

        return $languages_ref;
    }
}
