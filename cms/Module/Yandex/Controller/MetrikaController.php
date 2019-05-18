<?php

declare(strict_types=1);

namespace Monolith\Module\Yandex\Controller;

use Monolith\Module\Yandex\Entity\MetrikaCounter;
use Monolith\Module\Yandex\Entity\OauthToken;
use Smart\CoreBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yandex\Metrica\Management\ManagementClient;
use Yandex\Metrica\Stat\MetricConst;
use Yandex\Metrica\Stat\Models\ByTimeParams;
use Yandex\Metrica\Stat\StatClient;

class MetrikaController extends Controller
{
    public function indexAction(Request $request, MetrikaCounter $counter): Response
    {
        $counterId = $counter->getCounterId();
        $token = $counter->getToken()->getToken();

        /*
         * Управление
         *
        $managementClient = new ManagementClient($token);

        $params = new \Yandex\Metrica\Management\Models\CounterParams();
        $params->setField('goals,mirrors,grants,filters,operations');
        $result = $managementClient->counters()->getCounter($counterId, $params);

        dump($result);
        */

        $statClient = new StatClient($token);

        $paramsModel = new ByTimeParams();
        $paramsModel
            ->setMetrics(MetricConst::S_HITS)
            ->setId($counterId)
            ->setDate1('14daysAgo')
            ->setDate2('today')
            ->setGroup('day');
        $data = $statClient->data()->getByTime($paramsModel);

        return $this->render('@YandexModule/Metrika/index.html.twig', [
            'data' => $data,
        ]);
    }
}
