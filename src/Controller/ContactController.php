<?php

namespace App\Controller;

use App\Form\ContactType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Email; // Add this line to import the Email class
use Symfony\Component\Mailer\Exception\TransportException; // Add this line to import the TransportException class


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

            $email = (new Email())
                ->from($adresse)
                ->to('you@example.com')
                ->subject('Contact')
                ->text($contenu);
                // debug to see if email got send with getOriginalmessage

                
                try {
                    $mailer->send($email);
                    $logger->info('Mail envoyé');
                    dd($email);
                } catch (TransportExceptionInterface $e) {
                    $logger->error('Erreur lors de l\'envoi du mail : '.$e->getMessage());


                }
        }

        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'formulaire' => $form
        ]);
    }
}
