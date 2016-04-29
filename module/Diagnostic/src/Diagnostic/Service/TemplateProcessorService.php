<?php
namespace Diagnostic\Service;
use PhpOffice\PhpWord\TemplateProcessor;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Session\Container;


/**
 * Template Processor Service
 *
 * @package Diagnostic\Service
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class TemplateProcessorService extends TemplateProcessor implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Set a new image
     *
     * @param string $search
     * @param string $replace
     */
    public function setImageValue($search, $replace)
    {
        // Sanity check
        if (!file_exists($replace))
        {
            return;
        }

        // Delete current image
        $this->zipClass->deleteName('word/media/' . $search);

        // Add a new one
        $this->zipClass->addFile($replace, 'word/media/' . $search);
    }

    /**
     * Generate word
     *
     * @param $data
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function generateWord($data, $questions, $results, $translator) {

        $data['date'] = date('Y/m/d');

        $filename = ucfirst($data['document']) . '_' . date('Y-m-d') . '.docx';
        $filepath = 'data/results/' . $filename;

        //retrieve categories
        $categories = [];
        foreach ($questions as $question) {
            $categories[$question->getCategoryId()] = $question->getCategoryTranslationKey();
        }

        //create word
        foreach ($data as $key => $value) {
            $this->setValue(strtoupper($key), $translator->translate($value));
            if ($key == 'state') {
                $this->setValue('TYPE', $translator->translate($value));
            }
        }

        //image
        $container = new Container('diagnostic');
        $this->setImageValue('image3.png', $container->pie);
        $this->setImageValue('image4.png', $container->bar);
        $this->setImageValue('image6.png', $container->radar);

        //recommandations
        $this->cloneRow('RECOMM_NUM', count($results));

        $i = 1;
        foreach ($results as $result) {
            $name = 'RECOMM_NUM#' . $i;
            $this->setValue($name, $i);
            $i++;
        }

        $i = 1;
        foreach ($results as $result) {
            $name = 'RECOMM_TEXT#' . $i;
            $this->setValue($name, $result['recommandation']);
            $i++;
        }

        $i = 1;
        foreach ($results as $questionId => $result) {
            $name = 'RECOMM_DOM#' . $i;
            $this->setValue($name, $translator->translate($categories[$questions[$questionId]->getCategoryId()]));
            $i++;
        }

        $i = 1;
        foreach ($results as $result) {
            $gravity = '';
            switch ($result['gravity']) {
                case 1:
                    $gravity = $translator->translate('__low');
                    break;
                case 2:
                    $gravity = $translator->translate('__medium');
                    break;
                case 3:
                    $gravity = $translator->translate('__strong');
                    break;
            }
            $name = 'RECOMM_GRAV#' . $i;
            $this->setValue($name, $gravity);
            $i++;
        }

        $i = 1;
        foreach ($results as $result) {
            $name = 'RECOMM_CURR_MAT#' . $i;
            $this->setValue($name, $result['maturity']);
            $i++;
        }

        $i = 1;
        foreach ($results as $result) {
            $name = 'RECOMM_TARG_MAT#' . $i;
            $this->setValue($name, $result['maturityTarget']);
            $i++;
        }

        $j = 1;
        foreach ($categories as $categoryId => $category) {

            $name = 'PRISE_NOTE_CATEG_' . $j;
            $this->setValue($name, $translator->translate($category));

            $nbCategoryResults = 0;
            foreach ($results as $questionId => $result) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $nbCategoryResults++;
                }
            }

            $this->cloneRow('PRISE_NOTE_TO_COLLECT_' . $j, $nbCategoryResults);

            $prise1 = 1;
            foreach ($results as $questionId => $result) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_TO_COLLECT_' . $j . '#' . $prise1;
                    $this->setValue($name, $translator->translate($questions[$questionId]->getTranslationKey()));
                    $prise1++;
                }
            }

            $prise2 = 1;
            foreach ($results as $questionId => $result) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_COLLECT_' . $j . '#' . $prise2;
                    $this->setValue($name, $result['notes']);
                    $prise2++;
                }
            }

            $prise3 = 1;
            foreach ($results as $questionId => $result) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_QUEST_' . $j . '#' . $prise3;
                    $this->setValue($name, $translator->translate($questions[$questionId]->getTranslationKeyHelp()));
                    $prise3++;
                }
            }

            $prise4 = 1;
            foreach ($results as $questionId => $result) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_1_' . $j . '#' . $prise4;
                    $value = ($result['maturity'] == 0) ? 'X' : '';
                    $this->setValue($name, $value);
                    $prise4++;
                }
            }

            $prise5 = 1;
            foreach ($results as $questionId => $result) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_2_' . $j . '#' . $prise5;
                    $value = ($result['maturity'] == 1) ? 'X' : '';
                    $this->setValue($name, $value);
                    $prise5++;
                }
            }

            $prise6 = 1;
            foreach ($results as $questionId => $result) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_3_' . $j . '#' . $prise6;
                    $value = ($result['maturity'] == 2) ? 'X' : '';
                    $this->setValue($name, $value);
                    $prise6++;
                }
            }

            $prise7 = 1;
            foreach ($results as $questionId => $result) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_4_' . $j . '#' . $prise7;
                    $value = ($result['maturity'] == 3) ? 'X' : '';
                    $this->setValue($name, $value);
                    $prise7++;
                }
            }

            $prise8 = 1;
            foreach ($results as $questionId => $result) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_RECOMM_' . $j . '#' . $prise8;
                    $this->setValue($name, $result['recommandation']);
                    $prise8++;
                }
            }

            $prise9 = 1;
            foreach ($results as $questionId => $result) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_TARG_MAT_' . $j . '#' . $prise9;
                    $this->setValue($name, $result['maturityTarget']);
                    $prise9++;
                }
            }

            $j++;
        }

        $this->saveAs($filepath);

        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Length: " . filesize("$filepath") . ";");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/octet-stream; ");
        header("Content-Transfer-Encoding: binary");

        readfile($filepath);
    }
}