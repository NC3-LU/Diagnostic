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
    protected $categoryService;
    protected $languageService;
    protected $userForm;
    protected $adminQuestionForm;
    protected $adminCategoryForm;
    protected $adminLanguageForm;

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
    public function categoriesAction()
    {
        //retrieve categories
        $categoryService = $this->get('categoryService');
        $categories = $categoryService->getBddCategories();

        //send to view
        return new ViewModel([
            'categories' => $categories
        ]);
    }

    /**
     * Languages
     *
     * @return ViewModel
     */
    public function languagesAction()
    {
	// Variable to display error message when adding or deleting a language
	$lang_exist = 0;
	$lang_exist2 = 0;
	$lang_exist3 = 0;

        //retrieve questions
        $questionService = $this->get('questionService');
        $questions = $questionService->getBddQuestions();

	$form = $this->get('adminLanguageForm');
	$request = $this->getRequest();

	if ($request->isPost()) {
	    $form->setData($request->getPost());


	    // Don't reset the reference language when clicking a button
	    if (isset($_POST['submit_lang_add']) || isset($_POST['submit_lang_del']) || isset($_POST['submit_lang_ref']) || isset($_POST['submit_all']) || isset($_POST['submit_translation_add'])) {
	        $_SESSION['base_lang'] = 1;
	    }

	    // Skip categories and questions
	    $file = fopen('/var/www/diagnostic/language/fr.po', 'r');
	    $nb_translation = 0;
	    $fileCount = 3;
            while (!feof($file)) {
                $temp = fgets($file, 4096);
                if (substr($temp, 7, -2) == '__question' . ($_SESSION['nb_questions']-1) . 'help') {$temp = fgets($file, 4096); $temp = fgets($file, 4096); break;}
            }
            while (!feof($file)) {
                $temp = fgets($file, 4096);
	        if ($fileCount == 3) {$nb_translation++; $fileCount=0;}
	        $fileCount++;
            }
	    fclose($file);

	    // num_line2 is used to change all translations in 1 button
	    $file = fopen('/var/www/diagnostic/language/fr.po', 'r');
	    $num_line2 = -1;
            while (!feof($file)) {
                $temp = fgets($file, 4096);
	 	$num_line2++;
                if (substr($temp, 7, -2) == '__diagnostic') {$num_line2++; break;}
            }
	    fclose($file);

	    // Search the translation key in order to know the translation to change
	    $file_lang = fopen('/var/www/diagnostic/language/languages.txt', 'r');
	    for ($j=0; $j<$_SESSION['nb_lang']; $j++) {
	        $temp_lang = fgets($file_lang, 4096);
	        if ($_SESSION['lang'] == substr($temp_lang, 0, -1)) {

		    // Action to modify one translation
		    for ($i=1; $i<=$nb_translation; $i++){
	    	        if (isset($_POST['mod'.$i])){
			    $_SESSION['base_lang'] = 1;
	            	    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'r');
		    	    $fileCount = -1;
		    	    $num_line = 0;
        	    	    while (!feof($file)) {
                                $temp = fgets($file, 4096);
		                $fileCount++;
	                        if(substr($temp, 7, -2) == $_SESSION['key_' . substr($temp_lang, 0, -1)][$i]){
			    	    $num_line = $fileCount;
		                }
         	            }
		    	    fclose($file);

			    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'r');
			    $contents = fread($file, filesize('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po'));
			    fclose($file);
   			    $contents = explode(PHP_EOL, $contents); // PHP_EOL equals to /n in Linux
			    $contents[$num_line+1] = 'msgstr ' . '"' . $request->getPost('translation'.$i) . '"'; // Change the translation with the new one
 			    $contents = array_values($contents);
 			    $contents = implode(PHP_EOL, $contents);
			    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'w');
   			    fwrite($file, $contents); // Write the file with the new translation
			    fclose($file);

			    shell_exec('msgfmt /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po -o /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.mo');
		        }
		    }

		    // Action to modify all translations
		    $file2 = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'r');
		    $contents2 = fread($file2, filesize('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po'));
	            fclose($file2);
   		    $contents2 = explode(PHP_EOL, $contents2); // PHP_EOL equals to /n in Linux

		    for ($i=1; $i<=$nb_translation; $i++){

			if (isset($_POST['submit_all'])){
			    $contents2[$num_line2] = 'msgstr ' . '"' . $request->getPost('translation'.$i) . '"'; // Change the translation with the new one
			    $num_line2+=3;
			}
		    }

		    $contents2 = array_values($contents2);
 		    $contents2 = implode(PHP_EOL, $contents2);
		    $file2 = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'w');
   	            fwrite($file2, $contents2); // Write the file with the new translation
		    fclose($file2);

		    shell_exec('msgfmt /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po -o /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.mo');
	        }

	        // change reference language thanks to the session value
	        if (isset($_POST['submit_lang_ref'])){
	            if ($request->getPost('language_ref') == $j) {
	                $_SESSION['change_language'] = substr($temp_lang, 0, -1);
	            }
	        }
	    }
	    fclose($file_lang);

	    // Add a language
	    if (isset($_POST['submit_lang_add'])) {
	        $file_lang = fopen('/var/www/diagnostic/language/code_country.txt', 'r');
	        $fileCount = 0;
	        while (!feof($file_lang)) {
	            $temp_lang = fgets($file_lang, 4096);
		    $fileCount++;
	        }
	        fclose($file_lang);

	        $file_lang = fopen('/var/www/diagnostic/language/code_country.txt', 'r');
	        for ($i=0; $i<$fileCount; $i++) {
	            $temp_lang = fgets($file_lang, 4096);
		    if ($request->getPost('add_language') == $i) {
		        $temp = $temp_lang;
		        $file_temp = fopen('/var/www/diagnostic/language/languages.txt', 'a+');
			while (!feof($file_temp)) {
                	    $temp_lang = fgets($file_temp, 4096);
                	    if ($temp_lang == $temp) {$lang_exist=1; break;}
            		}

			if ($lang_exist == 0) {
		            fputs($file_temp, $temp);

			    // Creation .po file
			    $new_file = fopen('/var/www/diagnostic/language/' . substr($temp, 0, -1) . '.po', 'a+');
			    $fr_file = fopen('/var/www/diagnostic/language/fr.po', 'r');
			    while (!feof($fr_file)) {
                	        $fr_temp = fgets($fr_file, 4096);
                	    	if (substr($fr_temp, 7, -2) == '__category1') {break;}
			    	fputs($new_file, $fr_temp);
            		    }
			    $fileCount = 1;
			    while (!feof($fr_file)) {
                	    	if ($fileCount == 1) {fputs($new_file, $fr_temp);}
			    	elseif ($fileCount == 2) {fputs($new_file, 'msgstr ""');}
			    	else {fputs($new_file, PHP_EOL); fputs($new_file, PHP_EOL); $fileCount = 0;}
			    	$fr_temp = fgets($fr_file, 4096);
			    	$fileCount++;
            		    }
			    fputs($new_file, PHP_EOL);
			    fclose($fr_file);
			    fclose($new_file);

			    shell_exec('msgfmt /var/www/diagnostic/language/' . substr($temp, 0, -1) . '.po -o /var/www/diagnostic/language/' . substr($temp, 0, -1) . '.mo');
			}
			fclose($file_temp);
	            }
	        }
		fclose($file_lang);
  	    }

	    // Delete a language
	    if (isset($_POST['submit_lang_del']) && $lang_exist3 == 0) {
		$lang_exist2 = 2;

	        $file_lang = fopen('/var/www/diagnostic/language/code_country.txt', 'r');
	        $fileCount = 0;
	        while (!feof($file_lang)) {
	            $temp_lang = fgets($file_lang, 4096);
		    $fileCount++;
	        }
	        fclose($file_lang);

	        $file_lang = fopen('/var/www/diagnostic/language/code_country.txt', 'r');
	        for ($i=0; $i<$fileCount; $i++) {
	            $temp_lang = fgets($file_lang, 4096);
		    if ($request->getPost('add_language') == $i) {
		        $temp = $temp_lang;
		        $file_temp = fopen('/var/www/diagnostic/language/languages.txt', 'r');
		  	$num_line = -1;
			while (!feof($file_temp)) {
                	    $temp_lang = fgets($file_temp, 4096);
			    $num_line++;
                	    if ($temp_lang == $temp) {$lang_exist2=1; break;}
            		}
			fclose($file_temp);

			// Avoid to delete english and french languages, which are used to create other languages
			if ($temp_lang == 'fr' . PHP_EOL || $temp_lang == 'en' . PHP_EOL) {$lang_exist3=1;}

			if ($lang_exist2 == 1 && $lang_exist3 == 0) {

		            $file_temp = fopen('/var/www/diagnostic/language/languages.txt', 'r');
   		    	    $contents = fread($file_temp, filesize('/var/www/diagnostic/language/languages.txt'));
		    	    fclose($file_temp);
   		    	    $contents = explode(PHP_EOL, $contents); // PHP_EOL equals to /n in Linux
   		    	    unset($contents[$num_line]); // Delete the language
	      	    	    $contents = array_values($contents);
  		    	    $contents = implode(PHP_EOL, $contents);
		    	    $file_temp = fopen('/var/www/diagnostic/language/languages.txt', 'w');
   		    	    fwrite($file_temp, $contents); // Write the file without the deleted files
		    	    fclose($file_temp);

			    unlink('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po');
			    unlink('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.mo');
			}
	            }
	        }
		fclose($file_lang);
	    }
	}

	// english language when refreshing the page
	if ($_SESSION['base_lang'] == 0) {
	    $_SESSION['change_language'] = 'en';
	}

	//send to view
        return new ViewModel([
            'form' => $form,
	    'questions' => $questions,
	    'lang_exist' => $lang_exist,
	    'lang_exist2' => $lang_exist2,
	    'lang_exist3' => $lang_exist3
        ]);
    }

    /**
     * Add question
     *
     * @return ViewModel
     */
    public function addQuestionAction()
    {
	// Session value to know if the translation key already exist
        $_SESSION['erreur_exist'] = 0;

	$tabToGet = ['translation_key', 'category_id', 'threshold', 'csrf', 'submit'];

        $form = $this->get('adminQuestionForm');

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

	    // Determine if the translation key already exist
            $cmd='grep -c -w ' . $request->getPost('translation_key') . ' /var/www/diagnostic/language/en.po';
            if(exec($cmd) != 0){ $_SESSION['erreur_exist'] = 1;}

            if ($form->isValid() && $_SESSION['erreur_exist'] == 0) {
                $formData = [];
		foreach ($tabToGet as $key) {
		    $formData[$key] = $form->getData()[$key];
		}
                $questionService = $this->get('questionService');
                $questionService->create((array)$formData);
                $questionService->resetCache();

		// Add translation to the .po files.
		$file_lang = fopen('/var/www/diagnostic/language/languages.txt', 'r');
		for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
		    $temp_lang = fgets($file_lang, 4096);
		    rename('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', '/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '_temp.po');
		    $file_temp = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '_temp.po', 'r');
		    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'w');
		    while (!feof($file_temp)) {
		        $temp = fgets($file_temp, 4096);
		        fputs($file, $temp);
		    	if (substr($temp, 7, -2) == '__question' . ($_SESSION['nb_questions']-1) . 'help') {
			    $temp = fgets($file_temp, 4096);
			    fputs($file, $temp);
			    fputs($file, PHP_EOL);
			    fputs($file,  'msgid "' . $request->getPost('translation_key') . '"');
			    fputs($file, PHP_EOL);
			    fputs($file,  'msgstr "' . $request->getPost('translation_' . substr($temp_lang, 0, -1)) . '"');
			    fputs($file, PHP_EOL);
			    fputs($file, PHP_EOL);
			    fputs($file,  'msgid "' . $request->getPost('translation_key') . 'help"');
			    fputs($file, PHP_EOL);
			    if($request->getPost('help_' . substr($temp_lang, 0, -1)) == ''){
			    	fputs($file,  'msgstr " "');
			    }
			    else{
			    	fputs($file,  'msgstr "' . $request->getPost('help_' . substr($temp_lang, 0, -1)) . '"');
			    }
			    fputs($file, PHP_EOL);
			}
		    }
		    fclose($file_temp);
		    fclose($file);
		    unlink('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '_temp.po');
		    // compile from po to mo
		    shell_exec('msgfmt /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po -o /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.mo');
		}
		fclose($file_lang);

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
    public function addCategoryAction()
    {
	// Session value to know if the translation key already exist
	$_SESSION['erreur_exist'] = 0;

	$tabToGet = ['translation_key', 'csrf', 'submit'];

        $form = $this->get('adminCategoryForm');

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

	    // Determine if the translation key already exist
            $cmd='grep -c -w ' . $request->getPost('translation_key') . ' /var/www/diagnostic/language/en.po';
            if(exec($cmd) != 0){ $_SESSION['erreur_exist'] = 1;}

            if ($form->isValid() && $_SESSION['erreur_exist'] == 0) {
                $formData = [];
                foreach ($tabToGet as $key) {
                    $formData[$key] = $form->getData()[$key];
                }
                $categoryService = $this->get('categoryService');
                $categoryService->create((array)$formData);
                $categoryService->resetCache();

		// Add translation to the .po files.
                $file_lang = fopen('/var/www/diagnostic/language/languages.txt', 'r');
                for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
                    $temp_lang = fgets($file_lang, 4096);
                    rename('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', '/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '_temp.po');
                    $file_temp = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '_temp.po', 'r');
                    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'w');
                    while (!feof($file_temp)) {
                        $temp = fgets($file_temp, 4096);
                        fputs($file, $temp);
                        if (substr($temp, 7, -2) == '__category' . ($_SESSION['nb_categories']-1)) {
                            $temp = fgets($file_temp, 4096);
                            fputs($file, $temp);
                            fputs($file, PHP_EOL);
                            fputs($file,  'msgid "' . $request->getPost('translation_key') . '"');
                            fputs($file, PHP_EOL);
                            fputs($file,  'msgstr "' . $request->getPost('translation_' . substr($temp_lang, 0, -1)) . '"');
                            fputs($file, PHP_EOL);
			}
                    }
                    fclose($file_temp);
                    fclose($file);
                    unlink('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '_temp.po');
                    // compile from po to mo
                    shell_exec('msgfmt /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po -o /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.mo');
                }
                fclose($file_lang);

		//redirect
                return $this->redirect()->toRoute('admin', ['controller' => 'index', 'action' => 'categories']);
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
    public function modifyQuestionAction()
    {
	// Session value to know if the translation key already exist
	$_SESSION['erreur_exist'] = 0;

	$tabToGet = ['translation_key', 'category_id', 'threshold', 'csrf', 'submit'];

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
                $form->get('translation_key')->setValue($question->getTranslationKey());
		$cat = $question; // $cat equal to the question to modify
            }
        }

	// Display the current value of the translation in the form-text (all languages)
	$file_lang = fopen('/var/www/diagnostic/language/languages.txt', 'r');
	for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
	    $temp_lang = fgets($file_lang, 4096);
   	    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'r');
            while (!feof($file)) { // Read the file
                $temp = fgets($file, 4096); // Variable which contains one by one lines of the file
	        // This condition determines where the translation key is in the file, and put its translation in a session variable
	        if($temp == 'msgid "' . $cat->getTranslationKey() . '"' . PHP_EOL){$_SESSION['value_' . substr($temp_lang, 0, -1)] = fgets($file, 4096);}
		if($temp == 'msgid "' . $cat->getTranslationKey() . 'help"' . PHP_EOL){$_SESSION['value_' . substr($temp_lang, 0, -1) . '_help'] = fgets($file, 4096);}
            }
            fclose($file);
	    $_SESSION['value_' . substr($temp_lang, 0, -1)] = substr($_SESSION['value_' . substr($temp_lang, 0, -1)], 8, -2);
	    $_SESSION['value_' . substr($temp_lang, 0, -1) . '_help'] = substr($_SESSION['value_' . substr($temp_lang, 0, -1) . '_help'], 8, -2);
	}
	fclose($file_lang);

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

	    // Determine if the translation key already exist
	    $cmd='grep -c -w ' . $request->getPost('translation_key') . ' /var/www/diagnostic/language/en.po';
            if(exec($cmd) != 0){ $_SESSION['erreur_exist'] = 1;}
	    // If the translation key is the same than the current one, there is no error. Happens when you only want to change translations
	    if($request->getPost('translation_key') == $cat->getTranslationKey()){$_SESSION['erreur_exist'] = 0;}

            if ($form->isValid() && $_SESSION['erreur_exist'] == 0) {
                $formData = [];
                foreach ($tabToGet as $key) {
                    $formData[$key] = $form->getData()[$key];
                }

                $questionService->update($id, (array)$formData);
                $questionService->resetCache();

		// Create variables which will determine where to delete previous information in the translation files
		$file_lang = fopen('/var/www/diagnostic/language/languages.txt', 'r');
        	for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
            	    $temp_lang = fgets($file_lang, 4096);
            	    $fileCount = -1; // Variable to determine the position of the current line
            	    $num_line = 0;
            	    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'r');
            	    while (!feof($file)) {
                	$temp = fgets($file, 4096);
                	$fileCount++;
                	if($temp == 'msgid "' . $cat->getTranslationKey() . '"' . PHP_EOL){$num_line = $fileCount; break;}
            	    }
            	    fclose($file);

		    // Rewrite the new translations
		    rename('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', '/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '_temp.po');
                    $file_temp = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '_temp.po', 'r');
                    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'w');
                    while (!feof($file_temp)) {
                        $temp = fgets($file_temp, 4096);
                        fputs($file, $temp);
                        if (substr($temp, 7, -2) == $cat->getTranslationKey() . 'help') {
                            $temp = fgets($file_temp, 4096);
                            fputs($file, $temp);
                            fputs($file, PHP_EOL);
                            fputs($file,  'msgid "' . $request->getPost('translation_key') . '"');
                            fputs($file, PHP_EOL);
                            fputs($file,  'msgstr "' . $request->getPost('translation_' . substr($temp_lang, 0, -1)) . '"');
                            fputs($file, PHP_EOL);
                            fputs($file, PHP_EOL);
                            fputs($file,  'msgid "' . $request->getPost('translation_key') . 'help"');
                            fputs($file, PHP_EOL);
                            if($request->getPost('help_' . substr($temp_lang, 0, -1)) == ''){
                                fputs($file,  'msgstr " "');
                            }
                            else{
                                fputs($file,  'msgstr "' . $request->getPost('help_' . substr($temp_lang, 0, -1)) . '"');
                            }
                            fputs($file, PHP_EOL);
                        }
                    }
                    fclose($file_temp);
                    fclose($file);
                    unlink('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '_temp.po');

		    // Open the translation files and delete previous questions in order to add them with changes.
	 	    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'r');
   		    $contents = fread($file, filesize('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po'));
		    fclose($file);
   		    $contents = explode(PHP_EOL, $contents); // PHP_EOL equals to /n in Linux
   		    unset($contents[$num_line-1]); // Delete the line break
	      	    unset($contents[$num_line]); // Delete the translation key
		    unset($contents[$num_line+1]); // Delete the translation
		    unset($contents[$num_line+2]); // Delete the line break
		    unset($contents[$num_line+3]); // Delete the help translation key
		    unset($contents[$num_line+4]); // Delete the help translation
   		    $contents = array_values($contents);
  		    $contents = implode(PHP_EOL, $contents);
		    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'w');
   		    fwrite($file, $contents); // Write the file without the deleted files
		    fclose($file);

                    // compile from po to mo
                    shell_exec('msgfmt /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po -o /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.mo');
		}
		fclose($file_lang);

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
     * Modify Category
     *
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function modifyCategoryAction()
    {
	// Session value to know if the translation key already exist
	$_SESSION['erreur_exist'] = 0;

   	$tabToGet = ['translation_key', 'csrf', 'submit'];

	$id = $this->getEvent()->getRouteMatch()->getParam('id');

        if (is_null($id)) {
            throw new \Exception('Category not exist');
        }

        $form = $this->get('adminCategoryForm');

        $form->get('submit')->setValue('__modify');

        $categoryService = $this->get('categoryService');
        $currentCategory = $categoryService->getCategoryById($id);

	foreach ($currentCategory as $category) {
            if ($category->getId() == $id) {
                $form->get('translation_key')->setValue($category->getTranslationKey());
		$cat = $category; // $cat equal to the category to modify
            }
        }

	// Display the current value of the translation in the form-text (all languages)
        $file_lang = fopen('/var/www/diagnostic/language/languages.txt', 'r');
        for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
            $temp_lang = fgets($file_lang, 4096);
            $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'r');
            while (!feof($file)) { // Read the file
                $temp = fgets($file, 4096); // Variable which contains one by one lines of the file
                // This condition determines where the translation key is in the file, and put its translation in a session variable
                if($temp == 'msgid "' . $cat->getTranslationKey() . '"' . PHP_EOL){$_SESSION['value_' . substr($temp_lang, 0, -1)] = fgets($file, 4096);}
            }
            fclose($file);
            $_SESSION['value_' . substr($temp_lang, 0, -1)] = substr($_SESSION['value_' . substr($temp_lang, 0, -1)], 8, -2);
        }
        fclose($file_lang);

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            // Determine if the translation key already exist
            $cmd='grep -c -w ' . $request->getPost('translation_key') . ' /var/www/diagnostic/language/en.po';
            if(exec($cmd) != 0){ $_SESSION['erreur_exist'] = 1;}
            // If the translation key is the same than the current one, there is no error. Happens when you only want to change translations
            if($request->getPost('translation_key') == $cat->getTranslationKey()){$_SESSION['erreur_exist'] = 0;}

            if ($form->isValid() && $_SESSION['erreur_exist'] == 0) {
                $formData = [];
                foreach ($tabToGet as $key) {
                    $formData[$key] = $form->getData()[$key];
                }

                $categoryService->update($id, (array)$formData);
                $categoryService->resetCache();

	        // Create variables which will determine where to delete previous information in the translation files
                $file_lang = fopen('/var/www/diagnostic/language/languages.txt', 'r');
                for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
                    $temp_lang = fgets($file_lang, 4096);
                    $fileCount = -1; // Variable to determine the position of the current line
                    $num_line = 0;
                    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'r');
                    while (!feof($file)) {
                        $temp = fgets($file, 4096);
                        $fileCount++;
                        if($temp == 'msgid "' . $cat->getTranslationKey() . '"' . PHP_EOL){$num_line = $fileCount; break;}
                    }
                    fclose($file);

                    // Rewrite the new translations
                    rename('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', '/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '_temp.po');
                    $file_temp = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '_temp.po', 'r');
                    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'w');
                    while (!feof($file_temp)) {
                        $temp = fgets($file_temp, 4096);
                        fputs($file, $temp);
                        if (substr($temp, 7, -2) == $cat->getTranslationKey()) {
                            $temp = fgets($file_temp, 4096);
                            fputs($file, $temp);
                            fputs($file, PHP_EOL);
                            fputs($file,  'msgid "' . $request->getPost('translation_key') . '"');
                            fputs($file, PHP_EOL);
                            fputs($file,  'msgstr "' . $request->getPost('translation_' . substr($temp_lang, 0, -1)) . '"');
                            fputs($file, PHP_EOL);
                        }
		    }
                    fclose($file_temp);
                    fclose($file);
                    unlink('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '_temp.po');

                    // Open the translation files and delete previous questions in order to add them with changes.
                    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'r');
                    $contents = fread($file, filesize('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po'));
                    fclose($file);
                    $contents = explode(PHP_EOL, $contents); // PHP_EOL equals to /n in Linux
                    unset($contents[$num_line-1]); // Delete the line break
                    unset($contents[$num_line]); // Delete the translation key
                    unset($contents[$num_line+1]); // Delete the translation
                    $contents = array_values($contents);
                    $contents = implode(PHP_EOL, $contents);
                    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'w');
                    fwrite($file, $contents); // Write the file without the deleted files
                    fclose($file);

                    // compile from po to mo
                    shell_exec('msgfmt /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po -o /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.mo');
                }
                fclose($file_lang);

                //redirect
                return $this->redirect()->toRoute('admin', ['controller' => 'index', 'action' => 'categories']);
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

	// Delete translations from the translation files
	$file_lang = fopen('/var/www/diagnostic/language/languages.txt', 'r');
	for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
	    $temp_lang = fgets($file_lang, 4096);
	    $fileCount = -1;
	    $num_line = 0;
   	    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'r');
            while (!feof($file)) {
                $temp = fgets($file, 4096);
                $fileCount++;
	        if($temp == 'msgid "' . $cat->getTranslationKey() . '"' . PHP_EOL){$num_line = $fileCount;}
            }
            fclose($file);

	    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'r');
   	    $contents = fread($file, filesize('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po'));
	    fclose($file);
   	    $contents = explode(PHP_EOL, $contents);
   	    unset($contents[$num_line-1]);
	    unset($contents[$num_line]);
	    unset($contents[$num_line+1]);
	    unset($contents[$num_line+2]);
	    unset($contents[$num_line+3]);
	    unset($contents[$num_line+4]);
   	    $contents = array_values($contents);
  	    $contents = implode(PHP_EOL, $contents);
	    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'w');
   	    fwrite($file, $contents);
	    fclose($file);
	    shell_exec('msgfmt /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po -o /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.mo');
	}
	fclose($file_lang);

	$questionService->delete($id);

        //redirect
        return $this->redirect()->toRoute('admin', ['controller' => 'index', 'action' => 'questions']);
    }

    /**
     * Delete category
     *
     * @return \Zend\Http\Response
     * @throws \Exception
     */
    public function deleteCategoryAction()
    {
        //id user
        $id = $this->getEvent()->getRouteMatch()->getParam('id');

        //retrieve bdd categories
        $categoryService = $this->get('categoryService');
        $categories = $categoryService->getBddCategories();
        $categoriesIds = [];
        foreach ($categories as $category) {
            $categoriesIds[] = $category->getId();
	    if($category->getId() == $id){$cat = $category;}
        }

        //security
        if (!in_array($id, $categoriesIds)) {
            throw new \Exception('Category not exist');
        }

	// Search questions linked to the category to delete them
        $questionService = $this->get('questionService');
        $questions = $questionService->getBddQuestions();
        foreach ($questions as $question) {
	    if($question->getCategoryTranslationKey() == $cat->getTranslationKey()){
		$file_lang = fopen('/var/www/diagnostic/language/languages.txt', 'r');
        	for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
            	    $temp_lang = fgets($file_lang, 4096);
            	    $fileCount = -1;
            	    $num_line = 0;
            	    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'r');
	            while (!feof($file)) {
        	        $temp = fgets($file, 4096);
	                $fileCount++;
		        if($temp == 'msgid "' . $question->getTranslationKey() . '"'.PHP_EOL){$num_line = $fileCount;}
       	 	    }
         	    fclose($file);

		    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'r');
            	    $contents = fread($file, filesize('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po'));
            	    fclose($file);
            	    $contents = explode(PHP_EOL, $contents);
            	    unset($contents[$num_line-1]);
            	    unset($contents[$num_line]);
            	    unset($contents[$num_line+1]);
            	    unset($contents[$num_line+2]);
            	    unset($contents[$num_line+3]);
            	    unset($contents[$num_line+4]);
            	    $contents = array_values($contents);
            	    $contents = implode(PHP_EOL, $contents);
            	    $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'w');
            	    fwrite($file, $contents);
            	    fclose($file);
            	    shell_exec('msgfmt /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po -o /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.mo');
		}
		fclose($file_lang);
  	    }
        }

	// See comments in the delete function above
	$file_lang = fopen('/var/www/diagnostic/language/languages.txt', 'r');
        for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
            $temp_lang = fgets($file_lang, 4096);
            $fileCount = -1;
            $num_line = 0;
            $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'r');
            while (!feof($file)) {
                $temp = fgets($file, 4096);
                $fileCount++;
                if($temp == 'msgid "' . $cat->getTranslationKey() . '"' . PHP_EOL){$num_line = $fileCount;}
            }
            fclose($file);

            $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'r');
            $contents = fread($file, filesize('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po'));
            fclose($file);
            $contents = explode(PHP_EOL, $contents);
            unset($contents[$num_line-1]);
            unset($contents[$num_line]);
            unset($contents[$num_line+1]);
            $contents = array_values($contents);
            $contents = implode(PHP_EOL, $contents);
            $file = fopen('/var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po', 'w');
            fwrite($file, $contents);
            fclose($file);
            shell_exec('msgfmt /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.po -o /var/www/diagnostic/language/' . substr($temp_lang, 0, -1) . '.mo');
	}
	fclose($file_lang);

	$categoryService->delete($id);

        //redirect
        return $this->redirect()->toRoute('admin', ['controller' => 'index', 'action' => 'categories']);
    }
}
