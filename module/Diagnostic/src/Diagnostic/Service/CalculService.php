<?php
namespace Diagnostic\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Session\Container;

/**
 * CalculService
 *
 * @package Diagnostic\Service
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class CalculService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function calcul()
    {

        //retrieve questions
        $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
        $questions = $questionService->getQuestions();

        //retrieve results and questions
        $container = new Container('diagnostic');
        $results = ($container->offsetExists('result')) ? $container->result : [];

        $totalPoints = 0;
        $totalPointsTarget = 0;
        $totalThreshold = 0;
        $globalPoints = [];
        $globalPointsTarget = [];
        $globalThreshold = [];
        $recommandations = [];
        foreach ($questions as $questionId => $question) {

            $categoryId = $question->getCategoryId();
            $threshold = $question->getThreshold();

            if (array_key_exists($questionId, $results)) {
                if (strlen($results[$questionId]['notes'])) {
                    $points = $results[$questionId]['maturity'] * $threshold;
                    $pointsTarget = $results[$questionId]['maturityTarget'] * $threshold;
                    $recommandations[$question->getId()] = [
                        'recommandation' => $results[$questionId]['recommandation'],
                        'threshold' => $threshold,
                        'domaine' => $question->getCategoryTranslationKey(),
                        'gravity-img' => '/img/gravity_' . $results[$questionId]['gravity'] . '.png',
                        'gravity' => $results[$questionId]['gravity'],
                        'maturity' => $results[$questionId]['maturity'],
                        'maturity-img' => $this->getImgMaturity($results[$questionId]['maturity']),
                        'maturityTarget' => $results[$questionId]['maturityTarget'],
                        'maturityTarget-img' => $this->getImgMaturity($results[$questionId]['maturityTarget']),
                        'notes' => $results[$questionId]['notes']
                    ];

                    $totalPoints += $points;
                    $totalPointsTarget += $pointsTarget;
                    $globalPoints[$categoryId] = array_key_exists($categoryId, $globalPoints) ? $globalPoints[$categoryId] + $points : $points;
                    $globalPointsTarget[$categoryId] = array_key_exists($categoryId, $globalPointsTarget) ? $globalPointsTarget[$categoryId] + $pointsTarget : (int)$pointsTarget;

                    $totalThreshold += $threshold;
                    $globalThreshold[$categoryId] = array_key_exists($categoryId, $globalThreshold) ? $globalThreshold[$categoryId] + $threshold : (int)$threshold;
                }
            }
        }

        $total = ($totalThreshold) ? round($totalPoints / $totalThreshold * 100 / 3) : 0;
        $totalTarget = ($totalThreshold) ? round($totalPointsTarget / $totalThreshold * 100 / 3) : 0;

        $totalCategory = [];
        foreach ($globalPoints as $categoryId => $points) {
            $totalCategory[$categoryId] = ($globalThreshold[$categoryId]) ? round($points / $globalThreshold[$categoryId] * 100 / 3) : 0;
        }

        $totalCategoryTarget = [];
        foreach ($globalPointsTarget as $categoryId => $pointsTarget) {
            $totalCategoryTarget[$categoryId] = ($globalThreshold[$categoryId]) ? round($pointsTarget / $globalThreshold[$categoryId] * 100 / 3) : 0;
        }

        $recommandations = $this->sortArray($recommandations, 'maturityTarget');
        $recommandations = $this->sortArray($recommandations, 'maturity');
        $recommandations = $this->sortArray($recommandations, 'gravity');

        return [
            'total' => $total,
            'totalTarget' => $totalTarget,
            'totalCategory' => $totalCategory,
            'totalCategoryTarget' => $totalCategoryTarget,
            'recommandations' => $recommandations,
        ];
    }

    /**
     * Sort Array
     *
     * @param $recommandations
     * @param $field
     * @return array
     */
    public function sortArray($recommandations, $field)
    {
        $tmpArray = [];
        foreach ($recommandations as $questionId => $recommandation) {
            $tmpArray[$questionId] = $recommandation[$field];
        }
        arsort($tmpArray);

        $recommandationsSort = [];
        foreach ($tmpArray as $questionId => $value) {
            $recommandationsSort[$questionId] = $recommandations[$questionId];
        }

        return $recommandationsSort;
    }

    /**
     * Get Img Maturity
     *
     * @param $maturity
     * @return string
     */
    public function getImgMaturity($maturity)
    {

        switch ($maturity) {
            case 3:
                $img = '/img/mat_ok.png';
                break;
            case 2:
                $img = '/img/mat_moyen.png';
                break;
            case 1:
                $img = '/img/mat_plan.png';
                break;
            case 0:
                $img = '/img/mat_none.png';
                break;
        }

        return $img;
    }
}