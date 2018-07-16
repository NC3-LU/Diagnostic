<?php
namespace Diagnostic\Service;
use Zend\Crypt\BlockCipher;
use Zend\Session\Container;

/**
 * LanguageService
 *
 * @package Diagnostic\Service
 * @author Romain DESJARDINS
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
        $location_country = '/var/www/diagnostic/language/code_country.txt';
        $file_country = fopen($location_country, 'r');
        while (!feof($file_country)) {
            $temp_country = fgets($file_country, 4096);
            array_push($languages, substr($temp_country, 0, -1));
        }
        fclose($file_country);

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
        $location_lang = '/var/www/diagnostic/language/languages.txt';
        $file_lang = fopen($location_lang, 'r');
        while (!feof($file_lang)) {
            $temp_lang = fgets($file_lang, 4096);
            array_push($languages_ref, substr($temp_lang, 0, -1));
        }
        fclose($file_lang);

        array_pop($languages_ref);

        return $languages_ref;
    }
}
