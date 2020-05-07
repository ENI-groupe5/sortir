<?php

namespace App\Controller;

use App\Form\AskEmailForResetPassType;
use App\Form\ResetPassType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/oubliPass", name="app_oubliPass")
     * @param Request $request
     * @param ParticipantRepository $participants
     * @param TokenGeneratorInterface $tokenGenerator
     * @return Response
     */
    public function oubliPass(Request $request, ParticipantRepository $participants, TokenGeneratorInterface $tokenGenerator)
    {

        //initialiser le formulaire
        $emailForm = $this->createForm(AskEmailForResetPassType::class, ['email' => ""]);

        //récupérer le formulaire
        $emailForm->handleRequest($request);

        // si formulaire est valide
        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            //récupérer les données
            $email = $emailForm->get('email')->getData();

            try {
                // chercher un utilisateur ayant cet e-mail
                $user = $participants->findOneByEmail($email);
            } catch (\Exception $e) {
                $this->addFlash("danger", "Erreur ! Un problème est survenu lors de l'identification de l'e-mail.");
                return $this->redirectToRoute('home');
            }

            if ($user === null) {
                $this->addFlash('danger', 'Cette adresse e-mail est inconnue');
                return $this->redirectToRoute('home');
            }

            // générer un token
            $token = $tokenGenerator->generateToken();

            // sauvegarder le token en bdd
            try {
                $user->setResetToken($token);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Erreur ! Un problème est survenu lors de la sauvegarde du token. Veuillez contacter le service technique.');
                return $this->redirectToRoute('home');
            }

            // générer l'URL de réinitialisation de mdp
            $url = $this->generateUrl('app_resetPassword', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

    /*
            //générer l'e-mail
            $message = (new \Swift_Message('Mot de passe oublié'))
                ->setFrom('votre@adresse.fr')
                ->setTo($user->getEmail())
                ->setBody(
                    "Bonjour,<br><br>Une demande de réinitialisation de mot de passe a été effectuée pour le site Nouvelle-Techno.fr. Veuillez cliquer sur le lien suivant : " . $url,
                    'text/html'
                )
            ;
            // On envoie l'e-mail
            $mailer->send($message);
            // On crée le message flash de confirmation
            $this->addFlash('message', 'E-mail de réinitialisation du mot de passe envoyé !');
    */

            //à la place de l'envoi de l'email, envoi sur une page avec le lien URL pour réinitialiser mdp:
            $this->addFlash("info", "simulation d'envoi d'email");
            return $this->render('security/simulation_email.html.twig', [
                'url'=>$url,
                'email'=>$email,
                'pseudo'=>$user->getUsername()
            ]);

        } else {
            $this->addFlash('info', 'Veuillez renseigner votre adresse e-mail associée à votre compte');
        }

        // envoyer le formulaire à la vue
        return $this->render('security/oubliPass.html.twig', [
            'emailForm' => $emailForm->createView()
        ]);
    }

    /**
     * @Route("/resetPassword/{token}", name="app_resetPassword")
     * @param Request $request
     * @param string $token
     * @param ParticipantRepository $participant
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function resetPassword(string $token, Request $request, ParticipantRepository $participant, UserPasswordEncoderInterface $passwordEncoder)
    {
        //chercher un utilisateur avec le token donné
        $user = $participant->findOneBy(['reset_token' => $token]);

        //si user n'existe pas
        if ($user === null) {
            $this->addFlash('danger', 'Erreur ! Token inconnu, veuillez contacter le service technique.');
            return $this->redirectToRoute('home');
        }

        //créer formulaire et l'associer
        $pwdForm = $this->createForm(ResetPassType::class, $user);

        //récupérer formulaire
        $pwdForm->handleRequest($request);

        // si formulaire est valide
        if ($pwdForm->isSubmitted() && $pwdForm->isValid()) {

            // On supprime le token
            $user->setResetToken(null);

            //récupérer les données
            $pwd = $pwdForm->get('password')->getData();

            // On chiffre le mot de passe saisi
            $user->setPassword($passwordEncoder->encodePassword($user, $pwd));

            // On sauvegarde le user en BDD
            $em = $this->getDoctrine()->getManager();
            //$em->persist($user);
            $em->flush();

            // redirection login
            $this->addFlash('success', 'Mot de passe mis à jour');
            return $this->redirectToRoute('app_login');
        }else {
            $this->addFlash('info','Veuillez saisir votre nouveau mot de passe');
        }
        // Si on n'a pas reçu les données, on affiche le formulaire
        return $this->render('security/resetPass.html.twig', [
            'token' => $token,
            'pwdForm' => $pwdForm->createView()
        ]);

    }

}
