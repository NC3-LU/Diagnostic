<?php
namespace Diagnostic\Service;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Writer\Word2007;
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
     * Get
     *
     * @param $value
     * @return mixed
     */
    public function get($value) {
        return $this->$value;
    }

    /**
     * Set
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value) {
        $this->$key = $value;
        return $this;
    }

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




	 // ContentMat : 0 = 0/1, 1 = 0.5/1, 2 = 1/1, 3 = NA
         //css Tables
         $styleHeaderCell = ['valign' => 'center', 'bgcolor' => 'DFDFDF', 'size' => 10];
         $styleHeaderCellBlack = ['valign' => 'center', 'bgcolor' => '444444', 'size' => 10];
         $styleContentFontBold = ['bold' => true, 'size' => 10, 'name' => 'Century Schoolbook'];
         $styleContentFontBoldWhite = ['bold' => true, 'size' => 10, 'color' => 'FFFFFF', 'name' => 'Century Schoolbook'];
         $styleContentFontMat0 = ['bold' => true, 'size' => 16, 'color' => 'FD661F', 'name' => 'Wingdings 2'];
         $styleContentFontMat3 = ['bold' => true, 'size' => 11, 'name' => 'Century Schoolbook'];
         $styleContentFontMat1 = ['bold' => true, 'size' => 16, 'color' => 'FFBC1C', 'name' => 'Century Schoolbook'];
         $styleContentFontMat2 = ['bold' => true, 'size' => 16, 'color' => 'D6F107', 'name' => 'Wingdings 2'];
         $styleContentCell = ['align' => 'left', 'valign' => 'center', 'size' => 10];
         $styleContentCellMat0 = ['align' => 'left', 'valign' => 'center', 'size' => 10];
         $styleContentCellMat3 = ['align' => 'left', 'valign' => 'center', 'size' => 10];
         $styleContentCellMat1 = ['align' => 'left', 'valign' => 'center', 'size' => 10];
         $styleContentCellMat2 = ['align' => 'left', 'valign' => 'center', 'size' => 10];
         $styleContentCellMatTarget1 = ['align' => 'left', 'valign' => 'center', 'size' => 10];
         $styleContentCellMatTarget2 = ['align' => 'left', 'valign' => 'center', 'size' => 10];
         $bgcolorMat0 = 'FD661F';
         $bgcolorMat3 = 'DFDFDF';
         $bgcolorMat1 = 'FFBC1C';
         $bgcolorMat2 = 'D6F107';
         $styleContentFontGravity = ['bold' => true, 'color' => 'FF0000', 'size' => 12];
         $alignCenter = ['Alignment' => 'center', 'spaceAfter' => '0'];
         $alignLeft = ['Alignment' => 'left', 'spaceAfter' => '0'];
         $styleContentFont = ['bold' => false, 'size' => 10, 'name' => 'Century Schoolbook'];
         $cellRowSpan = ['vMerge' => 'restart', 'valign' => 'center', 'bgcolor' => 'DFDFDF', 'align' => 'center', 'Alignment' => 'center'];
         $cellRowContinue = ['vMerge' => 'continue','valign' => 'center', 'bgcolor' => 'DFDFDF'];
         $cellRowSpanBlack = ['vMerge' => 'restart', 'valign' => 'center', 'bgcolor' => '444444', 'align' => 'center', 'Alignment' => 'center'];
         $cellRowContinueBlack = ['vMerge' => 'continue','valign' => 'center', 'bgcolor' => '444444'];
         $cellColSpan9 = ['gridSpan' => 9, 'bgcolor' => 'DFDFDF', 'size' => 10, 'valign' => 'center', 'align' => 'center', 'Alignment' => 'center'];
         $cellColSpan4Black = ['gridSpan' => 4, 'bgcolor' => '444444', 'size' => 10, 'valign' => 'center', 'align' => 'center', 'Alignment' => 'center'];
         $cellColSpan2Black = ['gridSpan' => 2, 'bgcolor' => '444444', 'size' => 10, 'valign' => 'center', 'align' => 'center', 'Alignment' => 'center'];




         //create RECOMMENDATION_TABLE section
         $tableWord = new PhpWord();
         $section = $tableWord->addSection();
         $table = $section->addTable(['borderSize' => 1, 'borderColor' => 'ABABAB']);

         //header if array is not empty
         if (count($recommandations)) {
             $table->addRow(400, ['tblHeader' => true]);
             $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.00), $styleHeaderCell)->addText('Nr', $styleContentFontBold, $alignCenter);
             $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(10.00), $styleHeaderCell)->addText($translator->translate('__recommandation'), $styleContentFontBold, $alignCenter);
             $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(5.50), $styleHeaderCell)->addText($translator->translate('__domain'), $styleContentFontBold, $alignCenter);
             $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.50), $styleHeaderCell)->addText($translator->translate('__gravity'), $styleContentFontBold, $alignCenter);
             $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(2.20), $styleHeaderCell)->addText($translator->translate('__current_maturity'), $styleContentFontBold, $alignCenter);
             $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(2.20), $styleHeaderCell)->addText($translator->translate('__maturity_target'), $styleContentFontBold, $alignCenter);

         }

         $recommandations = $results['recommandations'];

         $i = 1;
         foreach ($recommandations as $recommandation => $value) {
          if ($value['recommandation']) {

          $category = $translator->translate($categories[$questions[$recommandation]->getCategoryId()]['label']);

           $gravity = '';
           for ($k = 0; $k <= ($value['gravity'] - 1); $k++) {
               $gravity .= '●';
            }

          $maturity = $translator->translate('__maturity_none');
          $styleContentCellMaturity = ['align' => 'left', 'bgcolor' => 'FD661F', 'valign' => 'center', 'size' => 10];
	  // 2 = 100%, 1 = 50%, 3 = non applicable en maturité
          switch ($value['maturity']) {
              case 3:
                  $maturity = $translator->translate('__maturity_plan');
                  $styleContentCellMaturity = ['align' => 'left', 'bgcolor' => 'E7E6E6','valign' => 'center', 'size' => 10];
                  break;
              case 1:
                  $maturity = $translator->translate('__maturity_medium');
                  $styleContentCellMaturity = ['align' => 'left', 'bgcolor' => 'FFBC1C', 'valign' => 'center', 'size' => 10];
                  break;
              case 2:
                  $maturity = $translator->translate('__maturity_ok');
                  $styleContentCellMaturity = ['align' => 'left', 'bgcolor' => 'D6F107', 'valign' => 'center', 'size' => 10];
                  break;
          }

          $maturityTarget = $translator->translate('__maturity_none');
          $styleContentCellMaturityTarget = ['align' => 'left', 'bgcolor' => 'FD661F', 'valign' => 'center', 'size' => 10];
          switch ($value['maturityTarget']) {
              case 3:
                  $maturityTarget = $translator->translate('__maturity_plan');
                  $styleContentCellMaturityTarget = ['align' => 'left', 'bgcolor' => 'E7E6E6', 'valign' => 'center', 'size' => 10];

                  break;
              case 1:
                  $maturityTarget = $translator->translate('__maturity_medium');
                  $styleContentCellMaturityTarget = ['align' => 'left', 'bgcolor' => 'FFBC1C', 'valign' => 'center', 'size' => 10];

                  break;
              case 2:
                  $maturityTarget = $translator->translate('__maturity_ok');
                  $styleContentCellMaturityTarget = ['align' => 'left', 'bgcolor' => 'D6F107', 'valign' => 'center', 'size' => 10];
                  break;
          }

          $table->addRow(400);
          $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.00), $styleContentCell)->addText($i, $styleContentFont, $alignCenter);
          $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(10.00), $styleContentCell)->addText($value['recommandation'], $styleContentFont, $alignLeft);
          $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(5.50), $styleContentCell)->addText($category, $styleContentFont, $alignLeft);
          $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.50), $styleContentCell)->addText($gravity, $styleContentFontGravity, $alignCenter);
          $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(2.20), $styleContentCellMaturity)->addText($maturity, $styleContentFontBold, $alignCenter);
          $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(2.20), $styleContentCellMaturityTarget)->addText($maturityTarget, $styleContentFontBold, $alignCenter);

          $i++;
          }
        }

        $this->setValue('RECOMMENDATION_TABLE', $this->getWordXmlFromWordObject($tableWord));
        unset($tableWord);


        //create NOTES_TABLE section
        $tableWord = new PhpWord();
        $section = $tableWord->addSection();
        $table = $section->addTable(['borderSize' => 1, 'borderColor' => 'ABABAB']);

        //headers
        $table->addRow(400, ['tblHeader' => true]);
        $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(7.00), $cellRowSpanBlack)->addText($translator->translate('__information_collect'), $styleContentFontBoldWhite, $alignCenter);
        $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(7.00), $cellRowSpanBlack)->addText($translator->translate('__collected_information'), $styleContentFontBoldWhite, $alignCenter);
        $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(4.00), $cellColSpan4Black)->addText($translator->translate('__current_maturity'), $styleContentFontBoldWhite, $alignCenter);
        $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(7.00), $cellRowSpanBlack)->addText($translator->translate('__recommandation'), $styleContentFontBoldWhite, $alignCenter);
        $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(2.20), $cellColSpan2Black)->addText($translator->translate('__maturity_target'), $styleContentFontBoldWhite, $alignCenter);

        $table->addRow();
        $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(7.00), $cellRowContinueBlack);
        $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(7.00), $cellRowContinueBlack);
        $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.00), $styleHeaderCellBlack)->addText('', $styleContentFontMat2, $alignCenter);
        $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.00), $styleHeaderCellBlack)->addText('±', $styleContentFontMat1, $alignCenter);
        $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.00), $styleHeaderCellBlack)->addText('', $styleContentFontMat0, $alignCenter);
        $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.00), $styleHeaderCellBlack)->addText('N/A', $styleContentFontBoldWhite, $alignCenter);
        $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(7.00), $cellRowContinueBlack);
        $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.10), $styleHeaderCellBlack)->addText('', $styleContentFontMat2, $alignCenter);
        $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.10), $styleHeaderCellBlack)->addText('±', $styleContentFontMat1, $alignCenter);

        $previousCategoryId = null;

        foreach ($categories as $categoryId => $category) {

          if ($categoryId != $previousCategoryId) {

            $categoryTest = $translator->translate($category['label']);
            $table->addRow(400);
            $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(5.00), $cellColSpan9)->addText($categoryTest, $styleContentFontBold, $alignLeft);

            foreach ($recommandations as $recommandation => $value) {
                if ($questions[$recommandation]->getCategoryId() == $categoryId) {

                  $questionCollect = $translator->translate($questions[$recommandation]->getTranslationKey());
                  $notes = $value['notes'];

                  for ($i = 0; $i <= 3 ; $i++) {
                    if ($value['maturity'] == $i) {
                      ${'styleContentCellMat' . $i} = ['valign' => 'center', 'bgcolor' => ${'bgcolorMat' . $i}, 'size' => 10];
                    }
                    if ($value['maturityTarget'] == $i) {
                      ${'styleContentCellMatTarget' . $i} = ['valign' => 'center', 'bgcolor' => ${'bgcolorMat' . $i}, 'size' => 10];

                    }
                  }

                  $table->addRow(400);
                  $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(7.00), $styleContentCell)->addText($questionCollect, $styleContentFont, $alignLeft);
                  $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(7.00), $styleContentCell)->addText($notes, $styleContentFont, $alignLeft);
                  $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.00), $styleContentCellMat2)->addText('', $styleContentFontBold, $alignCenter);
                  $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.00), $styleContentCellMat1)->addText('', $styleContentFontBold, $alignCenter);
                  $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.00), $styleContentCellMat0)->addText('', $styleContentFontBold, $alignCenter);
                  $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.00), $styleContentCellMat3)->addText('', $styleContentFontBold, $alignCenter);
                  $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(7.00), $styleContentCell)->addText($value['recommandation'], $styleContentFont, $alignLeft);
                  $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.00), $styleContentCellMatTarget2)->addText('', $styleContentFontBold, $alignCenter);
                  $table->addCell(\PhpOffice\Common\Font::centimeterSizeToTwips(1.00), $styleContentCellMatTarget1)->addText('', $styleContentFontBold, $alignCenter);

                  for ($i = 0; $i <= 3 ; $i++) {
                      ${'styleContentCellMat' . $i} = ['valign' => 'center', 'size' => 10];
                      ${'styleContentCellMatTarget' . $i} = ['valign' => 'center', 'size' => 10];
                  }
                }
            }
          }
          $previousCategoryId = $categoryId;

        }

        $this->setValue('NOTES_TABLE', $this->getWordXmlFromWordObject($tableWord));
        unset($tableWord);

        $j = 1;
        foreach ($categories as $categoryId => $category) {
	  $this->setValue('PRISE_NOTE_CATEG_' . $j, $translator->translate($category['label']));
          $this->setValue('CATEG__PERCENT_' . $j, $category['percent'] . '%');
          $this->setValue('CATEG__PERCENT_TARG_' . $j, $category['percentTarget'] . '%');
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

    protected function getWordXmlFromWordObject($phpWord, $useBody = true)
    {
        // Portion Copyright © Netlor SAS - 2015
        $part = new \PhpOffice\PhpWord\Writer\Word2007\Part\Document();
        $part->setParentWriter(new Word2007($phpWord));
        $docXml = $part->write();
        $matches = [];

        if ($useBody === true) {
            $regex = '/<w:body>(.*)<w:sectPr>/is';
        } else if ($useBody === 'graph') {
            return $docXml;
        } else {
            $regex = '/<w:r>(.*)<\/w:r>/is';
        }

        if (preg_match($regex, $docXml, $matches) === 1) {
            return $matches[1];
        } else {
            return "";
        }
    }
}
