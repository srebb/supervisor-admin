<?php

namespace Srebb\Bundle\SupervisorBundle\Controller\Api;

use Srebb\Bundle\SupervisorBundle\Server\ServerContainer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ServerController extends AbstractController
{
    /**
     * @Route("/api/server/{nameHash}/consumerList", methods={"GET"}, requirements={"nameHash"="[a-z0-9]{32}"})
     */
    public function getConsumerList(string $nameHash)
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');

        $server = $serverContainer->getServerByNameHash($nameHash);

        return new JsonResponse($server->getAllProcessInfo());
    }

    /**
     * @Route("/api/server/{nameHash}/stopAll", methods={"POST"}, requirements={"nameHash"="[a-z0-9]{32}"})
     */
    public function postStopAll(string $nameHash)
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');

        $server = $serverContainer->getServerByNameHash($nameHash);

        return new JsonResponse($server->stopAllProcesses());
    }

    /**
     * @Route("/api/server/{nameHash}/startAll", methods={"POST"}, requirements={"nameHash"="[a-z0-9]{32}"})
     */
    public function postSartAll(string $nameHash)
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');

        $server = $serverContainer->getServerByNameHash($nameHash);

        return new JsonResponse($server->startAllProcesses());
    }
}
