<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ParticipantController
 * @package App\Controller
 * @Route("/participant")
 */
class ParticipantController extends AbstractController
{
    /**
     * @Route("/index", name="index_participant", methods={"GET"})
     */
    public function index(ParticipantRepository $participantRepository)
    {
        return $this->render('participant/index.html.twig', [
            'participant' => $participantRepository -> findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="afficher_profil", methods={"GET"})
     */
    public function affichageProfil(Participant $participant) {
        return $this->render('participant/affichageProfil.html.twig', ['participant' => $participant,]);
    }

    /**
     * @Route("/{id}/modifier_profil", name="modifier_profil", methods={"GET", "POST"})
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
}
