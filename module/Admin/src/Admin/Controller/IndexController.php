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
    protected $adminTemplateForm;
    protected $adminAddTranslationForm;

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
     * Templates
     *
     * @return ViewModel
     */
    public function templatesAction()
    {
        $error_upload = 0; // File not valid
        $success_upload = 0; // File valid

        $request = $this->getRequest();
        $form = $this->get('adminTemplateForm');

        if ($request->isPost()) {
            $form->setData($request->getPost());

            $location_lang = '/var/www/diagnostic/language/';
            $file_lang = fopen($location_lang . 'languages.txt', 'r');
            for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
                $temp_lang = fgets($file_lang, 4096);

                // Download the template in the current language
                if (isset($_POST['dl'.$i])){
                    $file = '/var/www/diagnostic/data/resources/model_' . substr($temp_lang, 0, -1) . '.docx';

                    if (filesize($file) != 0) {
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/octet-stream');
                        header('Content-Disposition: attachment; filename=model_' . substr($temp_lang, 0, -1) . '.docx');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate');
                        header('Pragma: public');
                        header('Content-Length: ' . filesize($file));
                        readfile($file);
                    }else {
                        $file = '/var/www/diagnostic/data/resources/model_' . $_SESSION['lang'] . '.docx';
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/octet-stream');
                        header('Content-Disposition: attachment; filename=model_' . substr($temp_lang, 0, -1) . '.docx');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate');
                        header('Pragma: public');
                        header('Content-Length: ' . filesize($file));
                        readfile($file);
                    }
                }
            }
            fclose($file_lang);

            // Upload the modified template
            if (isset($_POST['submit_file'])){
                $file_country = fopen($location_lang . 'code_country.txt', 'r');
                while(!feof($file_country)){
                    $temp_country = fgets($file_country, 4096);
                    if($temp_country == substr($_FILES['file']['name'], 6, -5).PHP_EOL){$valid_file = substr($temp_country, 0, -1); break;}
                    $valid_file = 'en';
                }
                fclose($file_country);

                if ($_FILES['file']['name'] == 'model_' . $valid_file . '.docx' && file_exists('/var/www/diagnostic/data/resources/' . $_FILES['file']['name'])) {

                    move_uploaded_file($_FILES['file']['tmp_name'], '/var/www/diagnostic/data/resources/' . $_FILES['file']['name']);

                    // Search the modified template
                    $file_lang = fopen($location_lang . 'languages.txt', 'r');
                    $num_line = -1;
                    while(!feof($file_lang)) {
                        $temp_lang = fgets($file_lang, 4096);
                        $num_line+=1;
                        if($temp_lang == substr($_FILES['file']['name'], 6, -5).PHP_EOL) {break;}
                    }
                    fclose($file_lang);

                    // Change the user mail for the modified template
                    $file_user = fopen('/var/www/diagnostic/module/Admin/config/users.txt', 'r');
                    $contents = fread($file_user, filesize('/var/www/diagnostic/module/Admin/config/users.txt'));
                    fclose($file_user);
                    $contents = explode(PHP_EOL, $contents); // PHP_EOL equals to /n in Linux
                    $contents[$num_line] = $_SESSION['email']; // Change the user mail with the new one
                    $contents = array_values($contents);
                    $contents = implode(PHP_EOL, $contents);
                    $file_user = fopen('/var/www/diagnostic/module/Admin/config/users.txt', 'w');
                    fwrite($file_user, $contents); // Write the file with the new user mail
                    fclose($file_user);
                    $success_upload = 1;
                }else {
                    $error_upload = 1;
                }
            }
        }

        //send to view
        return new ViewModel([
            'form' => $form,
            'error_upload' => $error_upload,
            'success_upload' => $success_upload
        ]);
    }

    /**
     * Languages
     *
     * @return ViewModel
     */
    public function languagesAction()
    {
	$location_lang = '/var/www/diagnostic/language/';

        // Variable to display error message when adding or deleting an invalid language
        $error_lang_exist = 0; // The language already exist and can't be added
        $error_lang_add = 0; // The language doesn't exist and can't be deleted
        $error_lang_del = 0; // English language can't be deleted
        $error_lang_del2 = 0; // You can't delete a current used language

        //retrieve questions
        $questionService = $this->get('questionService');
        $questions = $questionService->getBddQuestions();

        $form = $this->get('adminLanguageForm');
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());

            // Don't reset the reference language when clicking a button
            if (isset($_POST['submit_lang_add']) || isset($_POST['submit_lang_del']) || isset($_POST['submit_lang_ref']) || isset($_POST['submit_all']) || isset($_POST['submit__dl_report']) || isset($_POST['submit_file'])) {
                $_SESSION['base_lang'] = 1;
            }

            // Skip categories and questions
            $file = fopen($location_lang . 'en.po', 'r');
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

            // num_line_all is used to change all translations in 1 button
            $file = fopen($location_lang . 'en.po', 'r');
            $num_line_all = -1;
            while (!feof($file)) {
                $temp = fgets($file, 4096);
                $num_line_all++;
                if (substr($temp, 7, -2) == '__diagnostic') {$num_line_all++; break;}
            }
            fclose($file);

            // Search the translation key in order to know the translation to change or delete
            $file_lang = fopen($location_lang . 'languages.txt', 'r');
            for ($j=1; $j<$_SESSION['nb_lang']; $j++) {
                $temp_lang = fgets($file_lang, 4096);
                if ($_SESSION['lang'] == substr($temp_lang, 0, -1)) {

                    for ($i=1; $i<=$nb_translation; $i++){

                        // Action to modify translation
                        if (isset($_POST['mod'.$i])){
                            $_SESSION['base_lang'] = 1; // Don't change translation ref when modifying translation

                            // Search for the line of the translation key to modify
                            $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
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

                            // Modify the translation
                            $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
                            $contents = fread($file, filesize($location_lang . substr($temp_lang, 0, -1) . '.po'));
                            fclose($file);
                            $contents = explode(PHP_EOL, $contents); // PHP_EOL equals to /n in Linux
                            $contents[$num_line+1] = 'msgstr ' . '"' . $request->getPost('translation'.$i) . '"'; // Change the translation with the new one
                            $contents = array_values($contents);
                            $contents = implode(PHP_EOL, $contents);
                            $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'w');
                            fwrite($file, $contents); // Write the file with the new translation
                            fclose($file);

                            shell_exec('msgfmt ' . $location_lang . substr($temp_lang, 0, -1) . '.po -o ' . $location_lang . substr($temp_lang, 0, -1) . '.mo');
                        }

                        // Action to delete translation
                        if (isset($_POST['del'.$i])){

                            // Search for the line of the translation key to delete
                            $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
                            $fileCount = -1;
                            $num_line3 = 0;
                            while (!feof($file)) {
                                $temp = fgets($file, 4096);
                                $fileCount++;
                                if(substr($temp, 7, -2) == $_SESSION['key_'  . substr($temp_lang, 0, -1)][$i]){
                                    $num_line3 = $fileCount;
                                }
                            }
                            fclose($file);
                        }
                    }

                    // Action to modify all translations
                    if (isset($_POST['submit_all'])){
                        $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
                        $contents = fread($file, filesize($location_lang . substr($temp_lang, 0, -1) . '.po'));
                        fclose($file);
                        $contents = explode(PHP_EOL, $contents); // PHP_EOL equals to /n in Linux
                        for ($i=1; $i<=$nb_translation; $i++){
                            $contents[$num_line_all] = 'msgstr ' . '"' . $request->getPost('translation'.$i) . '"'; // Change the translation with the new one
                            $num_line_all+=3;
                        }
                        $contents = array_values($contents);
                        $contents = implode(PHP_EOL, $contents);
                        $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'w');
                        fwrite($file, $contents); // Write the file with the new translation
                        fclose($file);

                        shell_exec('msgfmt ' . $location_lang . substr($temp_lang, 0, -1) . '.po -o ' . $location_lang . substr($temp_lang, 0, -1) . '.mo');
                    }
                }

                // Change reference language thanks to the session value
                if (isset($_POST['submit_lang_ref'])){
                    if ($request->getPost('language_ref') == $j-1) {
                        $_SESSION['change_language'] = substr($temp_lang, 0, -1);
                    }
                }
            }
            fclose($file_lang);

            // Delete translation
            $file_lang = fopen($location_lang . 'languages.txt', 'r');
            for ($j=1; $j<$_SESSION['nb_lang']; $j++) {
                $temp_lang = fgets($file_lang, 4096);
                for ($i=1; $i<=$nb_translation; $i++){
                    if (isset($_POST['del'.$i])){
                        $_SESSION['base_lang'] = 1;

                        $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
                        $contents = fread($file, filesize($location_lang . substr($temp_lang, 0, -1) . '.po'));
                        fclose($file);
                        $contents = explode(PHP_EOL, $contents); // PHP_EOL equals to /n in Linux
                        unset($contents[$num_line3-1]); // Delete the line break
                        unset($contents[$num_line3]); // Delete the translation key
                        unset($contents[$num_line3+1]); // Delete the translation
                        $contents = array_values($contents);
                        $contents = implode(PHP_EOL, $contents);
                        $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'w');
                        fwrite($file, $contents); // Write the file with the new translation
                        fclose($file);

                        shell_exec('msgfmt ' . $location_lang . substr($temp_lang, 0, -1) . '.po -o ' . $location_lang . substr($temp_lang, 0, -1) . '.mo');
                    }
                }
            }
            fclose($file_lang);

            // Add a language
            if (isset($_POST['submit_lang_add'])) {
                // Count the number of available languages
                $file_country = fopen($location_lang . 'code_country.txt', 'r');
                $fileCount = 0;
                while (!feof($file_country)) {
                    $temp_country = fgets($file_country, 4096);
                    $fileCount++;
                }
                fclose($file_country);

                $file_country = fopen($location_lang . 'code_country.txt', 'r');
                for ($i=0; $i<$fileCount; $i++) {
                    $temp_country = fgets($file_country, 4096);
                    if ($request->getPost('add_language') == $i) {
                        $temp = $temp_country;
                        $file_temp = fopen($location_lang . 'languages.txt', 'a+');
                        while (!feof($file_temp)) {
                            $temp_country = fgets($file_temp, 4096);
                            if ($temp_country == $temp) {$error_lang_exist=1; break;}
                        }

                        if ($error_lang_exist == 0) {
                            fputs($file_temp, $temp);

                            // Creation .po file by copying the beginning of the english file
                            $new_file = fopen($location_lang . substr($temp, 0, -1) . '.po', 'a+');
                            $en_file = fopen($location_lang . 'en.po', 'r');
                            while (!feof($en_file)) {
                                $en_temp = fgets($en_file, 4096);
                                if (substr($en_temp, 7, -2) == '__category1') {break;}
                                fputs($new_file, $en_temp);
                            }
                            $fileCount = 1;
                            while (!feof($en_file)) {
                                if ($fileCount == 1) {fputs($new_file, $en_temp);}
                                elseif ($fileCount == 2) {fputs($new_file, 'msgstr ""');}
                                else {fputs($new_file, PHP_EOL); fputs($new_file, PHP_EOL); $fileCount = 0;}
                                $en_temp = fgets($en_file, 4096);
                                $fileCount++;
                            }
                            fputs($new_file, PHP_EOL);
                            fclose($en_file);
                            fclose($new_file);

                            shell_exec('msgfmt ' . $location_lang . substr($temp, 0, -1) . '.po -o ' . $location_lang . substr($temp, 0, -1) . '.mo');

                            // Create the template
                            $file_template = fopen('/var/www/diagnostic/data/resources/model_' . substr($temp, 0, -1) . '.docx', 'a+');
                            copy('/var/www/diagnostic/data/resources/model_en.docx', '/var/www/diagnostic/data/resources/model_' . substr($temp, 0, -1) . '.docx');
                            fclose($file_template);

                            // Create the user mail for the template
                            $file_user = fopen('/var/www/diagnostic/module/Admin/config/users.txt', 'a+');
                            fputs($file_user, $_SESSION['email']);
                            fputs($file_user, PHP_EOL);
                            fclose($file_user);
                        }
                        fclose($file_temp);
                    }
                }
                fclose($file_country);
            }

            // Delete a language
            if (isset($_POST['submit_lang_del'])) {
                $error_lang_add = 1;

                $file_lang = fopen($location_lang . 'code_country.txt', 'r');
                $fileCount = 0;
                while (!feof($file_lang)) {
                    $temp_lang = fgets($file_lang, 4096);
                    $fileCount++;
                }
                fclose($file_lang);

                $file_lang = fopen($location_lang . 'code_country.txt', 'r');
                for ($i=0; $i<$fileCount; $i++) {
                    $temp_lang = fgets($file_lang, 4096);
                    if ($request->getPost('add_language') == $i) {
                        $temp = $temp_lang;
                        $file_temp = fopen($location_lang . 'languages.txt', 'r');
                        $num_line = -1;
                        while (!feof($file_temp)) {
                            $temp_lang = fgets($file_temp, 4096);
                            $num_line++;
                            if ($temp_lang == $temp) {
                                $error_lang_add=2;
                                if($temp_lang == $_SESSION['lang'].PHP_EOL) {$error_lang_del2 = 1;}
                                break;
                            }
                        }
                        fclose($file_temp);

                        // Avoid to delete english language, which is used to create other languages
                        if ($temp_lang == 'en' . PHP_EOL) {$error_lang_del=1;}

                        if ($error_lang_add == 2 && $error_lang_del == 0 && $error_lang_del2 == 0) {

                            $file_temp = fopen($location_lang . 'languages.txt', 'r');
                            $contents = fread($file_temp, filesize($location_lang . 'languages.txt'));
                            fclose($file_temp);
                            $contents = explode(PHP_EOL, $contents); // PHP_EOL equals to /n in Linux
                            unset($contents[$num_line]); // Delete the user
                            $contents = array_values($contents);
                            $contents = implode(PHP_EOL, $contents);
                            $file_temp = fopen($location_lang . 'languages.txt', 'w');
                            fwrite($file_temp, $contents); // Write the file without the deleted files
                            fclose($file_temp);

                            unlink($location_lang . substr($temp_lang, 0, -1) . '.po');
                            unlink($location_lang . substr($temp_lang, 0, -1) . '.mo');

                            // Delete the template if exist
                            if(file_exists('/var/www/diagnostic/data/resources/model_' . substr($temp_lang, 0, -1) . '.docx')) {
                                unlink('/var/www/diagnostic/data/resources/model_' . substr($temp_lang, 0, -1) . '.docx' );
                            }

                            $file_user = fopen('/var/www/diagnostic/module/Admin/config/users.txt', 'r');
                            $contents = fread($file_user, filesize('/var/www/diagnostic/module/Admin/config/users.txt'));
                            fclose($file_user);
                            $contents = explode(PHP_EOL, $contents); // PHP_EOL equals to /n in Linux
                            unset($contents[$num_line]); // Delete the language
                            $contents = array_values($contents);
                            $contents = implode(PHP_EOL, $contents);
                            $file_user = fopen('/var/www/diagnostic/module/Admin/config/users.txt', 'w');
                            fwrite($file_user, $contents); // Write the file without the deleted files
                            fclose($file_user);
                        }
                    }
                }
                fclose($file_lang);
            }
        }

        // English language when refreshing the page
        if ($_SESSION['base_lang'] == 0) {
            $_SESSION['change_language'] = 'en';
        }

        //send to view
        return new ViewModel([
            'form' => $form,
            'questions' => $questions,
            'error_lang_exist' => $error_lang_exist,
            'error_lang_add' => $error_lang_add,
            'error_lang_del' => $error_lang_del,
            'error_lang_del2' => $error_lang_del2
        ]);
    }

    /**
     * Add question
     *
     * @return ViewModel
     */
    public function addQuestionAction()
    {
        $location_lang = '/var/www/diagnostic/language/';

        // Session value to know if the translation key already exist
        $_SESSION['erreur_exist'] = 0;

        // Things we need for the db
        $tabToGet = ['translation_key', 'category_id', 'threshold', 'csrf', 'submit'];

        $form = $this->get('adminQuestionForm');

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            // Determine if the translation key already exist
            $cmd='grep -c -w ' . $request->getPost('translation_key') . ' ' . $location_lang . 'en.po';
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
                $file_lang = fopen($location_lang . 'languages.txt', 'r');
                for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
                    $temp_lang = fgets($file_lang, 4096);
                    rename($location_lang . substr($temp_lang, 0, -1) . '.po', $location_lang . substr($temp_lang, 0, -1) . '_temp.po');
                    $file_temp = fopen($location_lang . substr($temp_lang, 0, -1) . '_temp.po', 'r');
                    $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'w');
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
                    unlink($location_lang . substr($temp_lang, 0, -1) . '_temp.po');

                    // compile from po to mo
                    shell_exec('msgfmt ' . $location_lang . substr($temp_lang, 0, -1) . '.po -o ' . $location_lang . substr($temp_lang, 0, -1) . '.mo');
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
        $location_lang = '/var/www/diagnostic/language/';

        // Session value to know if the translation key already exist
        $_SESSION['erreur_exist'] = 0;

        $tabToGet = ['translation_key', 'csrf', 'submit'];

        $form = $this->get('adminCategoryForm');

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            // Determine if the translation key already exist
            $cmd='grep -c -w ' . $request->getPost('translation_key') . ' ' . $location_lang . 'en.po';
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
                $file_lang = fopen($location_lang . 'languages.txt', 'r');
                for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
                    $temp_lang = fgets($file_lang, 4096);
                    rename($location_lang . substr($temp_lang, 0, -1) . '.po', $location_lang . substr($temp_lang, 0, -1) . '_temp.po');
                    $file_temp = fopen($location_lang . substr($temp_lang, 0, -1) . '_temp.po', 'r');
                    $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'w');
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
                    unlink($location_lang . substr($temp_lang, 0, -1) . '_temp.po');

                    // compile from po to mo
                    shell_exec('msgfmt ' . $location_lang . substr($temp_lang, 0, -1) . '.po -o ' . $location_lang . substr($temp_lang, 0, -1) . '.mo');
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
     * Add translation
     *
     * @return ViewModel
     */
    public function addTranslationAction()
    {
        $location_lang = '/var/www/diagnostic/language/';

        // Session value to know if the translation key already exist
        $_SESSION['erreur_exist'] = 0;

        $form = $this->get('adminAddTranslationForm');

        //form is post and valid
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            // Determine if the translation key already exist
            $cmd='grep -c -w ' . $request->getPost('translation_key') . ' ' . $location_lang . 'en.po';
            if(exec($cmd) != 0){ $_SESSION['erreur_exist'] = 1;}

            if ($form->isValid() && $_SESSION['erreur_exist'] == 0) {

                // Add translation to the .po files.
                $file_lang = fopen($location_lang . 'languages.txt', 'r');
                for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
                    $temp_lang = fgets($file_lang, 4096);
                    $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'a+');
                    fputs($file, PHP_EOL);
                    fputs($file,  'msgid "' . $request->getPost('translation_key') . '"');
                    fputs($file, PHP_EOL);
                    fputs($file,  'msgstr "' . $request->getPost('translation_' . substr($temp_lang, 0, -1)) . '"');
                    fputs($file, PHP_EOL);
                    fclose($file);

                    // compile from po to mo
                    shell_exec('msgfmt ' . $location_lang . substr($temp_lang, 0, -1) . '.po -o ' . $location_lang . substr($temp_lang, 0, -1) . '.mo');
                }
                fclose($file_lang);

                //redirect
                return $this->redirect()->toRoute('admin', ['controller' => 'index', 'action' => 'languages']);
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
        $location_lang = '/var/www/diagnostic/language/';

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
        $file_lang = fopen($location_lang . 'languages.txt', 'r');
        for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
            $temp_lang = fgets($file_lang, 4096);
            $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
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
            $cmd='grep -c -w ' . $request->getPost('translation_key') . ' ' . $location_lang . 'en.po';
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
                $file_lang = fopen($location_lang . 'languages.txt', 'r');
                for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
                    $temp_lang = fgets($file_lang, 4096);
                    $fileCount = -1; // Variable to determine the position of the current line
                    $num_line = 0;
                    $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
                    while (!feof($file)) {
                        $temp = fgets($file, 4096);
                        $fileCount++;
                        if($temp == 'msgid "' . $cat->getTranslationKey() . '"' . PHP_EOL){$num_line = $fileCount; break;}
                    }
                    fclose($file);

                    // Rewrite the new translations
                    rename($location_lang . substr($temp_lang, 0, -1) . '.po', $location_lang . substr($temp_lang, 0, -1) . '_temp.po');
                    $file_temp = fopen($location_lang . substr($temp_lang, 0, -1) . '_temp.po', 'r');
                    $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'w');
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
                    unlink($location_lang . substr($temp_lang, 0, -1) . '_temp.po');

                    // Open the translation files and delete previous questions in order to add them with changes
                    $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
                    $contents = fread($file, filesize($location_lang . substr($temp_lang, 0, -1) . '.po'));
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
                    $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'w');
                    fwrite($file, $contents); // Write the file without the deleted files
                    fclose($file);

                    // compile from po to mo
                    shell_exec('msgfmt ' . $location_lang . substr($temp_lang, 0, -1) . '.po -o ' . $location_lang . substr($temp_lang, 0, -1) . '.mo');
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
        $location_lang = '/var/www/diagnostic/language/';

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
        $file_lang = fopen($location_lang . 'languages.txt', 'r');
        for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
            $temp_lang = fgets($file_lang, 4096);
            $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
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
            $cmd='grep -c -w ' . $request->getPost('translation_key') . ' ' . $location_lang . 'en.po';
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
                $file_lang = fopen($location_lang . 'languages.txt', 'r');
                for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
                    $temp_lang = fgets($file_lang, 4096);
                    $fileCount = -1; // Variable to determine the position of the current line
                    $num_line = 0;
                    $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
                    while (!feof($file)) {
                        $temp = fgets($file, 4096);
                        $fileCount++;
                        if($temp == 'msgid "' . $cat->getTranslationKey() . '"' . PHP_EOL){$num_line = $fileCount; break;}
                    }
                    fclose($file);

                    // Rewrite the new translations
                    rename($location_lang . substr($temp_lang, 0, -1) . '.po', $location_lang . substr($temp_lang, 0, -1) . '_temp.po');
                    $file_temp = fopen($location_lang . substr($temp_lang, 0, -1) . '_temp.po', 'r');
                    $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'w');
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
                    unlink($location_lang . substr($temp_lang, 0, -1) . '_temp.po');

                    // Open the translation files and delete previous questions in order to add them with changes.
                    $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
                    $contents = fread($file, filesize($location_lang . substr($temp_lang, 0, -1) . '.po'));
                    fclose($file);
                    $contents = explode(PHP_EOL, $contents); // PHP_EOL equals to /n in Linux
                    unset($contents[$num_line-1]); // Delete the line break
                    unset($contents[$num_line]); // Delete the translation key
                    unset($contents[$num_line+1]); // Delete the translation
                    $contents = array_values($contents);
                    $contents = implode(PHP_EOL, $contents);
                    $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'w');
                    fwrite($file, $contents); // Write the file without the deleted files
                    fclose($file);

                    // compile from po to mo
                    shell_exec('msgfmt ' . $location_lang . substr($temp_lang, 0, -1) . '.po -o ' . $location_lang . substr($temp_lang, 0, -1) . '.mo');
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
        $location_lang = '/var/www/diagnostic/language/';

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
        $file_lang = fopen($location_lang . 'languages.txt', 'r');
        for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
            $temp_lang = fgets($file_lang, 4096);
            $fileCount = -1;
            $num_line = 0;
            $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
            while (!feof($file)) {
                $temp = fgets($file, 4096);
                $fileCount++;
                if($temp == 'msgid "' . $cat->getTranslationKey() . '"' . PHP_EOL){$num_line = $fileCount;}
            }
            fclose($file);

            $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
            $contents = fread($file, filesize($location_lang . substr($temp_lang, 0, -1) . '.po'));
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
            $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'w');
            fwrite($file, $contents);
            fclose($file);

            shell_exec('msgfmt ' . $location_lang . substr($temp_lang, 0, -1) . '.po -o ' . $location_lang . substr($temp_lang, 0, -1) . '.mo');
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
        $location_lang = '/var/www/diagnostic/language/';

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
                $file_lang = fopen($location_lang . 'languages.txt', 'r');
                for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
            	    $temp_lang = fgets($file_lang, 4096);
            	    $fileCount = -1;
            	    $num_line = 0;
            	    $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
                    while (!feof($file)) {
                        $temp = fgets($file, 4096);
                        $fileCount++;
                        if($temp == 'msgid "' . $question->getTranslationKey() . '"'.PHP_EOL){$num_line = $fileCount;}
                    }
                    fclose($file);

                    $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
                    $contents = fread($file, filesize($location_lang . substr($temp_lang, 0, -1) . '.po'));
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
                    $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'w');
                    fwrite($file, $contents);
                    fclose($file);

                    shell_exec('msgfmt ' . $location_lang . substr($temp_lang, 0, -1) . '.po -o ' . $location_lang . substr($temp_lang, 0, -1) . '.mo');
                }
                fclose($file_lang);
            }
        }

        // See comments in the delete function above
        $file_lang = fopen($location_lang . 'languages.txt', 'r');
        for ($i=1; $i<$_SESSION['nb_lang']; $i++) {
            $temp_lang = fgets($file_lang, 4096);
            $fileCount = -1;
            $num_line = 0;
            $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
            while (!feof($file)) {
                $temp = fgets($file, 4096);
                $fileCount++;
                if($temp == 'msgid "' . $cat->getTranslationKey() . '"' . PHP_EOL){$num_line = $fileCount;}
            }
            fclose($file);

            $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'r');
            $contents = fread($file, filesize($location_lang . substr($temp_lang, 0, -1) . '.po'));
            fclose($file);
            $contents = explode(PHP_EOL, $contents);
            unset($contents[$num_line-1]);
            unset($contents[$num_line]);
            unset($contents[$num_line+1]);
            $contents = array_values($contents);
            $contents = implode(PHP_EOL, $contents);
            $file = fopen($location_lang . substr($temp_lang, 0, -1) . '.po', 'w');
            fwrite($file, $contents);
            fclose($file);

            shell_exec('msgfmt ' . $location_lang . substr($temp_lang, 0, -1) . '.po -o ' . $location_lang . substr($temp_lang, 0, -1) . '.mo');
        }
        fclose($file_lang);

        $categoryService->delete($id);

        //redirect
        return $this->redirect()->toRoute('admin', ['controller' => 'index', 'action' => 'categories']);
    }
}
