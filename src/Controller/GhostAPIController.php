<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

class GhostAPIController extends AbstractController
{
    #[Route('/ghostapi', name: 'app_ghost_a_p_i',methods: ['POST', 'GET'])]
    public function getFantomeData(LoggerInterface $logger): Response
    {
        
        $request = Request::createFromGlobals();

        $logger->info($request);
        $data=json_decode($request->getContent(),true);
        $logger->info('Decoded data: ' . print_r($data, true));
        return new Response(
            json_encode(['message' => 'Données fantome récupérées avec succès']),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );

        if  ($request)
        {
            $authorizationHeader = $request->headers->get('Authorization');
            if($authorizationHeader==='Bearer coucou')
            {
               $logger->info('Token valide');
               return new Response(
                json_encode(['message' => 'Données fantome récupérées avec succès']),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
            }
            else
            {
                $logger->info('Token invalide');
                return new Response(
                    json_encode(['message' => 'Non autorisé']),
                    Response::HTTP_UNAUTHORIZED,
                    ['content-type' => 'application/json']
                );
            }
            
        }
        

        
        // Vérifie si le token est valide
        
           
        // } else {
        //     return new Response(
        //         json_encode(['message' => 'Non autorisé']),
        //         Response::HTTP_UNAUTHORIZED,
        //         ['content-type' => 'application/json']
        //     );
        // }
       
}
}