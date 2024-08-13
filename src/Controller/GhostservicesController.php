<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

class GhostservicesController extends AbstractController
{
    #[Route('/ghostservices', name: 'app_ghostservices')]
    public function index(LoggerInterface $logger): Response
    {
        $request = Request::createFromGlobals();
      

            // return new Response(
            //     json_encode(['message' => 'GC SERVICE SUCCESS']),
            //     Response::HTTP_OK,
            //     ['content-type' => 'application/json']
            // );
        $logger->info("REQUEST IN SERVICES");
        $logger->info($request);

        if  ($request)
        {
            $authorizationHeader = $request->headers->get('Authorization');
            $logger->info($authorizationHeader);
            if($authorizationHeader==='Bearer coucou')
            {
               $logger->info('Token valide SERVICE AUTH');

               
                
               return new Response(
                'COUCOU2',
                Response::HTTP_OK,
                ['content-type' => 'text/html']
            );
            }
            else
            {
                $logger->info('Token invalide');
                return new Response(
                   'INVALIDE TOKEN',
                    Response::HTTP_UNAUTHORIZED,
                    ['content-type' => 'text/html']
                );
            }
            
        }else
        {
            $logger->info('REQUEST VIDE');
            return new Response(
                'REQUETE VIDE',
                 Response::HTTP_UNAUTHORIZED,
                 ['content-type' => 'text/html']
             );
        }
    }
}
