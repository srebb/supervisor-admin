<?php

namespace Srebb\Bundle\SupervisorBundle\Controller;

use Srebb\Bundle\SupervisorBundle\Server\ServerContainer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="srebb_supervisor_index")
     *
     * @return Response
     */
    public function index()
    {
        /** @var ServerContainer $serverContainer */
        $serverContainer = $this->get('srebb_supervisor.server_container');

        return $this->render('@SrebbSupervisor/index.html.twig', [
            'serverStack' => $serverContainer->getServerStack()
        ]);
    }
}
