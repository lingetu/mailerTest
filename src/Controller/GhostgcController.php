<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

class GhostgcController extends AbstractController
{

    
    #[Route('/ghostgc', name: 'app_ghostgc')]
    public function index(LoggerInterface $logger): Response
    {
        
        $request = Request::createFromGlobals();

        // REQUEST FROM FRONT

            // $logger->info('GC SUCCES');
            // return new Response(
            //     json_encode(['message' => 'GC SUCCES']),
            //     Response::HTTP_OK,
            //     ['content-type' => 'application/json']
            // );

            //stocker data from request

            $data = json_decode($request->getContent(), true);

        // Extraire les champs spécifiques

        $email = $data['email'] ?? null;
        $template = $data['template'] ?? null;
        $content = $data['content'] ?? null;     
        // Logger les données récupérées

        $data = <<<DATA
            {
            "email": "$email",
            "content": "$content",
            "template": "$template"
            }
        DATA;

        if  ($request)
        {
            $authorizationHeader = $request->headers->get('Authorization');
            $id = $request->headers->get('id');
            if($authorizationHeader && $id)
            {
               //CURL GET FOR AUTH TO SERVICE
               $ch = curl_init();

               $headers = array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: $authorizationHeader",
                "id: $id",
            );
        
              
              //define OPTIONS  
               $options = array(
                CURLOPT_URL => 'http://localhost:8003/ghostservices',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $headers,

            );




                curl_setopt_array($ch, $options);
                $response = curl_exec($ch);
                curl_close($ch);


                $httpReturnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
                if ($httpReturnCode !== 200) {
                    return new Response(
                        'GC CORE TO SERVICES FAILED',
                         Response::HTTP_UNAUTHORIZED,
                         ['content-type' => 'text/html']
                     );
                }
                
                //get header from response
                $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $scope = substr($response, 0, $header_size);



                if($httpReturnCode === 200 && $response)
                {

                    $cu= curl_init();

                    

                    $headers = array(
                        "Accept: application/json",
                        "Content-Type: application/json",
                        "Authorization: $scope",
                    );
        
              
                    //define OPTIONS  
                    $options = array(
                        CURLOPT_URL => 'http://localhost:8003/ghostservices',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST => true,
                        CURLOPT_HTTPHEADER => $headers,
                        CURLOPT_POSTFIELDS => $data,
                        // CURLOPT_POSTFIELDS => $data,
                    );

                    $logger->info('EXEC CURL POST MAIL SERVICES');
                    curl_setopt_array($cu, $options);

                    $response = curl_exec($cu);
                    curl_close($cu);
                    
                    $httpReturnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if ($httpReturnCode !== 200) {
                        return new Response(
                            'GC CORE TO MAIL FAILED',
                             Response::HTTP_UNAUTHORIZED,
                             ['content-type' => 'text/html']
                         );
                    }
                    else{
                        return new Response(
                            'GC CORE TO MAIL success',
                             Response::HTTP_OK,
                             ['content-type' => 'text/html']
                         );
                        }
                }else{
                    return new Response(
                        'GC CORE TO MAIL FAILED',
                         Response::HTTP_UNAUTHORIZED,
                         ['content-type' => 'text/html']
                     );
                }
               
            }
            else
            {
                return new Response(
                    'INVALIDE TOKEN',
                     Response::HTTP_UNAUTHORIZED,
                     ['content-type' => 'text/html']
                 );
            }
            
        }else
        {
            return new Response(
                'REQUETE VIDE',
                 Response::HTTP_UNAUTHORIZED,
                 ['content-type' => 'text/html']
             );
        }
    }
}
