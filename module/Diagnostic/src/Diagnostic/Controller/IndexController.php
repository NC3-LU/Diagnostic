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

        //form is post and valid
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
                    $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
                    $questionService->loadJson(file_get_contents($data["file"]["tmp_name"], true));

                    //redirect
                    return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'rapport']);
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
                            return $this->redirect()->toRoute('home', []);
                        }
                    } else {
                        return $this->redirect()->toRoute('home', []);
                    }
                }
            }
        }

        //send to view
        return new ViewModel(array(
            'formUpload' => $formUpload,
            'formLogin' => $formLogin,
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

        $id = ($this->getEvent()->getRouteMatch()->getParam('id')) ? $this->getEvent()->getRouteMatch()->getParam('id') : 1;

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

                //record result
                $result[$id] = $formData;
                $container->result = $result;

                //redirect
                return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'diagnostic', 'id' => $nextId]);
            }
        }

        $formUpload->setData(array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        ));

        if ($formUpload->isValid()) {
            $data = $formUpload->getData();

            //load json
            if ($data["file"]["tmp_name"]) {
                $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
                $questionService->loadJson(file_get_contents($data["file"]["tmp_name"], true));
            } else {
                throw new \Exception('No file');
            }

            return $this->redirect()->toRoute('diagnostic', ['controller' => 'index', 'action' => 'diagnostic', 'id' => $id]);
        }

        //populate
        $diagnosticEntity = $this->getServiceLocator()->get('Diagnostic\Model\DiagnosticEntity');
        $binding = (array_key_exists($id, $result)) ? $result[$id] : ['maturity' => 0, 'maturityTarget' => 3, 'gravity' => 2];
        $diagnosticEntity->exchangeArray($binding);
        $form->bind($diagnosticEntity);

        //send to view
        return new ViewModel(array(
            'questions' => $questions,
            'categories' => $categories,
            'result' => $result,
            'form' => $form,
            'formUpload' => $formUpload,
            'id' => $id,
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
                    'ponderation' => $formData['threshold']
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

        //retrieve questions
        $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
        $questions = $questionService->getQuestions();

        //format result
        $export = [
            'result' => $result,
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
            $categoriesRepartition[$i]['value'] = round($categoryNumber / $nbQuestions * 100);
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
            'totalCategory' => $categories,
            'recommandations' => $calculResults['recommandations'],
            'categoriesRepartition' => $categoriesRepartition,
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

                //radar
                $radar_base64 = $postData['radar'];
                $radar_base64 = str_replace('data:image/png;base64,', '', $radar_base64);
                $radarname = 'data/img/radar-' . time() . '.png';
                $handle = fopen($radarname, 'wb');
                fwrite($handle, base64_decode($radar_base64));
                fclose($handle);
                $container->radar = $radarname;

                //pie
                $pie_base64 = $postData['pie'];
                $pie_base64 = str_replace('data:image/png;base64,', '', $pie_base64);
                $piename = 'data/img/pie-' . time() . '.png';
                $handle = fopen($piename, 'wb');
                fwrite($handle, base64_decode($pie_base64));
                fclose($handle);
                $container->pie = $piename;

                //bar
                $bar_base64 = $postData['bar'];
                $bar_base64 = str_replace('data:image/png;base64,', '', $bar_base64);
                $barname = 'data/img/bar-' . time() . '.png';
                $handle = fopen($barname, 'wb');
                fwrite($handle, base64_decode($bar_base64));
                fclose($handle);
                $container->bar = $barname;

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

                    //retrieve result
                    $container = new Container('diagnostic');
                    $results = $container->result;

                    $translator = $this->getServiceLocator()->get('translator');

                    $word = new TemplateProcessorService('data/resources/modele_v0.21.docx');
                    $word->generateWord($data, $questions, $results, $translator);
                }
            }
        }

        //send to view
        return new ViewModel(array(
            'form' => $form,
        ));

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
