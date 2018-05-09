<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Diagnostic\Controller;

use Diagnostic\InputFilter\AddQuestionFormFilter;
use Diagnostic\InputFilter\DownloadFormFilter;
use Diagnostic\InputFilter\LoginFormFilter;
use Diagnostic\InputFilter\NewPasswordFormFilter;
use Diagnostic\InputFilter\PasswordForgottenFormFilter;
use Diagnostic\Service\CalculService;
use Diagnostic\Service\MailService;
use Diagnostic\Service\QuestionService;
use Diagnostic\Service\TemplateProcessorService;
use Diagnostic\Service\UserService;
use Diagnostic\Service\UserTokenService;
use Zend\Crypt\BlockCipher;
use Zend\Crypt\Password\Bcrypt;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractController
{
    protected $translator;
    protected $config;
    protected $uploadForm;
    protected $loginForm;
    protected $questionForm;
    protected $informationForm;
    protected $addQuestionForm;
    protected $passwordForgottenForm;
    protected $newPasswordForm;
    protected $linkDownloadForm;
    protected $downloadForm;
    protected $questionService;
    protected $userService;
    protected $userTokenService;
    protected $mailService;
    protected $calculService;
    protected $diagnosticEntity;
    protected $informationEntity;
    protected $questionEntity;

    /**
     * Index
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function indexAction()
    {
        //form
        $formUpload = $this->get('uploadForm');
        $formLogin = $this->get('loginForm');

        //input filter
        $loginFormFilter = new LoginFormFilter($this->get('dbAdapter'));
        $formLogin->setInputFilter($loginFormFilter);

        $message = '';

        //form is post and valid
        $errorMessage = '';
        $request = $this->getRequest();
        if ($request->isPost()) {

            if (count($request->getFiles())) {
                $formUpload->setData(array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                ));

                if ($formUpload->isValid()) {

                    $data = $formUpload->getData();

                    //load json
                    if ($data["file"]["tmp_name"]) {

                        /** @var QuestionService $questionService */
                        $questionService = $this->get('questionService');
                        $successUpload = $questionService->loadJson(file_get_contents($data["file"]["tmp_name"], true));

                        if ($successUpload) {
                            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'rapport']);
                        } else {
                            $errorMessage = '__error_file';
                        }

                    } else {
                        $errorMessage = '__no_file';
                    }
                }

            } else {
                $formLogin->setData($request->getPost());
                if ($formLogin->isValid()) {

                    $formData = $formLogin->getData();

                    /** @var UserService $userService */
                    $userService = $this->get('userService');
                    $user = $userService->getUserByEmail($formData['email']);

                    if (count($user)) {
                        $bcrypt = new Bcrypt();
                        if ($bcrypt->verify($formData['password'], $user->current()->password)) {

                            $container = new Container('user');
                            $container->email = $user->current()->email;
                            $container->admin = $user->current()->admin;

                            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'diagnostic']);
                        } else {
                            $message = '__login_error';
                        }
                    } else {
                        $message = '__login_error';
                    }
                }
            }
        } else {
            $container = new Container('diagnostic');
            $language = $container->language;

            //clear session
            $container = new Container('user');
            if ((!$container->offsetExists('email')) || (is_null($container->email))) {
                $container->getManager()->getStorage()->clear();
            }

            $container = new Container('diagnostic');
            $container->language = $language;
        }

        //send to view
        return new ViewModel([
            'formUpload' => $formUpload,
            'formLogin' => $formLogin,
            'message' => $message,
            'errorMessage' => $errorMessage,
        ]);
    }

    /**
     * Logout
     *
     * @return \Zend\Http\Response
     */
    public function logoutAction()
    {
        //clear session
        $container = new Container('user');
        $container->getManager()->getStorage()->clear();

        return $this->redirect()->toRoute('home', []);
    }

    /**
     * Password forgotten
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function passwordForgottenAction()
    {
        //form
        $form = $this->get('passwordForgottenForm');

        //input filter
        $emailFilter = new PasswordForgottenFormFilter($this->get('dbAdapter'));
        $form->setInputFilter($emailFilter);

        $view = new ViewModel([
            'form' => $form,
        ]);

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();

                //retrieve user
                /** @var UserService $userService */
                $userService = $this->get('userService');
                $user = $userService->getUserByEmail($formData['email']);

                if (count($user)) {

                    /** @var UserTokenService $userTokenService */
                    $userTokenService = $this->get('userTokenService');
                    $userTokenEntity = $userTokenService->saveEntity($formData['email']);

                    $token = $userTokenEntity->getToken();

                    //translator
                    $translator = $this->get('translator');

                    // Determine HTTP/HTTPS proto, and HTTP_HOST
                    if (isset($_SERVER['X_FORWARDED_PROTO'])) {
                        $proto = strtolower($_SERVER['X_FORWARDED_PROTO']);
                    } else if (isset($_SERVER['X_URL_SCHEME'])) {
                        $proto = strtolower($_SERVER['X_URL_SCHEME']);
                    } else if (isset($_SERVER['X_FORWARDED_SSL'])) {
                        $proto = (strtolower($_SERVER['X_FORWARDED_SSL']) == 'on') ? 'https' : 'http';
                    } else if (isset($_SERVER['FRONT_END_HTTPS'])) { // Microsoft variant
                        $proto = (strtolower($_SERVER['FRONT_END_HTTPS']) == 'on') ? 'https' : 'http';
                    } else if (isset($_SERVER['HTTPS'])) {
                        $proto = 'https';
                    } else {
                        $proto = 'http';
                    }

                    if (isset($_SERVER['X_FORWARDED_HOST'])) {
                        $host = $_SERVER['X_FORWARDED_HOST'];
                    } else {
                        $host = $_SERVER['HTTP_HOST'];
                    }

                    $link = $proto . '://' . $host . '/diagnostic/new-password?token=' . htmlentities($token);

                    $content = '
                        <style>
                        .btn {display: inline-block;padding: 6px 12px;margin-bottom: 0;font-size: 14px;font-weight: normal;line-height: 1.42857143;text-align: center;white-space: nowrap;vertical-align: middle;cursor: pointer;background-image: none;border: 1px solid transparent;border-radius: 4px;}
                        .btn:focus,
                        .btn:active:focus,
                        .btn.active:focus,
                        .btn.focus,
                        .btn:active.focus,
                        .btn.active.focus {outline: thin dotted;outline: 5px auto -webkit-focus-ring-color;outline-offset: -2px;}
                        .btn:hover,
                        .btn:focus,
                        .btn.focus {color: #333;text-decoration: none;}
                        .btn:active,
                        .btn.active {background-image: none;outline: 0;-webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);}
                        .btn.disabled,
                        .btn[disabled],
                        fieldset[disabled] .btn {cursor: not-allowed;filter: alpha(opacity=65);-webkit-box-shadow: none;box-shadow: none;opacity: .65;}
                        a.btn.disabled,
                        fieldset[disabled] a.btn {pointer-events: none;}
                        .btn-primary {color: #fff;background-color: #337ab7;border-color: #2e6da4;}
                        .btn-primary:focus,
                        .btn-primary.focus {color: #fff;background-color: #286090;border-color: #122b40;}
                        .btn-primary:hover {color: #fff;background-color: #286090;border-color: #204d74;}
                        .btn-primary:active,
                        .btn-primary.active,
                        .open > .dropdown-toggle.btn-primary {color: #fff;background-color: #286090;border-color: #204d74;}
                        .btn-primary:active:hover,
                        .btn-primary.active:hover,
                        .open > .dropdown-toggle.btn-primary:hover,
                        .btn-primary:active:focus,
                        .btn-primary.active:focus,
                        .open > .dropdown-toggle.btn-primary:focus,
                        .btn-primary:active.focus,
                        .btn-primary.active.focus,
                        .open > .dropdown-toggle.btn-primary.focus {color: #fff;background-color: #204d74;border-color: #122b40;}
                        .btn-primary:active,
                        .btn-primary.active,
                        .open > .dropdown-toggle.btn-primary {background-image: none;}
                        .btn-primary.disabled:hover,
                        .btn-primary[disabled]:hover,
                        fieldset[disabled] .btn-primary:hover,
                        .btn-primary.disabled:focus,
                        .btn-primary[disabled]:focus,
                        fieldset[disabled] .btn-primary:focus,
                        .btn-primary.disabled.focus,
                        .btn-primary[disabled].focus,
                        fieldset[disabled] .btn-primary.focus {background-color: #337ab7;border-color: #2e6da4;}
                        .btn-primary .badge {color: #337ab7;background-color: #fff;}
                        </style>
                        <p>' . $translator->translate('__mail_password_forgotten_content1') . '</p>
                        <p>' . $translator->translate('__mail_password_forgotten_content2') . '</p>
                        <br>
                        <div style="width: 500px; text-align: center">
                            <a href="' . $link . '" class="btn btn-primary" style="text-decoration: none;"><strong>' . $translator->translate('__mail_password_forgotten_link') . '</strong></a>
                        </div>
                        <br>
                        <p>' . $translator->translate('__mail_password_forgotten_content3') . '</p>
                        <p><strong>Cases</strong></p>';

                    //send mail
                    /** @var MailService $mailService */
                    $mailService = $this->get('mailService');
                    $mailService->send($formData['email'], $translator->translate('__mail_password_forgotten_subject'), $content);

                    //redirect
                    return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'index']);

                } else {
                    return $this->redirect()->toRoute('home', []);
                }
            }
        }

        //send to view
        return $view;
    }

    /**
     * New Password
     *
     * @return ViewModel
     */
    public function newPasswordAction()
    {
        //retrieve token
        $token = $this->getRequest()->getQuery('token');

        /** @var UserTokenService $userTokenService */
        $userTokenService = $this->get('userTokenService');
        $userTokenEntity = $userTokenService->getByToken($token);

        $validToken = false;
        foreach ($userTokenEntity as $userToken) {
            if (time() <= $userToken->getLimitTimestamp()) {
                $validToken = true;
            }
        }

        if ($validToken) {

            //form
            $form = $this->get('newPasswordForm');

            //input filter
            $newPasswordFormFilter = new NewPasswordFormFilter($this->get('dbAdapter'));
            $form->setInputFilter($newPasswordFormFilter);

            //form is post and valid
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {

                    $formData = $form->getData();

                    //change password
                    /** @var UserService $userService */
                    $userService = $this->get('userService');
                    $userService->updatePassword($userToken->getUserEmail(), $formData['password']);

                    //delete token
                    $userTokenService->delete($token);

                    //redirect
                    return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'index']);

                }
            }

            //send to view
            return new ViewModel([
                'form' => $form,
                'token' => $token,
            ]);
        } else {
            //redirect
            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'index']);
        }
    }

    /**
     * Delete user
     *
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function diagnosticAction()
    {
        $container = new Container('user');
        if ((!$container->offsetExists('email')) || (is_null($container->email))) {
            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'index']);
        }

        if (!$this->getEvent()->getRouteMatch()->getParam('id')) {
            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'information', 'id' => 1]);
        }

        $id = ($this->getEvent()->getRouteMatch()->getParam('id'));

        //save last question
        $container = new Container('navigation');
        $container->lastQuestion = $id;

        //retrieve questions
        /** @var QuestionService $questionService */
        $questionService = $this->get('questionService');
        $questions = $questionService->getQuestions();

        if (!array_key_exists($id, $questions)) {
            throw new \Exception('Question not exist');
        }

        //retrieve categories
        $categories = [];
        foreach ($questions as $question) {
            $categories[$question->getCategoryId()] = $question->getCategoryTranslationKey();
        }

        //retrieve current question
        $nextQuestion = false;
        foreach ($questions as $question) {
            $nextQuestion = next($questions);
            if ($question->getId() == $id) {
                break;

            }
        }

        //next id
        $nextId = ($nextQuestion) ? $nextQuestion->getId() : $id;

        //retrieve result
        $container = new Container('diagnostic');
        $result = ($container->offsetExists('result')) ? $container->result : [];
        $information = ($container->offsetExists('information')) ? $container->information : ['organization' => '', 'synthesis' => ''];
        //form
        $form = $this->get('questionForm');
        $formUpload = $this->get('uploadForm');

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {

                //format result
                $formData = $form->getData();
                unset($formData['csrf']);
                unset($formData['submit']);

                //security
                foreach (array_keys($formData) as $key) {
                    $formData[$key] = htmlspecialchars($formData[$key]);
                }

                //record result
                $result[$id] = $formData;
                $container->result = $result;

                //redirect
                return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'diagnostic', 'id' => $nextId]);
            }
        }

        //populate
        $diagnosticEntity = $this->get('diagnosticEntity');
        $binding = (array_key_exists($id, $result)) ? $result[$id] : ['maturity' => 3, 'maturityTarget' => 3, 'gravity' => 2];
        $diagnosticEntity->exchangeArray($binding);
        $form->bind($diagnosticEntity);

        //send to view
        return new ViewModel([
            'questions' => $questions,
            'categories' => $categories,
            'result' => $result,
            'information' => $information,
            'form' => $form,
            'formUpload' => $formUpload,
            'id' => $id,
        ]);
    }

    /**
     * Information
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function informationAction()
    {
        $container = new Container('user');
        if ((!$container->offsetExists('email')) || (is_null($container->email))) {
            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'index']);
        }

        //retrieve questions
        /** @var QuestionService $questionService */
        $questionService = $this->get('questionService');
        $questions = $questionService->getQuestions();

        //retrieve categories
        $categories = [];
        foreach ($questions as $question) {
            $categories[$question->getCategoryId()] = $question->getCategoryTranslationKey();
        }

        //retrieve result
        $container = new Container('diagnostic');
        $result = ($container->offsetExists('result')) ? $container->result : [];
        $information = ($container->offsetExists('information')) ? $container->information : ['organization' => '', 'synthesis' => ''];

	//form
        $form = $this->get('informationForm');
        $formUpload = $this->get('uploadForm');

        $type = $this->getEvent()->getRouteMatch()->getParam('id');
        $informationKey = ($type == 2) ? 'synthesis' : 'organization';

        //form is post and valid
        $errorMessage = '';
        $request = $this->getRequest();

	if ($request->isPost()) {

            if (count($request->getFiles())) {
                $formUpload->setData(array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                ));

                if ($formUpload->isValid()) {

                    $data = $formUpload->getData();

                    //load json
                    if ($data["file"]["tmp_name"]) {

                        /** @var QuestionService $questionService */
                        $questionService = $this->get('questionService');
                        $successUpload = $questionService->loadJson(file_get_contents($data["file"]["tmp_name"], true));

                        if ($successUpload) {
                            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'information', 'id' => 1]);
                        } else {
                            $errorMessage = '__error_file';
                        }

                    } else {
                        $errorMessage = '__no_file';
                    }
                }

            } else {
                $form->setData($request->getPost());
                if ($form->isValid()) {

                    //format result
                    $formData = $form->getData();
                    unset($formData['csrf']);
                    unset($formData['submit']);

                    //security
                    foreach (array_keys($formData) as $key) {
                        $formData[$key] = htmlspecialchars($formData[$key]);
                    }

                    //record information
                    $information[$informationKey] = $formData['information'];
                    $container->information = $information;


                    //retrieve first question
                    $firstId = false;
                    foreach ($questions as $key => $question) {
                        $firstId = $key;
                        break;
                    }
                    //redirect
                    if ($type == 1) {
                        return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'diagnostic', 'id' => $firstId]);
                    } else {
                        return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'information', 'id' => $type]);
                    }
                }
            }
        }

        //populate
        $informationEntity = $this->get('informationEntity');
        $binding = (array_key_exists($informationKey, $information)) ? ['information' => $information[$informationKey]] : [];
        $informationEntity->exchangeArray($binding);
        $form->bind($informationEntity);

        //send to view
        return new ViewModel([
            'questions' => $questions,
            'categories' => $categories,
            'result' => $result,
            'information' => $information,
            'form' => $form,
            'type' => $type,
            'formUpload' => $formUpload,
            'errorMessage' => $errorMessage,
        ]);
    }

    /**
     * Add a question
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function addQuestionAction()
    {
        $id = $this->getRequest()->getQuery()->get('id');
        $categoryId = $this->getRequest()->getQuery()->get('categoryId');

        if (!$categoryId) {
            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'diagnostic']);
        }

        //form
        $form = $this->get('addQuestionForm');

        //input filter
        $addQuestionFormFilter = new AddQuestionFormFilter($this->get('dbAdapter'));
        $form->setInputFilter($addQuestionFormFilter);

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {

                $formData = $form->getData();

                //retrieve questions
                /** @var QuestionService $questionService */
                $questionService = $this->get('questionService');
                $questions = $questionService->getQuestions();
                $lastId = 0;
                foreach ($questions as $question) {
                    if ($question->getId() > $lastId) {
                        $lastId = $question->getId();
                    }
                }

                //retrieve categories
                $categories = [];
                foreach ($questions as $question) {
                    $categories[$question->getCategoryId()] = $question->getCategoryTranslationKey();
                }

                //new question
                $newId = $lastId + 1;
                $questionEntity = $this->get('questionEntity');
                $questionEntity->exchangeArray([
                    'id' => (string)$newId,
                    'category_id' => $categoryId,
                    'category_translation_key' => $categories[$categoryId],
                    'translation_key' => $formData['question'],
                    'translation_key_help' => $formData['help'],
                    'threshold' => $formData['threshold'],
                    'new' => true,
                ]);

                //record question
                $questions[] = $questionEntity;
                $container = new Container('diagnostic');
                $container->questions = $questions;

                //redirect
                return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'diagnostic', 'id' => $newId]);
            }
        }

        //send to view
        return new ViewModel([
            'form' => $form,
            'id' => $id,
            'categoryId' => $categoryId,
        ]);
    }

    /**
     * Delete Question
     *
     * @return \Zend\Http\Response
     */
    public function deleteQuestionAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');

        //no id
        if (!$id) {
            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'information', 'id' => 1]);
        }

        //retrieve questions
        /** @var QuestionService $questionService */
        $questionService = $this->get('questionService');
        $questions = $questionService->getQuestions();

        //retrieve result
        $container = new Container('diagnostic');
        $result = ($container->offsetExists('result')) ? $container->result : [];

        //retrieve current question
        $nextQuestion = false;
        $currentQuestion = false;
        foreach ($questions as $question) {
            $nextQuestion = next($questions);
            if ($question->getId() == $id) {
                $currentQuestion = $question;
                break;

            }
        }

        //no question
        if (!$currentQuestion) {
            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'diagnostic', 'id' => $id]);
        }

        //verify not the last for the category
        $nbQuestionsForTheCurrentCategory = 0;
        foreach ($questions as $question) {
            if ($question->getCategoryId() == $currentQuestion->getCategoryId()) {
                $nbQuestionsForTheCurrentCategory++;
            }
        }
        if ($nbQuestionsForTheCurrentCategory < 2) {
            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'diagnostic', 'id' => $id]);
        }

        //next id
        $nextId = ($nextQuestion) ? $nextQuestion->getId() : $id;

        unset($questions[$id]);
        unset($result[$id]);

        $container->questions = $questions;
        $container->result = $result;

        return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'diagnostic', 'id' => $nextId]);
    }

    /**
     * Export
     *
     * @return ViewModel
     */
    public function exportAction()
    {
        //retrieve result
        $container = new Container('diagnostic');
        $result = ($container->offsetExists('result')) ? $container->result : [];
        $information = ($container->offsetExists('information')) ? $container->information : [];

        //retrieve questions
        /** @var QuestionService $questionService */
        $questionService = $this->get('questionService');
        $questions = $questionService->getQuestions();

        //format result
        $export = [
            'result' => $result,
            'information' => $information,
            'questions' => $questions
        ];
        $export = json_encode($export);

        //encryption key
        $config = $this->get('config');
        $encryptionKey = $config['encryption_key'];

	//encrypt result
        //$blockCipher = BlockCipher::factory('mcrypt', ['algo' => 'aes']);
        //$blockCipher->setKey($encryptionKey);
        //$cryptExport = $blockCipher->encrypt($export);
	$iv = $config['iv_key'];
        $cryptExport = openssl_encrypt($export,'AES-256-CBC', $encryptionKey, OPENSSL_RAW_DATA, $iv);
        //create file
        $filename = 'Diagnostic_' . date('YmdHis') . '.cases';
        !$handle = fopen($filename, 'w');
        fwrite($handle, $cryptExport);
        fclose($handle);

        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Length: " . filesize("$filename") . ";");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/octet-stream; ");
        header("Content-Transfer-Encoding: binary");

        readfile($filename);

        unlink($filename);

        $view = new ViewModel();
        $view->setTerminal(true);

        return $view;
    }

    /**
     * Rapport
     *
     * @return ViewModel
     */
    public function rapportAction()
    {
        //form
        $form = $this->get('linkDownloadForm');

        //retrieve results and questions
        $container = new Container('diagnostic');
        $results = ($container->offsetExists('result')) ? $container->result : [];

        //calcul
        /** @var CalculService $calculService */
        $calculService = $this->get('calculService');
        $calculResults = $calculService->calcul();

        //retrieve questions
        /** @var QuestionService $questionService */
        $questionService = $this->get('questionService');
        $questions = $questionService->getQuestions();

        //retrieve categories
        $categories = [];
        $numberByCategories = [];
        foreach ($questions as $question) {
            $categories[$question->getCategoryId()] = $question->getCategoryTranslationKey();
            if (array_key_exists($question->getCategoryTranslationKey(), $numberByCategories)) {
                $numberByCategories[$question->getCategoryTranslationKey()] = $numberByCategories[$question->getCategoryTranslationKey()] + 1;
            } else {
                $numberByCategories[$question->getCategoryTranslationKey()] = 1;
            }
        }

        //translator
        $translator = $this->get('translator');

        //categories repartition
        $categoriesColor = [
            ['color' => '#F7464A', 'highlight' => '#FF5A5E'],
            ['color' => '#46BFBD', 'highlight' => '#5AD3D1'],
            ['color' => '#FDB45C', 'highlight' => '#FFC870'],
            ['color' => '#1b6d85', 'highlight' => '#2aabd2'],
            ['color' => '#3c763d', 'highlight' => '#4cae4c'],
            ['color' => '#555555', 'highlight' => '#666666'],
            ['color' => '#B266FF', 'highlight' => '#CC99FF'],
            ['color' => '#FF66FF', 'highlight' => '#FF99FF'],
        ];
        $categoriesRepartition = [];
        $i = 0;
        foreach ($numberByCategories as $category => $categoryNumber) {
            $categoriesRepartition[$i]['label'] = $translator->translate($category);
            $categoriesRepartition[$i]['color'] = $categoriesColor[$i]['color'];
            $categoriesRepartition[$i]['highlight'] = $categoriesColor[$i]['highlight'];
            $categoriesRepartition[$i]['value'] = $categoryNumber;
            $i++;
        }
        $categories = array_flip($categories);
        $categoriesTarget = $categories;

        foreach ($categories as $key => $category) {
            $categories[$key] = (array_key_exists($category, $calculResults['totalCategory'])) ? (int)$calculResults['totalCategory'][$category] : 0;
            $categoriesTarget[$key] = (array_key_exists($category, $calculResults['totalCategoryTarget'])) ? (int)$calculResults['totalCategoryTarget'][$category] : 0;
        }

        //send to view
        return new ViewModel([
            'form' => $form,
            'total' => $calculResults['total'],
            'totalTarget' => $calculResults['totalTarget'],
            'totalCategory' => $categories,
            'totalCategoryTarget' => $categoriesTarget,
            'recommandations' => $calculResults['recommandations'],
            'categoriesRepartition' => $categoriesRepartition,
            'download' => (count($results)) ? true : false,
        ]);
    }

    /**
     * Download
     *
     * @return ViewModel
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function downloadAction()
    {
        //form
        $form = $this->get('downloadForm');

        //input filter
        $downloadFilter = new DownloadFormFilter($this->get('dbAdapter'));
        $form->setInputFilter($downloadFilter);

        //retrieve information
        $container = new Container('diagnostic');
        $information = ($container->offsetExists('information')) ? $container->information : [];

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();
            if (array_key_exists('radar', $postData)) {

                $container = new Container('diagnostic');

                $imgs = ['radar', 'pie', 'bar'];

                foreach ($imgs as $img) {
                    $imgbase64 = $postData[$img];
                    $imgbase64 = str_replace('data:image/png;base64,', '', $imgbase64);
                    $name = 'data/img/' . $img . '-' . time() . '.png';
                    $handle = fopen($name, 'wb');
                    fwrite($handle, base64_decode($imgbase64));
                    fclose($handle);
                    $container->$img = $name;
                }

            } else {
                $form->setData($postData);
                if ($form->isValid()) {
                    $data = $form->getData();

                    //format form data
                    unset($data['csrf']);
                    unset($data['submit']);

                    //retrieve questions
                    /** @var QuestionService $questionService */
                    $questionService = $this->get('questionService');
                    $questions = $questionService->getQuestions();

                    $translator = $this->get('translator');

                    /** @var CalculService $calculService */
                    $calculService = $this->get('calculService');
                    $calculResults = $calculService->calcul();

			//generating the deliverable according to the language 
                    $word = new TemplateProcessorService('data/resources/model_'.$translator->getLocale().'.docx');
                    $word->generateWord($data, $questions, $calculResults, $information, $translator);
                }
            }
        }

        //send to view
        return new ViewModel([
            'form' => $form,
        ]);
    }

    /**
     * New diagnostic
     * @return \Zend\Http\Response
     */
    public function newDiagnosticAction()
    {
        $container = new Container('user');
        $email = $container->email;
        $admin = $container->admin;

        $container = new Container('diagnostic');
        $language = $container->language;

        $container->getManager()->getStorage()->clear();

        $container = new Container('user');
        $container->email = $email;
        $container->admin = $admin;

        $container = new Container('diagnostic');
        $container->language = $language;

        //redirection
        return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'diagnostic']);
    }

    /**
     * Language
     */
    public function languageAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');

        $container = new Container('diagnostic');
        if ($id == 1) {
            $container->language = 'fr_FR';
        }
        if ($id == 2) {
            $container->language = 'en_EN';
        }
	if ($id == 3) {
            $container->language = 'de_DE';
        }
        //redirection
        $this->redirect()->toUrl($this->getRequest()->getHeader('Referer')->getUri());
    }
}
