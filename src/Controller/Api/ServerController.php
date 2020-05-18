<?php

namespace Srebb\Bundle\SupervisorBundle\Controller\Api;

use Srebb\Bundle\SupervisorBundle\Server\ServerContainer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ServerController extends AbstractController
{
    /**
     * @Route("/api/consumerlist/{nameHash}", methods={"GET"}, requirements={"nameHash"="[a-z0-9]{32}"})
     */
    public function getConsumerList(string $nameHash)
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');

        $server = $serverContainer->getServerByNameHash($nameHash);

        return new JsonResponse($server->getAllProcessInfo());
    }
}
