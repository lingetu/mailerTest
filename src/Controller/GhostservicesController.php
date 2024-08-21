<?php

namespace App\Controller;

use App\Service\JwtService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
//templated email
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
// Add this line to import the Email class
use Symfony\Component\Mailer\Exception\TransportException;
// Add this line to import the TransportException clas

class GhostservicesController extends AbstractController  {

    public JwtService $jwtService;
    
    

    public function __construct( JwtService $jwtService ) {
        $this->jwtService = $jwtService;
        
    }

    #[ Route( '/ghostservices', name: 'app_ghostservices' ) ]

    public function index( LoggerInterface $logger, MailerInterface $mailer ): Response  {

        $request = Request::createFromGlobals();

       

        // $tokenCryp = $request->headers->get( 'Authorization' );

        // $logger->info( $tokenCryp );

        // $validator = $this->jwtservice->verifyToken();

        if ( $request ) {

            $goal = $request->headers->get( 'Goal' ) ?? null;

            if ( $goal === 'co' ) {

                $logger->info('Simulation de connexion avec token de connexion REPONSE SERVICE');
                $payload =  [
                    'auth' => true,
                    'sendMail' => false,
                ];

                $token = $this->jwtService->createToken( $payload );

                $goal = null;

                return new Response(
                    $token,
                    Response::HTTP_OK,
                    [ 'content-type' => 'text/html' ]
                );

            } elseif ( $goal == null ) {

                $tokenCrypt = $request->headers->get( 'Authorization' );

                $token = $this->jwtService->verifyToken( $tokenCrypt );

                

                if ( $token[ 'auth' ] === true && $token[ 'sendMail' ] === false && $token ) {

                    //generate random scope for send mail
                    $scopeMail = 'sendMail' . $this->generateRandomString(8);
                    
                    $id = $request->headers->get( 'id' );

                    $this->jwtService->stockScopeMail( $id, $scopeMail );

                    

                    $payload = [
                        'auth' => false,
                        'sendMail' => true,
                        'scopeMail' => $scopeMail,
                        'id' => $id,
                    ];
                    
                    $logger->info('Demande d\'authentification pour demande d\'envoie de mail REPONSE SERVICE');

                    $token = $this->jwtService->createToken( $payload );

                    return new Response(
                        $token,
                        Response::HTTP_OK,
                        [ 'content-type' => 'text/html' ]
                    );

                } elseif ( $token && $token[ 'sendMail' ] === true && $token[ 'auth' ] === false ) {

                    if ( $token[ 'scopeMail' ] === $this->jwtService->getScopeMail( $token[ 'id' ] ) ) {

                         $logger->info('Demande d\'envoie de mail');

                    $data = json_decode( $request->getContent(), true );

                    $email = $data[ 'email' ] ?? null;
                    $template = $data[ 'template' ] ?? null;
                    $content = $data[ 'content' ] ?? null;

                    if ( $email && $template && $content ) {
                        //suprimer le scope
                       $this->jwtService->deleteScopeMail( $token[ 'id' ] );
                        return $this->sendEmail( $logger, $mailer, $email, $template, $content );
                    } else {
                        return new Response(
                            'CHAMP MANQUANT',
                            Response::HTTP_UNAUTHORIZED,
                            [ 'content-type' => 'text/html' ]
                        );
                    }
                }
                else {
                    return new Response(
                        'INVALIDE TOKEN',
                        Response::HTTP_UNAUTHORIZED,
                        [ 'content-type' => 'text/html' ]
                    );
                }
            }
            } else {
            
                return new Response(
                    'INVALIDE TOKEN',
                    Response::HTTP_UNAUTHORIZED,
                    [ 'content-type' => 'text/html' ]
                );
            }

        } else {
            return new Response(
                'REQUETE INVALIDE',
                Response::HTTP_UNAUTHORIZED,
                [ 'content-type' => 'text/html' ]
            );
        }

    }

    function sendEmail( $logger, $mailer, $email, $template, $content ): Response  {

        if ( $template && $content && $email ) {
            $logger->info( 'Envoie de mail SERVICE' );

            if ( $template == 'emails/signup.html.twig' ) {
                $email = ( new TemplatedEmail() )
                ->from( $email )
                ->to( 'you@example.com' )
                ->subject( 'Contact' )
                ->text( $content )
                ->htmlTemplate( 'emails/signup.html.twig' )
                ->locale( 'de' )
                ->context( [
                    'expiration_date' => new \DateTime( '+7 days' ),
                    'client_name' => 'foo',
                ] );
            } elseif ( $template == 'emails/rappel.html.twig' ) {
                $email = ( new TemplatedEmail() )
                ->from( $email )
                ->to( 'you@example.com' )
                ->subject( 'Contact' )
                ->text( $content )
                ->htmlTemplate( 'emails/rappel.html.twig' )
                ->locale( 'de' )
                ->context( [
                    'expiration_date' => new \DateTime( '+7 days' ),
                    'client_name' => 'foo',
                    'montant_du' => 'montant_du',
                    'date_echeance' => 'date_echeance',
                    'iban'  => 'iban',
                    'bic' => 'bic',
                    'email$email_postale' => 'email$email_postale',
                ] );
            } elseif ( $template == 'emails/compterendu.html.twig' ) {
                $email = ( new TemplatedEmail() )
                ->from( $email )
                ->to( 'you@exemple.com' )
                ->subject( 'Contact' )
                ->text( $content )
                ->htmlTemplate( 'emails/compterendu.html.twig' )
                ->locale( 'de' )
                ->context( [
                    'expiration_date' => new \DateTime( '+7 days' ),
                    'client_name' => 'foo',
                    'satisfaction_rate' => 'satisfaction_rate',
                    'reservation_count' => 'reservation_count',
                ] );
            } elseif ( $template == 'emails/marketing.html.twig' ) {
                $email = ( new TemplatedEmail() )
                ->from( $email )
                ->to( 'you@exemple.com' )
                ->subject( 'Contact' )
                ->text( $content )
                ->htmlTemplate( 'emails/marketing.html.twig' )
                ->locale( 'de' )
                ->context( [
                    'expiration_date' => new \DateTime( '+7 days' ),
                ] );
            }

            try {
                $mailer->send( $email );

                return new Response(
                    'ENVOIE MAIL FAIT',
                    Response::HTTP_OK,
                    [ 'content-type' => 'text/html' ]
                );

            } catch ( TransportExceptionInterface $e ) {
                $logger->error( 'Erreur lors de l\'envoi du mail : '.$e->getMessage());

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
                    ['content-type' => 'text/html' ]
            );
        }

    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}


