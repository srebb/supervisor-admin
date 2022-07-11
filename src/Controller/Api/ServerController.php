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
     * @Route(
     *     path="/api/server",
     *     methods={"GET"}
     * )
     */
    public function getServerList(ServerContainer $serverContainer)
    {
        return new JsonResponse($serverContainer->getServerStackAsArray());
    }

    /**
     * @Route(
     *     path="/api/server/{nameHash}/consumerlist",
     *      methods={"GET"},
     *      requirements={"nameHash"="[a-z0-9]{32}"}
     * )
     */
    public function getConsumerList(string $nameHash, ServerContainer $serverContainer)
    {
        $server = $serverContainer->getServerByNameHash($nameHash);

        return new JsonResponse($server->getAllProcessInfo());
    }

    /**
     * @Route(
     *     path="/api/server/{nameHash}/supervisorversion",
     *     methods={"GET"},
     *     requirements={"nameHash"="[a-z0-9]{32}"}
     * )
     */
    public function getSupervisorVersion(string $nameHash, ServerContainer $serverContainer)
    {
        $server = $serverContainer->getServerByNameHash($nameHash);

        try {
            return new JsonResponse($server->getSupervisorVersion());
        } catch (\Exception $e) {
            return new JsonResponse($e->getTraceAsString(), Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    /**
     * @Route(
     *     path="/api/server/{nameHash}/stopAll",
     *     methods={"POST"},
     *     requirements={"nameHash"="[a-z0-9]{32}"}
     * )
     */
    public function postStopAll(string $nameHash, ServerContainer $serverContainer)
    {
        $server = $serverContainer->getServerByNameHash($nameHash);

        return new JsonResponse($server->stopAllProcesses());
    }

    /**
     * @Route(
     *     path="/api/server/{nameHash}/startAll",
     *     methods={"POST"},
     *     requirements={"nameHash"="[a-z0-9]{32}"}
     * )
     */
    public function postStartAll(string $nameHash, ServerContainer $serverContainer)
    {
        $server = $serverContainer->getServerByNameHash($nameHash);

        return new JsonResponse($server->startAllProcesses());
    }

    /**
     * @Route(
     *     path="/api/server/{nameHash}/restartAll",
     *     methods={"POST"},
     *     requirements={"nameHash"="[a-z0-9]{32}"}
     * )
     */
    public function postRestartAll(string $nameHash, ServerContainer $serverContainer)
    {
        $server = $serverContainer->getServerByNameHash($nameHash);

        $server->stopAllProcesses();
        return new JsonResponse($server->startAllProcesses());
    }

    /**
     * @Route(
     *     path="/api/server/{nameHash}/stop/{consumerName}",
     *     methods={"POST"},
     *     requirements={"nameHash"="[a-z0-9]{32}"}
     * )
     */
    public function postStop(string $nameHash, string $consumerName, ServerContainer $serverContainer)
    {
        $server = $serverContainer->getServerByNameHash($nameHash);

        return new JsonResponse($server->stopProcess($consumerName));
    }

    /**
     * @Route(
     *     path="/api/server/{nameHash}/start/{consumerName}",
     *     methods={"POST"},
     *     requirements={"nameHash"="[a-z0-9]{32}"}
     * )
     */
    public function postStart(string $nameHash, string $consumerName, ServerContainer $serverContainer)
    {
        $server = $serverContainer->getServerByNameHash($nameHash);

        return new JsonResponse($server->startProcess($consumerName));
    }

    /**
     * @Route(
     *     path="/api/server/{nameHash}/restart/{consumerName}",
     *     methods={"POST"},
     *     requirements={"nameHash"="[a-z0-9]{32}"}
     * )
     */
    public function postRestart(string $nameHash, string $consumerName, ServerContainer $serverContainer)
    {
        $server = $serverContainer->getServerByNameHash($nameHash);

        $server->stopProcess($consumerName);
        return new JsonResponse($server->startProcess($consumerName));
    }

    /**
     * @Route(
     *     path="/api/server/{nameHash}/getLog/{consumerName}",
     *     methods={"GET"},
     *     requirements={"nameHash"="[a-z0-9]{32}"}
     * )
     */
    public function getConsumerLog(string $nameHash, string $consumerName, ServerContainer $serverContainer)
    {
        $server = $serverContainer->getServerByNameHash($nameHash);

        try {
            $log = $server->getConsumerLog($consumerName);
        } catch (\Exception $e) {
            return new JsonResponse(['errorCode' => 404], 404);
        }
        return new JsonResponse($log);
    }

    /**
     * @Route(
     *     path="/api/server/{nameHash}/getErrorLog/{consumerName}",
     *     methods={"GET"},
     *     requirements={"nameHash"="[a-z0-9]{32}"}
     * )
     */
    public function getConsumerErrorLog(string $nameHash, string $consumerName, ServerContainer $serverContainer)
    {
        $server = $serverContainer->getServerByNameHash($nameHash);

        try {
            $log = $server->getConsumerErrorLog($consumerName);
        } catch (\Exception $e) {
            return new JsonResponse(['errorCode' => 404], 404);
        }
        return new JsonResponse($log);
    }

    /**
     * @Route(
     *     path="/api/server/{nameHash}/loginfo",
     *     methods={"GET"},
     *     requirements={"nameHash"="[a-z0-9]{32}"}
     * )
     */
    public function getLogInfo(string $nameHash, ServerContainer $serverContainer)
    {
        $server = $serverContainer->getServerByNameHash($nameHash);

        return new JsonResponse($server->getAllProcessLogInfo());
    }
}
