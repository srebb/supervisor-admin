<?php

namespace Srebb\Bundle\SupervisorBundle\Controller\Api;

use Srebb\Bundle\SupervisorBundle\Server\ServerContainer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServerController extends AbstractController
{
    /**
     * @Route("/api/server", methods={"GET"})
     */
    public function getServerList()
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');

        return new JsonResponse($serverContainer->getServerStackAsArray());
    }

    /**
     * @Route("/api/server/{nameHash}/consumerlist", methods={"GET"}, requirements={"nameHash"="[a-z0-9]{32}"})
     */
    public function getConsumerList(string $nameHash)
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');

        $server = $serverContainer->getServerByNameHash($nameHash);

        return new JsonResponse($server->getAllProcessInfo());
    }

    /**
     * @Route("/api/server/{nameHash}/supervisorversion", methods={"GET"}, requirements={"nameHash"="[a-z0-9]{32}"})
     */
    public function getSupervisorVersion(string $nameHash)
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');
        $server          = $serverContainer->getServerByNameHash($nameHash);

        try {
            return new JsonResponse($server->getSupervisorVersion());
        } catch (\Exception $e) {
            return new JsonResponse($e->getTraceAsString(), Response::HTTP_SERVICE_UNAVAILABLE);
        }
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
    public function postStartAll(string $nameHash)
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');

        $server = $serverContainer->getServerByNameHash($nameHash);

        return new JsonResponse($server->startAllProcesses());
    }

    /**
     * @Route("/api/server/{nameHash}/restartAll", methods={"POST"}, requirements={"nameHash"="[a-z0-9]{32}"})
     */
    public function postRestartAll(string $nameHash)
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');

        $server = $serverContainer->getServerByNameHash($nameHash);

        $server->stopAllProcesses();
        return new JsonResponse($server->startAllProcesses());
    }

    /**
     * @Route("/api/server/{nameHash}/stop/{consumerName}", methods={"POST"}, requirements={"nameHash"="[a-z0-9]{32}"})
     */
    public function postStop(string $nameHash, string $consumerName)
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');

        $server = $serverContainer->getServerByNameHash($nameHash);

        return new JsonResponse($server->stopProcess($consumerName));
    }

    /**
     * @Route("/api/server/{nameHash}/start/{consumerName}", methods={"POST"}, requirements={"nameHash"="[a-z0-9]{32}"})
     */
    public function postStart(string $nameHash, string $consumerName)
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');

        $server = $serverContainer->getServerByNameHash($nameHash);

        return new JsonResponse($server->startProcess($consumerName));
    }

    /**
     * @Route("/api/server/{nameHash}/restart/{consumerName}", methods={"POST"}, requirements={"nameHash"="[a-z0-9]{32}"})
     */
    public function postRestart(string $nameHash, string $consumerName)
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');

        $server = $serverContainer->getServerByNameHash($nameHash);

        $server->stopProcess($consumerName);
        return new JsonResponse($server->startProcess($consumerName));
    }

    /**
     * @Route("/api/server/{nameHash}/getLog/{consumerName}", methods={"GET"}, requirements={"nameHash"="[a-z0-9]{32}"})
     */
    public function getConsumerLog(string $nameHash, string $consumerName)
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');

        $server = $serverContainer->getServerByNameHash($nameHash);

        try {
            $log = $server->getConsumerLog($consumerName);
        } catch (\Exception $e) {
            return new JsonResponse(['errorCode' => 404], 404);
        }
        return new JsonResponse($log);
    }

    /**
     * @Route("/api/server/{nameHash}/getErrorLog/{consumerName}", methods={"GET"}, requirements={"nameHash"="[a-z0-9]{32}"})
     */
    public function getConsumerErrorLog(string $nameHash, string $consumerName)
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');

        $server = $serverContainer->getServerByNameHash($nameHash);

        try {
            $log = $server->getConsumerErrorLog($consumerName);
        } catch (\Exception $e) {
            return new JsonResponse(['errorCode' => 404], 404);
        }
        return new JsonResponse($log);
    }



    /**
     * @Route("/api/server/{nameHash}/loginfo", methods={"GET"}, requirements={"nameHash"="[a-z0-9]{32}"})
     */
    public function getLogInfo(string $nameHash)
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');

        $server = $serverContainer->getServerByNameHash($nameHash);

        return new JsonResponse($server->getAllProcessLogInfo());
    }
}
