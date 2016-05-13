<?php
namespace Diagnostic\Service;

use Zend\Crypt\BlockCipher;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Session\Container;

/**
 * QuestionService
 *
 * @package Diagnostic\Service
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class QuestionService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;


    /**
     * Fetch all with categories
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function fetchAllWithCategories() {

        if ($this->getServiceLocator()->has('Diagnostic\Gateway\QuestionGateway')) {

            $tableGateway = $this->getServiceLocator()->get('Diagnostic\Gateway\QuestionGateway');

            return $tableGateway->fetchAllWithCategories();

        } else {
            throw new \Exception('Question Gateway not found');
        }
    }

    /**
     * Get question
     *
     * @return array
     * @throws \Exception
     */
    public function getQuestions() {
        $container = new Container('diagnostic');
        if ($container->offsetExists('questions')) {
            $questions = $container->questions;
        } else {
            $questionsObject = $this->fetchAllWithCategories();

            $questions = [];
            foreach($questionsObject as $question) {
                $questions[$question->getId()] = $question;
            }

            $container->questions = $questions;
        }

        $tmpArray = [];
        foreach($questions as $question) {
            $tmpArray[$question->getCategoryId()][$question->getId()] = $question;
        }

        ksort($tmpArray);
        $questions = [];
        foreach($tmpArray as $array) {
            ksort($array);
            foreach($array as $value) {
                $questions[$value->getId()] = $value;
            }
        }

        return $questions;
    }

    /**
     * Load Json
     *
     * @param $json
     */
    public function loadJson($json) {
        //encryption key
        $config = $this->getServiceLocator()->get('Config');
        $encryptionKey = $config['encryption_key'];

        //encrypt result
        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $blockCipher->setKey($encryptionKey);
        $json = $blockCipher->decrypt($json);

        $data = (array) json_decode($json);

        //result
        $result = [];
        foreach($data['result'] as $key => $value) {
            $result[$key] = (array) $value;
        }
        $container = new Container('diagnostic');
        $container->result = $result;

        //questions
        $questions = [];
        foreach($data['questions'] as $key => $value) {
            $questionEntity = $this->getServiceLocator()->get('Diagnostic\Model\QuestionEntity');
            $questionEntity->exchangeArray($value);
            $questions[$key] = $questionEntity;
        }
        $container->questions = $questions;
    }

    /**
     * Create
     *
     * @param $data
     */
    public function create($data) {
        $questionGateway = $this->getServiceLocator()->get('Diagnostic\Gateway\QuestionGateway');
        $questionGateway->insert($data);
    }

    /**
     * Reset Cache
     */
    public function resetCache() {
        $container = new Container('diagnostic');
        $container->offsetUnset('questions');
    }

    /**
     * Get question by id
     *
     * @return \Zend\Db\ResultSet\ResultSet
     * @throws \Exception
     */
    public function getQuestionById($id) {
        if ($this->getServiceLocator()->has('Diagnostic\Gateway\QuestionGateway')) {

            $tableGateway = $this->getServiceLocator()->get('Diagnostic\Gateway\QuestionGateway');

            return $tableGateway->getQuestionById($id);
        }
    }

    /**
     * Update
     *
     * @param $id
     * @param $data
     */
    public function update($id, $data) {
        unset($data['id']);
        $questionGateway = $this->getServiceLocator()->get('Diagnostic\Gateway\QuestionGateway');
        $questionGateway->update($id, $data);
    }

}