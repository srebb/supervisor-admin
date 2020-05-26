<?php

namespace Srebb\Bundle\SupervisorBundle\Server;

use fXmlRpc\Transport\HttpAdapterTransport;
use Http\Message\MessageFactory\DiactorosMessageFactory;
use Http\Adapter\Guzzle6\Client as Guzzle6Client;
use Supervisor\Connector\XmlRpc;
use Supervisor\Supervisor;
use fXmlRpc\Client;
use GuzzleHttp\Client as GuzzleClient;

class Server
{
    /**
     * @var string
     */
    private $serverName;
    /**
     * @var string
     */
    private $nameHash;
    /**
     * @var string
     */
    private $host;

    /**
     * @var Supervisor
     */
    private $supervisor;

    public function __construct(string $serverName, array $serverData)
    {
        $this->serverName = $serverName;
        $this->nameHash   = md5($serverName);
        $this->host       = $serverData['host'];

        $this->supervisor = $this->createSupervisor();
    }

    private function createSupervisor()
    {
        $guzzleClient = new GuzzleClient();

        $client = new Client(
            sprintf('http://%s:9001/RPC2', $this->host),
            new HttpAdapterTransport(
                new DiactorosMessageFactory(),
                new Guzzle6Client($guzzleClient)
            )
        );

        $connector = new XmlRpc($client);

        return new Supervisor($connector);
    }

    /**
     * @return string
     */
    public function getServerName(): string
    {
        return $this->serverName;
    }

    /**
     * @return string
     */
    public function getNameHash(): string
    {
        return $this->nameHash;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    public function getAllProcessInfo()
    {
        return $this->supervisor->getAllProcessInfo();
    }

    public function stopAllProcesses()
    {
        return $this->supervisor->stopAllProcesses();
    }

    public function startAllProcesses()
    {
        return $this->supervisor->startAllProcesses();
    }
}
