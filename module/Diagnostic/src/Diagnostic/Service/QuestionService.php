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
class QuestionService extends AbstractService
{
    protected $config;

    /**
     * Fetch all with categories
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function fetchAllWithCategories()
    {
        $tableGateway = $this->get('gateway');

        return $tableGateway->fetchAllWithCategories();
    }

    /**
     * Get question
     *
     * @return array
     * @throws \Exception
     */
    public function getQuestions()
    {
        $container = new Container('diagnostic');
        if ($container->offsetExists('questions')) {
            $questions = $container->questions;
        } else {
            $questionsObject = $this->fetchAllWithCategories();

            $questions = [];
            foreach ($questionsObject as $question) {
                $questions[$question->getId()] = $question;
            }

            $container->questions = $questions;
        }

        $tmpArray = [];
        foreach ($questions as $question) {
            $tmpArray[$question->getCategoryId()][$question->getId()] = $question;
        }

        ksort($tmpArray);
        $questions = [];
        foreach ($tmpArray as $array) {
            ksort($array);
            foreach ($array as $value) {
                $questions[$value->getId()] = $value;
            }
        }

        return $questions;
    }

    /**
     * Get Bdd Questions
     *
     * @return array
     * @throws \Exception
     */
    public function getBddQuestions()
    {

        $questionsObject = $this->fetchAllWithCategories();

        $questions = [];
        foreach ($questionsObject as $question) {
            $questions[$question->getId()] = $question;
        }

        return $questions;
    }

    /**
     * Load Json
     *
     * @param $json
     * @return bool
     */
    public function loadJson($json)
    {
        //encryption key
        $config = $this->get('config');
        $encryptionKey = $config['encryption_key'];

        //encrypt result
        $blockCipher = BlockCipher::factory('mcrypt', ['algo' => 'aes']);
        $blockCipher->setKey($encryptionKey);
        $json = $blockCipher->decrypt($json);

        $data = (array)json_decode($json);

        //result
        $result = [];
        if (array_key_exists('result', $data)) {
            foreach ($data['result'] as $key => $value) {
                $result[$key] = (array)$value;
            }
        }

        //information
        $information = [];
        if (array_key_exists('information', $data)) {
            $information = (array)$data['information'];
        }

        //questions
        $questions = [];
		$questionEntity = $this->get('entity');
        if (array_key_exists('questions', $data)) {
            foreach ($data['questions'] as $key => $value) {
                $questions[$key] = new $questionEntity;
				$questions[$key]->exchangeArray($value);
            }
        }

        if (count($questions)) {
            $container = new Container('diagnostic');
            $container->result = $result;
            $container->information = $information;
            $container->questions = $questions;

            return true;
        } else {
            return false;
        }
    }

    /**
     * Create
     *
     * @param $data
     */
    public function create($data)
    {
        $questionGateway = $this->get('gateway');
        $questionGateway->insert($data);
    }

    /**
     * Reset Cache
     */
    public function resetCache()
    {
        $container = new Container('diagnostic');
        $container->offsetUnset('questions');
    }

    /**
     * Get question by id
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function getQuestionById($id)
    {
        $tableGateway = $this->get('gateway');

        return $tableGateway->getQuestionById($id);
    }

    /**
     * Update
     *
     * @param $id
     * @param $data
     */
    public function update($id, $data)
    {
        unset($data['id']);
        $questionGateway = $this->get('gateway');
        $questionGateway->update($id, $data);
    }

    /**
     * Delete
     *
     * @param $id
     */
    public function delete($id)
    {
        $questionGateway = $this->get('gateway');
        $questionGateway->delete($id);
    }
}
