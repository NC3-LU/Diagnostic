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
use Diagnostic\Controller\AbstractController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractController
{
    protected $userService;
    protected $userTokenService;
    protected $questionService;
    protected $userForm;
    protected $adminQuestionForm;

    /**
     * Index
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function usersAction()
    {
        $id = $this->getEvent()->getRouteMatch()->getParam('id');
        //retrieve users
        $userService = $this->get('userService');
        $users = $userService->getUsers();

        //retrieve current user
        $container = new Container('user');
        $currentEmail = $container->email;

        $arrayView = [
            'users' => $users,
            'id' => $id,
        ];

        if (!is_null($id)) {
            $form = $this->get('userForm');

            $userFormFilter = new UserFormFilter($this->get('dbAdapter'));
            $form->setInputFilter($userFormFilter);

            $arrayView['form'] = $form;

            $currentUser = $userService->getUserById($id);

            $userToModify = null;
            foreach ($currentUser as $user) {
                $userToModify = $user;
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

                if ($request->getPost('email') != $userToModify->email) {
                    $emailNotExistFilter = new EmailNotExistFilter($this->get('dbAdapter'));
                    $form->setInputFilter($emailNotExistFilter);
                }

                $form->setData($request->getPost());

                $emailUserToModify = $userToModify->email;
                if ($form->isValid()) {
                    $formData = $form->getData();
                    if (is_null($formData->admin)) {
                        unset($formData->admin);
                    }
                    if ($request->getPost('email') != $emailUserToModify) {
                        $userTokenService = $this->get('userTokenService');
                        $userTokenService->deleteByEmail($emailUserToModify);
                    }
                    $userService->update($id, (array)$formData);

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
    public function addUserAction()
    {
        $form = $this->get('userForm');

        $emailNotExistFilter = new EmailNotExistFilter($this->get('dbAdapter'));
        $form->setInputFilter($emailNotExistFilter);

        $form->get('submit')->setValue('__add');

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();

                $userService = $this->get('userService');
                $userService->create((array)$formData);

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
    public function questionsAction()
    {
        //retrieve questions
        $questionService = $this->get('questionService');
        $questions = $questionService->getBddQuestions();

        //send to view
        return new ViewModel([
            'questions' => $questions
        ]);
    }

     /**
     * Categories
     *
     * @return ViewModel
     */
    /*public function categoriesAction()
    {
        //retrieve questions
        $questionService = $this->get('questionService');
        $questions = $questionService->getBddQuestions();

        //send to view
        return new ViewModel([
            'questions' => $questions
        ]);
    }*/

    /**
     * Add question
     *
     * @return ViewModel
     */
    public function addQuestionAction()
    {
	// Session value to know if the translation key already exist
        $_SESSION['erreur_exist'] = 0;

        $form = $this->get('adminQuestionForm');

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
  	    // Determine if the translation key already exist
            $cmd="grep -c -w $_POST[translation_key] /var/www/diagnostic/language/fr_FR.po";
            if(exec($cmd) != 0){ $_SESSION['erreur_exist'] = 1;}

            if ($form->isValid() && $_SESSION['erreur_exist'] == 0) {
                $formData = $form->getData();
                $questionService = $this->get('questionService');
                $questionService->create((array)$formData);
                $questionService->resetCache();

		// Open the french file and write the new question with its help
		$file = fopen('/var/www/diagnostic/language/fr_FR.po', 'a+');
		fputs($file, PHP_EOL);
		fputs($file,  'msgid ' . '"' . $_POST[translation_key] . '"');
		fputs($file, PHP_EOL);
		fputs($file,  'msgstr' . ' ' . '"' . $_POST[translation_fr] . '"');
		fputs($file, PHP_EOL);
		fputs($file, PHP_EOL);
		fputs($file,  'msgid' . ' ' . '"' . $_POST[translation_key] . help . '"');
		fputs($file, PHP_EOL);
		if($_POST['help_fr'] == ""){
		    fputs($file,  'msgstr' . ' ' . '"' . ' ' . '"');
		}
		else{
		    fputs($file,  'msgstr' . ' ' . '"' . $_POST[help_fr] . '"');
		}
		fputs($file, PHP_EOL);
		fclose($file);
		// compile from po to mo
		shell_exec('msgfmt /var/www/diagnostic/language/fr_FR.po -o /var/www/diagnostic/language/fr_FR.mo');

		// Open the english file and write the new question with its help
		$file = fopen('/var/www/diagnostic/language/en_EN.po', 'a+');
		fputs($file, PHP_EOL);
		fputs($file,  'msgid' . ' ' . '"' . $_POST[translation_key] . '"');
		fputs($file, PHP_EOL);
		fputs($file,  'msgstr' . ' ' . '"' . $_POST[translation_en] . '"');
		fputs($file, PHP_EOL);
		fputs($file, PHP_EOL);
		fputs($file,  'msgid' . ' ' . '"' . $_POST[translation_key] . help . '"');
		fputs($file, PHP_EOL);
		if($_POST['help_en'] == ""){
		    fputs($file,  'msgstr' . ' ' . '"' . ' ' . '"');
		}
		else{
		    fputs($file,  'msgstr' . ' ' . '"' . $_POST[help_en] . '"');
		}
		fputs($file, PHP_EOL);
		fclose($file);
		shell_exec('msgfmt /var/www/diagnostic/language/en_EN.po -o /var/www/diagnostic/language/en_EN.mo');

		// Open the german file and write the new question with its help
		$file = fopen('/var/www/diagnostic/language/de_DE.po', 'a+');
		fputs($file, PHP_EOL);
		fputs($file,  'msgid' . ' ' . '"' . $_POST[translation_key] . '"');
		fputs($file, PHP_EOL);
		fputs($file,  'msgstr' . ' ' . '"' . $_POST[translation_de] . '"');
		fputs($file, PHP_EOL);
		fputs($file, PHP_EOL);
		fputs($file,  'msgid' . ' ' . '"' . $_POST[translation_key] . help . '"');
		fputs($file, PHP_EOL);
		if($_POST['help_de'] == ""){
		    fputs($file,  'msgstr' . ' ' . '"' . ' ' . '"');
		}
		else{
		    fputs($file,  'msgstr' . ' ' . '"' . $_POST[help_de] . '"');
		}
		fputs($file, PHP_EOL);
		fclose($file);
		shell_exec('msgfmt /var/www/diagnostic/language/de_DE.po -o /var/www/diagnostic/language/de_DE.mo');

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
     * Add category
     *
     * @return ViewModel
     */
    /*public function addCategoryAction()
    {
	// Session value to know if the translation key already exist
	$_SESSION['erreur_exist'] = 0;

        $form = $this->get('adminQuestionForm');

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

	    // Test if the translation key exist in the file. If it doesn't exist, exec($cmd) != 0
	    $cmd="grep -c -w $_POST[category_id] /var/www/diagnostic/language/fr_FR.po";
	    if(exec($cmd) != 0){ $_SESSION['erreur_exist'] = 1;}
            if ($form->isValid() && $_SESSION['erreur_exist'] == 0) {
                $formData = $form->getData();
                $questionService = $this->get('questionService');
                $questionService->create((array)$formData);
                $questionService->resetCache();

		// Open the french file and write the new question with its help
		$file = fopen('/var/www/diagnostic/language/fr_FR.po', 'a+');
		fputs($file, PHP_EOL);
		fputs($file,  'msgid' . ' ' . '"' . $_POST[category_id] . '"');
		fputs($file, PHP_EOL);
		fputs($file,  'msgstr' . ' ' . '"' . $_POST[translation_fr] . '"');
		fputs($file, PHP_EOL);
		fclose($file);
		// compile from po to mo
		shell_exec('msgfmt /var/www/diagnostic/language/fr_FR.po -o /var/www/diagnostic/language/fr_FR.mo');

		// Open the english file and write the new question with its help
		$file = fopen('/var/www/diagnostic/language/en_EN.po', 'a+');
		fputs($file, PHP_EOL);
		fputs($file,  'msgid' . ' ' . '"' . $_POST[category_id] . '"');
		fputs($file, PHP_EOL);
		fputs($file,  'msgstr' . ' ' . '"' . $_POST[translation_en] . '"');
		fputs($file, PHP_EOL);
		fclose($file);
		shell_exec('msgfmt /var/www/diagnostic/language/en_EN.po -o /var/www/diagnostic/language/en_EN.mo');

		// Open the german file and write the new question with its help
		$file = fopen('/var/www/diagnostic/language/de_DE.po', 'a+');
		fputs($file, PHP_EOL);
		fputs($file,  'msgid' . ' ' . '"' . $_POST[category_id] . '"');
		fputs($file, PHP_EOL);
		fputs($file,  'msgstr' . ' ' . '"' . $_POST[translation_de] . '"');
		fputs($file, PHP_EOL);
		fclose($file);
		shell_exec('msgfmt /var/www/diagnostic/language/de_DE.po -o /var/www/diagnostic/language/de_DE.mo');

		//redirect
                return $this->redirect()->toRoute('admin', ['controller' => 'index', 'action' => 'questions']);
            }
        }

        //send to view
        return new ViewModel([
            'form' => $form
        ]);
    }*/

    /**
     * Modify Question
     *
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function modifyQuestionAction()
    {
	// Session value to know if the translation key already exist
	$_SESSION['erreur_exist'] = 0;

        $id = $this->getEvent()->getRouteMatch()->getParam('id');

        if (is_null($id)) {
            throw new \Exception('Question not exist');
        }

        $form = $this->get('adminQuestionForm');

        $form->get('submit')->setValue('__modify');

        $questionService = $this->get('questionService');
        $currentQuestion = $questionService->getQuestionById($id);

	foreach ($currentQuestion as $question) {
            if ($question->getId() == $id) {
                $form->bind($question);
		$cat = $question; // $cat equal to the question to modify
            }
        }

	// Display the current value of the translation in the form-text (all languages)
   	$file = fopen("/var/www/diagnostic/language/fr_FR.po", "r");
        while (!feof($file)) { // Read the file
            $temp = fgets($file, 4096); // Variable which contains one by one lines of the file
	    // This condition determines where the translation key is in the file, and put its translation in a session variable
	    if($temp == 'msgid '.'"'.$cat->getTranslationKey().'"'.PHP_EOL){$_SESSION['value_fr'] = fgets($file, 4096);}
        }
        fclose($file);
	$_SESSION['value_fr'] = substr($_SESSION['value_fr'], 8, -2);

   	$file = fopen("/var/www/diagnostic/language/en_EN.po", "r");
        while (!feof($file)) {
            $temp = fgets($file, 4096);
	    if($temp == 'msgid '.'"'.$cat->getTranslationKey().'"'.PHP_EOL){$_SESSION['value_en'] = fgets($file, 4096);}
        }
        fclose($file);
	$_SESSION['value_en'] = substr($_SESSION['value_en'], 8, -2);

   	$file = fopen("/var/www/diagnostic/language/de_DE.po", "r");
        while (!feof($file)) {
            $temp = fgets($file, 4096);
	    if($temp == 'msgid '.'"'.$cat->getTranslationKey().'"'.PHP_EOL){$_SESSION['value_de'] = fgets($file, 4096);}
        }
        fclose($file);
	$_SESSION['value_de'] = substr($_SESSION['value_de'], 8, -2);


   	$file = fopen("/var/www/diagnostic/language/fr_FR.po", "r");
        while (!feof($file)) {
            $temp = fgets($file, 4096);
	    if($temp == 'msgid '.'"'.$cat->getTranslationKey().'help'.'"'.PHP_EOL){$_SESSION['value_fr_help'] = fgets($file, 4096);}
        }
        fclose($file);
	$_SESSION['value_fr_help'] = substr($_SESSION['value_fr_help'], 8, -2);

   	$file = fopen("/var/www/diagnostic/language/en_EN.po", "r");
        while (!feof($file)) {
            $temp = fgets($file, 4096);
	    if($temp == 'msgid '.'"'.$cat->getTranslationKey().'help'.'"'.PHP_EOL){$_SESSION['value_en_help'] = fgets($file, 4096);}
        }
        fclose($file);
	$_SESSION['value_en_help'] = substr($_SESSION['value_en_help'], 8, -2);

   	$file = fopen("/var/www/diagnostic/language/de_DE.po", "r");
        while (!feof($file)) {
            $temp = fgets($file, 4096);
	    if($temp == 'msgid '.'"'.$cat->getTranslationKey().'help'.'"'.PHP_EOL){$_SESSION['value_de_help'] = fgets($file, 4096);}
        }
        fclose($file);
	$_SESSION['value_de_help'] = substr($_SESSION['value_de_help'], 8, -2);


	// Create variables which will determine where to delete previous information in the translation files
	$fileCount = -1; // Variable to determine the position of the current line
	$temp_fr = 0;
   	$file = fopen("/var/www/diagnostic/language/fr_FR.po", "r");
        while (!feof($file)) {
            $temp = fgets($file, 4096);
            $fileCount++;
	    if($temp == 'msgid '.'"'.$cat->getTranslationKey().'"'.PHP_EOL){$temp_fr = $fileCount;}
        }
        fclose($file);

	$fileCount = -1;
	$temp_en = 0;
   	$file = fopen("/var/www/diagnostic/language/en_EN.po", "r");
        while (!feof($file)) {
            $temp = fgets($file, 4096);
            $fileCount++;
	    if($temp == 'msgid '.'"'.$cat->getTranslationKey().'"'.PHP_EOL){$temp_en = $fileCount;}
        }
        fclose($file);

	$fileCount = -1;
	$temp_de = 0;
   	$file = fopen("/var/www/diagnostic/language/de_DE.po", "r");
        while (!feof($file)) {
            $temp = fgets($file, 4096);
            $fileCount++;
	    if($temp == 'msgid '.'"'.$cat->getTranslationKey().'"'.PHP_EOL){$temp_de = $fileCount;}
        }
        fclose($file);


        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
	    // Determine if the translation key already exist
	    $cmd="grep -c -w $_POST[translation_key] /var/www/diagnostic/language/fr_FR.po";
            if(exec($cmd) != 0){ $_SESSION['erreur_exist'] = 1;}
	    // If the translation key is the same than the current one, there is no error. Happens when you only want to change translations
	    if($_POST['translation_key'] == $cat->getTranslationKey()){$_SESSION['erreur_exist'] = 0;}
            if ($form->isValid() && $_SESSION['erreur_exist'] == 0) {
                $formData = $form->getData();

                $questionService->update($id, (array)$formData);

                $questionService->resetCache();

		// Open the translation files and delete previous questions in order to add them with changes.
		$file = fopen("/var/www/diagnostic/language/fr_FR.po", "r");
   		$contents = fread($file, filesize("/var/www/diagnostic/language/fr_FR.po"));
		fclose($file);
   		$contents = explode(PHP_EOL, $contents); // PHP_EOL equals to /n in Linux
   		unset($contents[$temp_fr-1]); // Delete the line break
		unset($contents[$temp_fr]); // Delete the translation key
		unset($contents[$temp_fr+1]); // Delete the translation
		unset($contents[$temp_fr+2]); // Delete the line break
		unset($contents[$temp_fr+3]); // Delete the help translation key
		unset($contents[$temp_fr+4]); // Delete the help translation
   		$contents = array_values($contents);
  		$contents = implode(PHP_EOL, $contents);
		$file = fopen("/var/www/diagnostic/language/fr_FR.po", "w");
   		fwrite($file, $contents); // Write the file without the deleted files
		fclose($file);

		// Open the translation files and write the new question with its help
		$file = fopen('/var/www/diagnostic/language/fr_FR.po', 'a+');
		fputs($file, PHP_EOL);
		fputs($file,  'msgid' . ' ' . '"' . $_POST[translation_key] . '"');
		fputs($file, PHP_EOL);
		fputs($file,  'msgstr' . ' ' . '"' . $_POST[translation_fr] . '"');
		fputs($file, PHP_EOL);
		fputs($file, PHP_EOL);
		fputs($file,  'msgid' . ' ' . '"' . $_POST[translation_key] . help . '"');
		fputs($file, PHP_EOL);
		if($_POST['help_fr'] == ""){
		    fputs($file,  'msgstr' . ' ' . '"' . ' ' . '"');
		}
		else{
		    fputs($file,  'msgstr' . ' ' . '"' . $_POST[help_fr] . '"');
		}
		fputs($file, PHP_EOL);
		fclose($file);

		shell_exec('msgfmt /var/www/diagnostic/language/fr_FR.po -o /var/www/diagnostic/language/fr_FR.mo');

		$file = fopen("/var/www/diagnostic/language/en_EN.po", "r");
   		$contents = fread($file, filesize("/var/www/diagnostic/language/en_EN.po"));
		fclose($file);
   		$contents = explode(PHP_EOL, $contents);
   		unset($contents[$temp_en-1]);
		unset($contents[$temp_en]);
		unset($contents[$temp_en+1]);
		unset($contents[$temp_en+2]);
		unset($contents[$temp_en+3]);
		unset($contents[$temp_en+4]);
   		$contents = array_values($contents);
  		$contents = implode(PHP_EOL, $contents);
		$file = fopen("/var/www/diagnostic/language/en_EN.po", "w");
   		fwrite($file, $contents);
		fclose($file);

		$file = fopen('/var/www/diagnostic/language/en_EN.po', 'a+');
		fputs($file, PHP_EOL);
		fputs($file,  'msgid' . ' ' . '"' . $_POST[translation_key] . '"');
		fputs($file, PHP_EOL);
		fputs($file,  'msgstr' . ' ' . '"' . $_POST[translation_en] . '"');
		fputs($file, PHP_EOL);
		fputs($file, PHP_EOL);
		fputs($file,  'msgid' . ' ' . '"' . $_POST[translation_key] . help . '"');
		fputs($file, PHP_EOL);
		if($_POST['help_en'] == ""){
		    fputs($file,  'msgstr' . ' ' . '"' . ' ' . '"');
		}
		else{
		    fputs($file,  'msgstr' . ' ' . '"' . $_POST[help_en] . '"');
		}
		fputs($file, PHP_EOL);
		fclose($file);

		shell_exec('msgfmt /var/www/diagnostic/language/en_EN.po -o /var/www/diagnostic/language/en_EN.mo');

		$file = fopen("/var/www/diagnostic/language/de_DE.po", "r");
   		$contents = fread($file, filesize("/var/www/diagnostic/language/de_DE.po"));
		fclose($file);
   		$contents = explode(PHP_EOL, $contents);
		unset($contents[$temp_de-1]);
		unset($contents[$temp_de]);
		unset($contents[$temp_de+1]);
		unset($contents[$temp_de+2]);
		unset($contents[$temp_de+3]);
		unset($contents[$temp_de+4]);
   		$contents = array_values($contents);
  		$contents = implode(PHP_EOL, $contents);
		$file = fopen("/var/www/diagnostic/language/de_DE.po", "w");
   		fwrite($file, $contents);
		fclose($file);

		$file = fopen('/var/www/diagnostic/language/de_DE.po', 'a+');
		fputs($file, PHP_EOL);
		fputs($file,  'msgid' . ' ' . '"' . $_POST[translation_key] . '"');
		fputs($file, PHP_EOL);
		fputs($file,  'msgstr' . ' ' . '"' . $_POST[translation_de] . '"');
		fputs($file, PHP_EOL);
		fputs($file, PHP_EOL);
		fputs($file,  'msgid' . ' ' . '"' . $_POST[translation_key] . help . '"');
		fputs($file, PHP_EOL);
		if($_POST['help_de'] == ""){
		    fputs($file,  'msgstr' . ' ' . '"' . ' ' . '"');
		}
		else{
		    fputs($file,  'msgstr' . ' ' . '"' . $_POST[help_de] . '"');
		}
		fputs($file, PHP_EOL);
		fclose($file);

		shell_exec('msgfmt /var/www/diagnostic/language/de_DE.po -o /var/www/diagnostic/language/de_DE.mo');

                //redirect
                return $this->redirect()->toRoute('admin', ['controller' => 'index', 'action' => 'questions']);
            }
        }

        //send to view
        return new ViewModel([
            'form' => $form,
            'id' => $id,
        ]);
    }

    /**
     * Delete user
     *
     * @return \Zend\Http\Response
     * @throws \Exception
     */
    public function deleteUserAction()
    {
        //id user
        $id = $this->getEvent()->getRouteMatch()->getParam('id');

        //retrieve users
        $userService = $this->get('userService');
        $users = $userService->getUsers();
        $usersIds = [];
        foreach ($users as $user) {
            $usersIds[] = $user->getId();
        }

        //security
        if (!in_array($id, $usersIds)) {
            throw new \Exception('User not exist');
        }

        $userService = $this->get('userService');
        $userService->delete($id);

        //redirect
        return $this->redirect()->toRoute('admin', ['controller' => 'index', 'action' => 'users']);
    }

    /**
     * Delete question
     *
     * @return \Zend\Http\Response
     * @throws \Exception
     */
    public function deleteQuestionAction()
    {
        //id user
        $id = $this->getEvent()->getRouteMatch()->getParam('id');

        //retrieve bdd questions
        $questionService = $this->get('questionService');
        $questions = $questionService->getBddQuestions();
        $questionsIds = [];
        foreach ($questions as $question) {
            $questionsIds[] = $question->getId();
	    if($question->getId() == $id){$cat = $question;}
        }

        //security
        if (!in_array($id, $questionsIds)) {
            throw new \Exception('Question not exist');
        }

	// See comments in the add function
	$fileCount = -1;
	$temp_fr = 0;
   	$file = fopen("/var/www/diagnostic/language/fr_FR.po", "r");
        while (!feof($file)) {
            $temp = fgets($file, 4096);
            $fileCount++;
	    if($temp == 'msgid '.'"'.$cat->getTranslationKey().'"'.PHP_EOL){$temp_fr = $fileCount;}
        }
        fclose($file);

	$file = fopen("/var/www/diagnostic/language/fr_FR.po", "r");
   	$contents = fread($file, filesize("/var/www/diagnostic/language/fr_FR.po"));
	fclose($file);
   	$contents = explode(PHP_EOL, $contents);
   	unset($contents[$temp_fr-1]);
	unset($contents[$temp_fr]);
	unset($contents[$temp_fr+1]);
	unset($contents[$temp_fr+2]);
	unset($contents[$temp_fr+3]);
	unset($contents[$temp_fr+4]);
   	$contents = array_values($contents);
  	$contents = implode(PHP_EOL, $contents);
	$file = fopen("/var/www/diagnostic/language/fr_FR.po", "w");
   	fwrite($file, $contents);
	fclose($file);
	shell_exec('msgfmt /var/www/diagnostic/language/fr_FR.po -o /var/www/diagnostic/language/fr_FR.mo');

	$fileCount = -1;
	$temp_en = 0;
   	$file = fopen("/var/www/diagnostic/language/en_EN.po", "r");
        while (!feof($file)) {
            $temp = fgets($file, 4096);
            $fileCount++;
	    if($temp == 'msgid '.'"'.$cat->getTranslationKey().'"'.PHP_EOL){$temp_en = $fileCount;}
        }
        fclose($file);

	$file = fopen("/var/www/diagnostic/language/en_EN.po", "r");
   	$contents = fread($file, filesize("/var/www/diagnostic/language/en_EN.po"));
	fclose($file);
   	$contents = explode(PHP_EOL, $contents);
   	unset($contents[$temp_en-1]);
	unset($contents[$temp_en]);
	unset($contents[$temp_en+1]);
	unset($contents[$temp_en+2]);
	unset($contents[$temp_en+3]);
	unset($contents[$temp_en+4]);
   	$contents = array_values($contents);
  	$contents = implode(PHP_EOL, $contents);
	$file = fopen("/var/www/diagnostic/language/en_EN.po", "w");
   	fwrite($file, $contents);
	fclose($file);
	shell_exec('msgfmt /var/www/diagnostic/language/en_EN.po -o /var/www/diagnostic/language/en_EN.mo');

	$fileCount = -1;
	$temp_de = 0;
   	$file = fopen("/var/www/diagnostic/language/de_DE.po", "r");
        while (!feof($file)) {
            $temp = fgets($file, 4096);
            $fileCount++;
	    if($temp == 'msgid '.'"'.$cat->getTranslationKey().'"'.PHP_EOL){$temp_de = $fileCount;}
        }
        fclose($file);

	$file = fopen("/var/www/diagnostic/language/de_DE.po", "r");
   	$contents = fread($file, filesize("/var/www/diagnostic/language/de_DE.po"));
	fclose($file);
   	$contents = explode(PHP_EOL, $contents);
   	unset($contents[$temp_de-1]);
	unset($contents[$temp_de]);
	unset($contents[$temp_de+1]);
	unset($contents[$temp_de+2]);
	unset($contents[$temp_de+3]);
	unset($contents[$temp_de+4]);
   	$contents = array_values($contents);
  	$contents = implode(PHP_EOL, $contents);
	$file = fopen("/var/www/diagnostic/language/de_DE.po", "w");
   	fwrite($file, $contents);
	fclose($file);
	shell_exec('msgfmt /var/www/diagnostic/language/de_DE.po -o /var/www/diagnostic/language/de_DE.mo');

	$questionService->delete($id);

        //redirect
        return $this->redirect()->toRoute('admin', ['controller' => 'index', 'action' => 'questions']);
    }
}
