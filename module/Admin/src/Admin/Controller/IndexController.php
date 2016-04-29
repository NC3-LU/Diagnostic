<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;


use Admin\InputFilter\EmailNotExistFilter;
use Admin\InputFilter\UserCreateFormFilter;
use Admin\InputFilter\UserFormFilter;
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
    public function usersAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');

        //retrieve users
        $userService = $this->getServiceLocator()->get('Diagnostic\Service\UserService');
        $users = $userService->getUsers();

        //retrieve current user
        $container = new Container('user');
        $currentEmail = $container->email;

        $arrayView = [
            'users' => $users,
            'id' => $id,
        ];

        if (!is_null($id)) {
            $form = $this->getServiceLocator()->get('formElementManager')->get('UserForm');

            $userFormFilter = new UserFormFilter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $form->setInputFilter($userFormFilter);

            $arrayView['form'] = $form;

            $currentUser = $userService->getUserById($id);

            foreach ($currentUser as $user) {
                if ($user->getId() == $id) {
                    $form->bind($user);
                    if ($user->getEmail() == $currentEmail) {
                        $arrayView['current'] = true;
                    }
                }
            }

            //form is post and valid
            $request = $this->getRequest();
            if ($request->isPost()) {
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $formData = $form->getData();
                    if (is_null($formData->admin)) {
                        unset($formData->admin);
                    }
                    $userService->update($id, (array) $formData);

                    //redirect
                    return $this->redirect()->toRoute('admin', ['controller' => 'index', 'action' => 'users']);
                }
            }

        }

        //send to view
        return new ViewModel($arrayView);
    }

    /**
     * Add a user
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function addUserAction() {
        $form = $this->getServiceLocator()->get('formElementManager')->get('UserForm');

        $userFormFilter = new UserFormFilter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->setInputFilter($userFormFilter);

        $emailNotExistFilter = new EmailNotExistFilter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->setInputFilter($emailNotExistFilter);

        $form->get('submit')->setValue('__add');

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();

                $userService = $this->getServiceLocator()->get('Diagnostic\Service\UserService');
                $userService->create((array) $formData);

                //redirect
                return $this->redirect()->toRoute('admin', ['controller' => 'index', 'action' => 'users']);
            }
        }

        //send to view
        return new ViewModel([
            'form' => $form
        ]);
    }

    /**
     * Questions
     *
     * @return ViewModel
     */
    public function questionsAction() {
        //retrieve questions
        $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
        $questions = $questionService->getQuestions();

        //send to view
        return new ViewModel([
            'questions' => $questions
        ]);
    }

    /**
     * Add question
     *
     * @return ViewModel
     */
    public function addQuestionAction() {
        $form = $this->getServiceLocator()->get('formElementManager')->get('AdminQuestionForm');

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();

                $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
                $questionService->create((array) $formData);

                $questionService->resetCache();

                //redirect
                return $this->redirect()->toRoute('admin', ['controller' => 'index', 'action' => 'questions']);
            }
        }

        //send to view
        return new ViewModel([
            'form' => $form
        ]);
    }

    /**
     * Modify Question
     *
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function modifyQuestionAction() {

        $id = $this->getEvent()->getRouteMatch()->getParam('id');

        if (is_null($id)) {
            throw new \Exception('Question not exist');
        }

        $form = $this->getServiceLocator()->get('formElementManager')->get('AdminQuestionForm');

        $form->get('submit')->setValue('__modify');

        $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
        $currentQuestion = $questionService->getQuestionById($id);

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();

                $questionService->update($id, (array) $formData);

                $questionService->resetCache();

                //redirect
                return $this->redirect()->toRoute('admin', ['controller' => 'index', 'action' => 'questions']);
            }
        }

        foreach ($currentQuestion as $question) {
            if ($question->getId() == $id) {
                $form->bind($question);
            }
        }

        //send to view
        return new ViewModel([
            'form' => $form,
            'id' => $id,
        ]);
    }
}
