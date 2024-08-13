<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Form\ContactType;
use Psr\Log\LoggerInterface;
use Symfony\Component\String\UnicodeString;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Email; // Add this line to import the Email class
use Symfony\Component\Mailer\Exception\TransportException; // Add this line to import the TransportException clas

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer, LoggerInterface $logger): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $adresse = $data['email'];
            $contenu = $data['Content'];
            $template = $data['Template'];
            // create a new email
            $token='coucou';
            
            $postData = [
                'email' => $adresse,
                'content' => $contenu,
                'template' => $template
            ];

            
            // Encodez le tableau en JSON
            $jsonData = json_encode($postData);
           
        $logger->info('EXEC CURL');
        $ch = curl_init();
        
        //define header
        $headers = array(
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer $token",
        );
        //define data
        $data = <<<DATA
            {
            "email": "$adresse",
            "content": "$contenu",
            "template": "$template"
            }
        DATA;
        
        //define options
        $options = array(
            CURLOPT_URL => 'http://localhost:8000/ghostgc',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $data,
        );
        


        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        curl_close($ch);
        
        
        try{
        
            // Check if initialization had gone wrong*    
            if ($ch === false) {
                throw new Exception('failed to initialize');
            }
        
            // Better to explicitly set URL
            
            // $response = curl_exec($ch);
        
            // Check the return value of curl_exec(), too
            if ($response === false) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
        
            // Check HTTP return code, too; might be something else than 200
            $httpReturnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
            if ($httpReturnCode !== 200) {
                throw new Exception('HTTP response code was ' . $httpReturnCode);
            }
        
        }catch(Exception $e) {
        
            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);
        
        } finally {
            // Close curl handle unless it failed to initialize
            if (is_resource($ch)) {
                curl_close($ch);
            }
        }
    
        //dd de l'header de la réponse
        
        // $logger->info('TEST');
        // $test = json_decode($response, true);

        // $logger->info($test);

        
        // $logger->info('I just got the logger');
        // $logger->info($test);
        //     // dd($test=$response); 
        //     $logger->info('I just got the logger');
        // $logger->info($test);
        
       
            if(true){
            dd($response);
            }
            
            
        }

        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'formulaire' => $form
        ]);
    }
}
