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
        if (!file_exists($replace)) {
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
    public function generateWord($data, $questions, $results, $information, $translator)
    {

        $data['date'] = date('Y/m/d');

        $filename = ucfirst($data['document']) . '_' . date('Y-m-d') . '.docx';
        $filepath = 'data/results/' . $filename;

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

        //categories repartition
        $categoriesRepartition = [];
        $i = 0;
        foreach ($numberByCategories as $category => $categoryNumber) {
            $categoriesRepartition[$i]['label'] = $translator->translate($category);
            $categoriesRepartition[$i]['value'] = $categoryNumber;
            $i++;
        }

        foreach ($categories as $id => $label) {
            $categories[$id] = [
                'label' => $label,
                'percent' => (array_key_exists($id, $results['totalCategory'])) ? (int)$results['totalCategory'][$id] : 0,
                'percentTarget' => (array_key_exists($id, $results['totalCategoryTarget'])) ? (int)$results['totalCategoryTarget'][$id] : 0,
            ];
        }

        $recommandations = $results['recommandations'];

        //create word
        foreach ($data as $key => $value) {
            $this->setValue(strtoupper($key), $translator->translate($value));
            if ($key == 'state') {
                $this->setValue('TYPE', $translator->translate($value));
            }
        }

        //image
        $container = new Container('diagnostic');
        $this->setImageValue('image9.png', $container->bar);
        $this->setImageValue('image5.png', $container->pie);
        $this->setImageValue('image10.png', $container->radar);


        //number of recommandations
        $nbRecommandations = 0;
        foreach ($recommandations as $recommandation) {
            if ($recommandation['recommandation']) {
                $nbRecommandations++;
            }
        }

        if (isset($information['organization'])) {
            $this->setValue('ORGANIZATION_INFORMATION', $information['organization']);
        } else {
            $this->setValue('ORGANIZATION_INFORMATION', '');
        }

        if (isset($information['synthesis'])) {
            $this->setValue('EVALUATION_SYNTHESYS', $information['synthesis']);
        } else {
            $this->setValue('EVALUATION_SYNTHESYS', '');
        }

        //recommandations
        $this->cloneRow('RECOMM_NUM', $nbRecommandations);

        $i = 1;
        foreach ($recommandations as $recommandation) {
            if ($recommandation['recommandation']) {
                $this->setValue('RECOMM_NUM#' . $i, $i);
                $i++;
            }
        }

        $i = 1;
        foreach ($recommandations as $recommandation) {
            if ($recommandation['recommandation']) {
                $this->setValue('RECOMM_TEXT#' . $i, $recommandation['recommandation']);
                $i++;
            }
        }

        $i = 1;
        foreach ($recommandations as $questionId => $recommandation) {
            if ($recommandation['recommandation']) {
                $this->setValue('RECOMM_DOM#' . $i, $translator->translate($categories[$questions[$questionId]->getCategoryId()]['label']));
                $i++;
            }
        }

        $i = 1;
        foreach ($recommandations as $recommandation) {
            if ($recommandation['recommandation']) {
                $gravity = '';
                switch ($recommandation['gravity']) {
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
                $this->setValue('RECOMM_GRAV#' . $i, $gravity);
                $i++;
            }
        }

        $i = 1;
        foreach ($recommandations as $recommandation) {
            if ($recommandation['recommandation']) {
                $maturity = $translator->translate('__maturity_none');
                switch ($recommandation['maturity']) {
                    case 1:
                        $maturity = $translator->translate('__maturity_plan');
                        break;
                    case 2:
                        $maturity = $translator->translate('__maturity_medium');
                        break;
                    case 3:
                        $maturity = $translator->translate('__maturity_ok');
                        break;
                }
                $this->setValue('RECOMM_CURR_MAT#' . $i, $maturity);
                $i++;
            }
        }

        $i = 1;
        foreach ($recommandations as $recommandation) {
            if ($recommandation['recommandation']) {
                $maturityTarget = $translator->translate('__maturity_none');
                switch ($recommandation['maturityTarget']) {
                    case 1:
                        $maturityTarget = $translator->translate('__maturity_plan');
                        break;
                    case 2:
                        $maturityTarget = $translator->translate('__maturity_medium');
                        break;
                    case 3:
                        $maturityTarget = $translator->translate('__maturity_ok');
                        break;
                }
                $this->setValue('RECOMM_TARG_MAT#' . $i, $maturityTarget);
                $i++;
            }
        }

        $j = 1;
        foreach ($categories as $categoryId => $category) {

            $nbCategoryResults = 0;
            foreach ($recommandations as $questionId => $recommandation) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $nbCategoryResults++;
                }
            }

            $this->setValue('PRISE_NOTE_CATEG_' . $j, $translator->translate($category['label']));
            $this->setValue('CATEG__PERCENT_' . $j, $category['percent'] . '%');
            $this->setValue('CATEG__PERCENT_TARG_' . $j, $category['percentTarget'] . '%');

            $this->cloneRow('PRISE_NOTE_TO_COLLECT_' . $j, $nbCategoryResults);

            $prise1 = 1;
            foreach ($recommandations as $questionId => $recommandation) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_TO_COLLECT_' . $j . '#' . $prise1;
                    $this->setValue($name, $translator->translate($questions[$questionId]->getTranslationKey()));
                    $prise1++;
                }
            }

            $prise2 = 1;
            foreach ($recommandations as $questionId => $recommandation) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_COLLECT_' . $j . '#' . $prise2;
                    $this->setValue($name, $recommandation['notes']);
                    $prise2++;
                }
            }

            $prise3 = 1;
            foreach ($recommandations as $questionId => $recommandation) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_QUEST_' . $j . '#' . $prise3;
                    $prise3++;

                    if ($questions[$questionId]->getTranslationKeyHelp()) {
                        $this->setValue($name, strip_tags($translator->translate($questions[$questionId]->getTranslationKeyHelp())));
                    }
                }
            }

            $prise4 = 1;
            foreach ($recommandations as $questionId => $recommandation) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_1_' . $j . '#' . $prise4;
                    $value = ($recommandation['maturity'] == 3) ? 'X' : '';
                    $this->setValue($name, $value);
                    $prise4++;
                }
            }

            $prise5 = 1;
            foreach ($recommandations as $questionId => $recommandation) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_2_' . $j . '#' . $prise5;
                    $value = ($recommandation['maturity'] == 2) ? 'X' : '';
                    $this->setValue($name, $value);
                    $prise5++;
                }
            }

            $prise6 = 1;
            foreach ($recommandations as $questionId => $recommandation) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_3_' . $j . '#' . $prise6;
                    $value = ($recommandation['maturity'] == 1) ? 'X' : '';
                    $this->setValue($name, $value);
                    $prise6++;
                }
            }

            $prise7 = 1;
            foreach ($recommandations as $questionId => $recommandation) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_4_' . $j . '#' . $prise7;
                    $value = ($recommandation['maturity'] == 0) ? 'X' : '';
                    $this->setValue($name, $value);
                    $prise7++;
                }
            }

            $prise8 = 1;
            foreach ($recommandations as $questionId => $recommandation) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_RECOMM_' . $j . '#' . $prise8;
                    $this->setValue($name, $recommandation['recommandation']);
                    $prise8++;
                }
            }

            $prise9 = 1;
            foreach ($recommandations as $questionId => $recommandation) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_TARG_1_' . $j . '#' . $prise9;
                    $value = ($recommandation['maturityTarget'] == 3) ? 'X' : '';
                    $this->setValue($name, $value);
                    $prise9++;
                }
            }

            $prise10 = 1;
            foreach ($recommandations as $questionId => $recommandation) {
                if ($questions[$questionId]->getCategoryId() == $categoryId) {
                    $name = 'PRISE_NOTE_TARG_2_' . $j . '#' . $prise10;
                    $value = ($recommandation['maturityTarget'] == 2) ? 'X' : '';
                    $this->setValue($name, $value);
                    $prise10++;
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

        unlink($filepath);
    }
}