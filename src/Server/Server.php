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

    public function __construct(string $serverName, array $serverData)
    {
        $this->serverName = $serverName;
        $this->nameHash   = md5($serverName);
        $this->host       = $serverData['host'];
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
        $guzzleClient = new GuzzleClient();

        $client = new Client(
            sprintf('http://%s:9001/RPC2', $this->host),
            new HttpAdapterTransport(
                new DiactorosMessageFactory(),
                new Guzzle6Client($guzzleClient)
            )
        );

        $connector = new XmlRpc($client);

        $supervisor = new Supervisor($connector);

        return $supervisor->getAllProcessInfo();
    }
}
