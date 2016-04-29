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

    public function calcul() {

        //retrieve questions
        $questionService = $this->getServiceLocator()->get('Diagnostic\Service\QuestionService');
        $questions = $questionService->getQuestions();

        //retrieve results and questions
        $container = new Container('diagnostic');
        $results = ($container->offsetExists('result')) ? $container->result : [];

        $totalPoints = 0;
        $totalThreshold = 0;
        $globalPoints = [];
        $globalThreshold = [];
        $recommandations = [];
        foreach ($results as $key => $result) {
            $categoryId = $questions[$key]->getCategoryId();
            $points = $result['maturity'] * $questions[$key]->getThreshold();
            $threshold = $questions[$key]->getThreshold();

            $recommandations[$questions[$key]->getId()] = [
                'recommandation' => $result['recommandation'],
                'threshold' => $questions[$key]->getThreshold(),
            ];

            $totalPoints += $points;
            $totalThreshold += $threshold;

            $globalPoints[$categoryId] = array_key_exists($categoryId, $globalPoints) ? $globalPoints[$categoryId] + $points : $points;
            $globalThreshold[$categoryId] = array_key_exists($categoryId, $globalThreshold) ? $globalThreshold[$categoryId] + $threshold : $threshold;
        }

        $total = ($totalThreshold) ? round($totalPoints / $totalThreshold * 100 / 3) : 0;
        $totalCategory = [];
        foreach($globalPoints as $categoryId => $points) {
            $totalCategory[$categoryId] = round($points / $globalThreshold[$categoryId] * 100 / 3);
        }

        //order recommandation by threshold
        $tmpArray = [];
        foreach($recommandations as $questionId => $recommandation) {
            $tmpArray[$questionId] = $recommandation['threshold'];
        }
        asort($tmpArray);
        $recommandationsSort = [];
        foreach($tmpArray as $questionId => $value) {
            $recommandationsSort[$questionId] = $recommandations[$questionId]['recommandation'];
        }

        return [
            'total' => $total,
            'totalCategory' => $totalCategory,
            'recommandations' => $recommandationsSort,
        ];
    }
}