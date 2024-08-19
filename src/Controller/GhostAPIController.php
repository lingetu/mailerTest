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


class GhostAPIController extends AbstractController
{
    #[Route('/ghostmailer', name: 'app_ghost_a_p_i',methods: ['POST', 'GET'])]
    public function getFantomeData(LoggerInterface $logger,MailerInterface $mailer): Response
    {
        
        $request = Request::createFromGlobals();

        
        $logger->info('REQUEST IN SERVICES');
        $logger->info($request);


      

        if  ($request)
        {

            $data = json_decode($request->getContent(), true);

            $email = $data['email'] ?? null;
            $template = $data['template'] ?? null;
            $content = $data['content'] ?? null;

            $logger->info('Email: ' . $email);
            $logger->info('Template: ' . $template);
            $logger->info('Content: ' . $content);

            $authorizationHeader = $request->headers->get('Authorization');
            if($authorizationHeader==='COUCOU2' && $template && $content && $email)
            {
               $logger->info('Token valide');

               

               if ($template == 'emails/signup.html.twig') {
                $logger->info('Template signup');
                $email = (new TemplatedEmail())
                ->from($email)
                ->to('you@example.com')
                ->subject('Contact')
                ->text($content)
                ->htmlTemplate('emails/signup.html.twig')
                ->locale('de')
                ->context([
                    'expiration_date' => new \DateTime('+7 days'),
                    'client_name' => 'foo',
                ]);
            } elseif ($template == 'emails/rappel.html.twig') {
                $email = (new TemplatedEmail())
                ->from($email)
                ->to('you@example.com')
                ->subject('Contact')
                ->text($content)
                ->htmlTemplate('emails/rappel.html.twig')
                ->locale('de')
                ->context([
                    'expiration_date' => new \DateTime('+7 days'),
                    'client_name' => 'foo',
                    'montant_du' => 'montant_du',
                    'date_echeance' => 'date_echeance',
                    'iban'  => 'iban',
                    'bic' => 'bic',
                    'email$email_postale' => 'email$email_postale',
                ]);
            } elseif ($template == 'emails/compterendu.html.twig') {
                $email = (new TemplatedEmail())
                ->from($email)
                ->to('you@exemple.com')
                ->subject('Contact')
                ->text($content)
                ->htmlTemplate('emails/compterendu.html.twig')
                ->locale('de')
                ->context([
                    'expiration_date' => new \DateTime('+7 days'),
                    'client_name' => 'foo',
                    'satisfaction_rate' => 'satisfaction_rate',
                    'reservation_count' => 'reservation_count',
                ]);
            } elseif ($template == 'emails/marketing.html.twig') {
                $email = (new TemplatedEmail())
                ->from($email)
                ->to('you@exemple.com')
                ->subject('Contact')
                ->text($content)
                ->htmlTemplate('emails/marketing.html.twig')
                ->locale('de')
                ->context([
                    'expiration_date' => new \DateTime('+7 days'),
                ]);
            }


                
                try {
                    $mailer->send($email);
                    $logger->info('Mail envoyé');

                    return new Response(
                        'ENVOIE MAIL FAIT',
                        Response::HTTP_OK,
                        ['content-type' => 'text/html']
                    );

                } catch (TransportExceptionInterface $e) {
                    $logger->error('Erreur lors de l\'envoi du mail : '.$e->getMessage());

                    return new Response(
                        'ENVOIE MAIL ECHOUE',
                        Response::HTTP_OK,
                        ['content-type' => 'text/html']
                    );
                }
            }
            else
            {
                return new Response(
                    'CHAMP MANQUANT',
                    Response::HTTP_UNAUTHORIZED,
                    ['content-type' => 'text/html']
                );
            }
            
        }else
        {
            $logger->info('REQUEST VIDE');
            return new Response(
                json_encode(['message' => 'REQUEST VIDE']),
                Response::HTTP_UNAUTHORIZED,
                ['content-type' => 'application/json']
            );
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