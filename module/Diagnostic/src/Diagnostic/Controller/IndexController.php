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
use Diagnostic\Service\TemplateProcessorService;
use Zend\Crypt\BlockCipher;
use Zend\Crypt\Password\Bcrypt;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /**
     * Index
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function indexAction()
    {
        //form
        $formUpload = $this->getServiceLocator()->get('formElementManager')->get('UploadForm');
        $formLogin = $this->getServiceLocator()->get('formElementManager')->get('LoginForm');

        //input filter
        $loginFormFilter = new LoginFormFilter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
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

                        $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
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

                    $userService = $this->getServiceLocator()->get('Diagnostic\Service\UserService');
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
        }

        //send to view
        return new ViewModel(array(
            'formUpload' => $formUpload,
            'formLogin' => $formLogin,
            'message' => $message,
            'errorMessage' => $errorMessage,
        ));
    }

    /**
     * Logout
     *
     * @return \Zend\Http\Response
     */
    public function logoutAction() {
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
    public function passwordForgottenAction() {
        //form
        $form = $this->getServiceLocator()->get('formElementManager')->get('PasswordForgottenForm');

        //input filter
        $emailFilter = new PasswordForgottenFormFilter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
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
                $userService = $this->getServiceLocator()->get('Diagnostic\Service\UserService');
                $user = $userService->getUserByEmail($formData['email']);

                if (count($user)) {

                    $userTokenService = $this->getServiceLocator()->get('Diagnostic\Service\UserTokenService');
                    $userTokenEntity = $userTokenService->saveEntity($formData['email']);

                    $token = $userTokenEntity->getToken();

                    //translator
                    $translator = $this->getServiceLocator()->get('translator');

                    $config = $this->getServiceLocator()->get('Config');

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
                            <a href="' . $config['domain'] . '/diagnostic/new-password?token=' . htmlentities($token) . '" class="btn btn-primary" style="text-decoration: none;"><strong>' . $translator->translate('__mail_password_forgotten_link') . '</strong></a>
                        </div>
                        <br>
                        <p>' . $translator->translate('__mail_password_forgotten_content3') . '</p>
                        <p><strong>Cases</strong></p>';

                    //send mail
                    $mailService = $this->getServiceLocator()->get('Diagnostic\Service\MailService');
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
    public function newPasswordAction() {

        //retrieve token
        $token = $this->getRequest()->getQuery('token');
        $userTokenService = $this->getServiceLocator()->get('Diagnostic\Service\UserTokenService');
        $userTokenEntity = $userTokenService->getByToken($token);

        $validToken = false;
        foreach ($userTokenEntity as $userToken) {
            if (time() <= $userToken->getLimitTimestamp()){
                $validToken = true;
            }
        }

        if ($validToken) {

            //form
            $form = $this->getServiceLocator()->get('formElementManager')->get('NewPasswordForm');

            //input filter
            $newPasswordFormFilter = new NewPasswordFormFilter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $form->setInputFilter($newPasswordFormFilter);

            //form is post and valid
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {

                    $formData = $form->getData();

                    //change password
                    $userService = $this->getServiceLocator()->get('Diagnostic\Service\UserService');
                    $userService->updatePassword($userToken->getUserEmail(), $formData['password']);

                    //delete token
                    $userTokenService->delete($token);

                    //redirect
                    return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'index']);

                }
            }

            //send to view
            return new ViewModel(array(
                'form' => $form,
                'token' => $token,
            ));
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
    public function diagnosticAction() {

        $container = new Container('user');
        if ((! $container->offsetExists('email')) || (is_null($container->email))) {
            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'index']);
        }

        if (! $this->getEvent()->getRouteMatch()->getParam('id')) {
            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'information', 'id' => 1]);
        }

        $id = ($this->getEvent()->getRouteMatch()->getParam('id'));

        //save last question
        $container = new Container('navigation');
        $container->lastQuestion = $id;

        //retrieve questions
        $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
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
        $information = ($container->offsetExists('information')) ? $container->information : [];

        //form
        $form = $this->getServiceLocator()->get('formElementManager')->get('QuestionForm');
        $formUpload = $this->getServiceLocator()->get('formElementManager')->get('UploadForm');

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
        $diagnosticEntity = $this->getServiceLocator()->get('Diagnostic\Model\DiagnosticEntity');
        $binding = (array_key_exists($id, $result)) ? $result[$id] : ['maturity' => 3, 'maturityTarget' => 3, 'gravity' => 2];
        $diagnosticEntity->exchangeArray($binding);
        $form->bind($diagnosticEntity);

        //send to view
        return new ViewModel(array(
            'questions' => $questions,
            'categories' => $categories,
            'result' => $result,
            'information' => $information,
            'form' => $form,
            'formUpload' => $formUpload,
            'id' => $id,
        ));
    }

    public function informationAction() {

        $container = new Container('user');
        if ((! $container->offsetExists('email')) || (is_null($container->email))) {
            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'index']);
        }

        //retrieve questions
        $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
        $questions = $questionService->getQuestions();

        //retrieve categories
        $categories = [];
        foreach ($questions as $question) {
            $categories[$question->getCategoryId()] = $question->getCategoryTranslationKey();
        }

        //retrieve result
        $container = new Container('diagnostic');
        $result = ($container->offsetExists('result')) ? $container->result : [];
        $information = ($container->offsetExists('information')) ? $container->information : [];

        //form
        $form = $this->getServiceLocator()->get('formElementManager')->get('InformationForm');
        $formUpload = $this->getServiceLocator()->get('formElementManager')->get('UploadForm');

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

                        $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
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


                    //redirect
                    if ($type == 1) {
                        return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'diagnostic', 'id' => 1]);
                    } else {
                        return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'information', 'id' => $type]);
                    }
                }
            }
        }

        //populate
        $informationEntity = $this->getServiceLocator()->get('Diagnostic\Model\InformationEntity');
        $binding = (array_key_exists($informationKey, $information)) ? ['information' => $information[$informationKey]] : [];
        $informationEntity->exchangeArray($binding);
        $form->bind($informationEntity);

        //send to view
        return new ViewModel(array(
            'questions' => $questions,
            'categories' => $categories,
            'result' => $result,
            'information' => $information,
            'form' => $form,
            'type' => $type,
            'formUpload' => $formUpload,
            'errorMessage' => $errorMessage,
        ));

    }

    /**
     * Add a question
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function addQuestionAction() {
        $id = $this->getRequest()->getQuery()->get('id');
        $categoryId = $this->getRequest()->getQuery()->get('categoryId');

        if ((!$id) || (!$categoryId)) {
            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'diagnostic']);
        }

        //form
        $form = $this->getServiceLocator()->get('formElementManager')->get('AddQuestionForm');

        //input filter
        $addQuestionFormFilter = new AddQuestionFormFilter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->setInputFilter($addQuestionFormFilter);

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {

                $formData = $form->getData();

                //retrieve questions
                $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
                $questions = $questionService->getQuestions();
                $lastId = 0;
                foreach($questions as $question){
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
                $questionEntity = $this->getServiceLocator()->get('Diagnostic\Model\QuestionEntity');
                $questionEntity->exchangeArray([
                    'id' => (string) $newId,
                    'category_id' => $categoryId,
                    'category_translation_key' => $categories[$categoryId],
                    'translation_key' => $formData['question'],
                    'translation_key_help' => $formData['help'],
                    'threshold' => $formData['threshold']
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
        return new ViewModel(array(
            'form' => $form,
            'id' => $id,
            'categoryId' => $categoryId,
        ));
    }

    /**
     * Export
     *
     * @return ViewModel
     */
    public function exportAction() {

        //retrieve result
        $container = new Container('diagnostic');
        $result = ($container->offsetExists('result')) ? $container->result : [];
        $information = ($container->offsetExists('information')) ? $container->information : [];

        //retrieve questions
        $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
        $questions = $questionService->getQuestions();

        //format result
        $export = [
            'result' => $result,
            'information' => $information,
            'questions' => $questions
        ];
        $export = json_encode($export);

        //encryption key
        $config = $this->getServiceLocator()->get('Config');
        $encryptionKey = $config['encryption_key'];


        //encrypt result
        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $blockCipher->setKey($encryptionKey);
        $cryptExport = $blockCipher->encrypt($export);

        //create file
        $filename = 'data/' . date('YmdHis') . '.cases';
        !$handle = fopen($filename, 'w');
        fwrite($handle, $cryptExport);
        fclose($handle);

        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Length: ". filesize("$filename").";");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/octet-stream; ");
        header("Content-Transfer-Encoding: binary");

        readfile($filename);

        $view = new ViewModel();
        $view->setTerminal(true);

        return $view;
    }

    /**
     * Rapport
     *
     * @return ViewModel
     */
    public function rapportAction() {

        //form
        $form = $this->getServiceLocator()->get('formElementManager')->get('LinkDownloadForm');

        //retrieve results and questions
        $container = new Container('diagnostic');
        $results = ($container->offsetExists('result')) ? $container->result : [];

        //calcul
        $calculService = $this->getServiceLocator()->get('Diagnostic\Service\CalculService');
        $calculResults = $calculService->calcul();

        //retrieve questions
        $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
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
        $translator = $this->getServiceLocator()->get('translator');

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
        $nbQuestions = count($questions);
        $categoriesRepartition = [];
        $i = 0;
        foreach($numberByCategories as $category => $categoryNumber) {
            $categoriesRepartition[$i]['label'] = $translator->translate($category);
            $categoriesRepartition[$i]['color'] = $categoriesColor[$i]['color'];
            $categoriesRepartition[$i]['highlight'] = $categoriesColor[$i]['highlight'];
            $categoriesRepartition[$i]['value'] = $categoryNumber;
            $i++;
        }

        $categories = array_flip($categories);

        foreach ($categories as $key => $category) {
            $categories[$key] = (array_key_exists($category, $calculResults['totalCategory'])) ? (int) $calculResults['totalCategory'][$category] : 0;
        }

        //send to view
        return new ViewModel(array(
            'form' => $form,
            'total' => $calculResults['total'],
            'totalTarget' => $calculResults['totalTarget'],
            'totalCategory' => $categories,
            'recommandations' => $calculResults['recommandations'],
            'categoriesRepartition' => $categoriesRepartition,
            'download' => (count($results)) ? true : false,
        ));
    }


    /**
     * Download
     *
     * @return ViewModel
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function downloadAction() {
        //form
        $form = $this->getServiceLocator()->get('formElementManager')->get('DownloadForm');

        //input filter
        $downloadFilter = new DownloadFormFilter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->setInputFilter($downloadFilter);

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();
            if (array_key_exists('radar', $postData)) {

                $container = new Container('diagnostic');

                $imgs = ['radar', 'pie', 'bar'];

                foreach($imgs as $img) {
                    $imgbase64 = $postData[$img];
                    $imgbase64 = str_replace('data:image/png;base64,', '', $imgbase64);
                    $name =  'data/img/' . $img . '-' . time() . '.png';
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
                    $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
                    $questions = $questionService->getQuestions();

                    $translator = $this->getServiceLocator()->get('translator');

                    $calculService = $this->getServiceLocator()->get('Diagnostic\Service\CalculService');
                    $calculResults = $calculService->calcul();

                    $word = new TemplateProcessorService('data/resources/modele_v0.7.docx');
                    $word->generateWord($data, $questions, $calculResults['recommandations'], $translator);
                }
            }
        }

        //send to view
        return new ViewModel(array(
            'form' => $form,
        ));

    }

    /**
     * New diagnostic
     * @return \Zend\Http\Response
     */
    public function newDiagnosticAction() {

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
    public function languageAction() {

        $id = $this->getEvent()->getRouteMatch()->getParam('id');

        $container = new Container('diagnostic');
        if ($id == 1) {
            $container->language = 'fr_FR';
        }
        if ($id == 2) {
            $container->language = 'en_EN';
        }

        //redirection
        $this->redirect()->toUrl($this->getRequest()->getHeader('Referer')->getUri());
    }
}
