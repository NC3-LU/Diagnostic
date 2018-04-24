<?php
namespace Diagnostic\Service;

use Zend\Session\Container;
/**
 * CalculService
 *
 * @package Diagnostic\Service
 * @author Jerome De Almeida <jerome.dealmeida@vesperiagroup.com>
 */
class CalculService extends AbstractService
{

    protected $questionService;

    public function calcul()
    {
        //retrieve questions
        $questionService = $this->get('questionService');
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
		// If the maturity equal to 3, so N/A, it isn't count in the score
		if ($results[$questionId]['maturity'] == 3) {
			$points = 0;
			$threshold = 0;
		}
                    $pointsTarget = $results[$questionId]['maturityTarget'] * $threshold;
		    // Display red points like in Monarc instead of triangles
		    if ($results[$questionId]['gravity'] == 1) $temp = '●';
 		    elseif ($results[$questionId]['gravity'] == 2) $temp = '●●';
		    else $temp = '●●●';
                    $recommandations[$question->getId()] = [
                        'recommandation' => $results[$questionId]['recommandation'],
                        'threshold' => $threshold,
                        'domaine' => $question->getCategoryTranslationKey(),
                        'gravity' => $results[$questionId]['gravity'],
			'gravity-img' => $temp, // Display red points
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

	// Divided by 2 to make the score 0/1, 0.5/1, 1/1, instead of 3 for 0/1, 0.33/1, 0.66/1, 1/1
        $total = ($totalThreshold) ? round($totalPoints / $totalThreshold * 100 / 2) : 0;
        $totalTarget = ($totalThreshold) ? round($totalPointsTarget / $totalThreshold * 100 / 2) : 0;

        $totalCategory = [];
        foreach ($globalPoints as $categoryId => $points) {
            $totalCategory[$categoryId] = ($globalThreshold[$categoryId]) ? round($points / $globalThreshold[$categoryId] * 100 / 2) : 0;
        }

        $totalCategoryTarget = [];
        foreach ($globalPointsTarget as $categoryId => $pointsTarget) {
            $totalCategoryTarget[$categoryId] = ($globalThreshold[$categoryId]) ? round($pointsTarget / $globalThreshold[$categoryId] * 100 / 2) : 0;
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
	// 2 = 100%, 1 = 50%, 0 = 0%, 3 = N/A
        switch ($maturity) {
            case 2:
                $img = '/img/mat_ok.png';
                break;
            case 1:
                $img = '/img/mat_moyen.png';
                break;
            case 3:
                $img = '/img/mat_plan.png';
                break;
            case 0:
                $img = '/img/mat_none.png';
                break;
        }

        return $img;
    }
}
