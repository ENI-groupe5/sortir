<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Form\RegisterFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ParticipantController
 * @package App\Controller
 * @Route("/participant")
 */
class ParticipantController extends AbstractController
{
    /**
     * @Route("/index", name="index_participant", methods={"GET"})
     * @param ParticipantRepository $participantRepository
     * @return Response
     */
    public function index(ParticipantRepository $participantRepository)
    {
        return $this->render('participant/index.html.twig', [
            'participant' => $participantRepository -> findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="afficher_profil", methods={"GET"})
     * @param Participant $participant
     * @return Response
     */
    public function affichageProfil(Participant $participant) {
        return $this->render('participant/affichageProfil.html.twig', ['participant' => $participant,]);
    }

    /**
     * @Route("/{id}/modifier_profil", name="modifier_profil", methods={"GET", "POST"})
     * @param Request $request
     * @param Participant $participant
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function modifierProfil(Request $request, Participant $participant, EntityManagerInterface $em) {
        $formulaire = $this->createForm(ParticipantType::class, $participant);
        $formulaire -> handleRequest($request);
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            /**
             * @var UploadedFile $avatar
             */
            $avatar = $formulaire ['avatar']->getData();
            if ($avatar) {
                $avatarActuel = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                $nouvelAvatar = $avatarActuel . '-' . uniqid() . '.' . $avatar->guessExtension();
                // Déplacement des avatars vers l'emplacement où sont stocké les avatars
                try {
                    $avatar->move(
                        $this->getParameter('avatar_directory'),
                        $nouvelAvatar
                    );
                } catch (FileException $e) {
                    // Gérer les exceptions si une erreur se produit pendant le téléchargement d'un avatar
                }
                $participant->setAvatar($nouvelAvatar);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($participant);
            $em->flush();
            $this->addFlash("success", "Votre profil a bien été modifié !");
            return $this->redirectToRoute('afficher_profil', array('id' => $participant->getId()));
        }

        return $this->render('participant/formulaireProfil.html.twig', [
            'participant' => $participant,
            'formulaire' => $formulaire->createView(),
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     */
    public function register(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new Participant();
        $registerForm = $this->createForm(RegisterFormType::class,$user);
        $registerForm->handleRequest($request);
        if ($registerForm->isSubmitted()&&$registerForm->isValid())
        {
            $user->setRoles(['ROLE_USER']);
            $user->setActif(true);
            $hashed = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hashed);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success','Enregistrement réussi');
            return $this->redirectToRoute('app_login');
        } elseif ($registerForm->isSubmitted()&&!$registerForm->isValid())
        {
            $this->addFlash('error','Enregistrement échoué');
        }
        return $this->render('participant/register.html.twig',[
            'form'=>$registerForm->createView()
        ]);
    }
}
