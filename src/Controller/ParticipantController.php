<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\LoadType;
use App\Form\ParticipantType;
use App\Form\RegisterFormType;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Exception;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Class ParticipantController
 * @package App\Controller
 * @Route("/participant")
 */
class ParticipantController extends AbstractController
{

    /**
     * @Route("/user/{id}", name="afficher_profil", methods={"GET"})
     * @param Participant $participant
     * @return Response
     */
    public function affichageProfil(Participant $participant)
    {
        return $this->render('participant/affichageProfil.html.twig',[
            'participant'=>$participant
        ]);
    }

    /**
     * @Route("/user/modifier_profil/{id}", name="modifier_profil", methods={"GET", "POST"})
     * @param Request $request
     * @param Participant $participant
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     */
    public function modifierProfil(Request $request, Participant $participant, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder) {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $formulaire = $this->createForm(ParticipantType::class, $participant);
        $formulaire -> handleRequest($request);
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $hashed = $encoder->encodePassword($participant,$participant->getPassword());
            $participant->setPassword($hashed);
            $participant->setUpdatedAt(new \DateTime());
            $em->persist($participant);
            $em->flush();
            $this->addFlash("success", "Votre profil a bien été modifié !");
            return $this->redirectToRoute('home', array('id' => $participant->getId()));
        }

        return $this->render('participant/formulaireProfil.html.twig', [
            'participant' => $participant,
            'formulaire' => $formulaire->createView()
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function register(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator)
    {
        // accès autorisé pour les administrateurs seulement
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        // on crée un nouveau participant
        $user = new Participant();

        // on crée un formulaire d'enregistrement et on traite la réponse
        $registerForm = $this->createForm(RegisterFormType::class,$user);
        $registerForm->handleRequest($request);

        // on crée un formulaire d'import csv et on traite la réponse
        $loadform = $this->createForm(LoadType::class);
        $loadform->handleRequest($request);

        // si on clique sur le bouton de création manuelle du formulaire

        // s'il est valide on crée un nouvel utilisateur en bdd
        if ($registerForm->isSubmitted()&&$registerForm->isValid())
        {
            $user->setUpdatedAt(new \DateTime());
            $roles = $registerForm->get("roles")->getData();
            $user->setRoles($roles);
            dump($roles);
            /*
            if (empty($roles)) {
                $user->setRoles('ROLE_USER');
            }*/
            $user->setActif(true);
            $hashed = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hashed);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success','Enregistrement réussi');
            return $this->redirectToRoute('app_register');

        } // s'il n'est pas valide, on lui envoie un message d'échec
        elseif ($registerForm->isSubmitted()&&!$registerForm->isValid())
        {
            $this->addFlash('danger','Enregistrement échoué');
        }

        // si on clique sur le bouton pour importer des utilisateurs via un fichier
        // s'il est valide
        if ($loadform->isSubmitted() && $loadform->isValid()){

            // nom que l'on donnera au fichier
            $nomFichier = "fichier.csv";
            // on récupère les données chargées dans le champ
            $file = $loadform['filefield']->getData();
            // on sauvegarde ce fichier dans le dossier assets
            $file->move("../assets/file/", $nomFichier);
            // on récupère le fichier csv
            $csv = Reader::createFromPath('../assets/file/fichier.csv', 'r');
            // on lui attribue un header (la première ligne du fichier csv)
            $csv->setHeaderOffset(0);
            // on compte le nombre de lignes du fichier
            $taille = count($csv);
            // on récupère les lignes
            $resultats = $csv->getRecords();

            // on récupère le repository du participant et on appelle une fonction qui va nous permettre
            // de créer chaque utilisateur en bdd ou de nous retourner les erreurs quand il y en a
            $repoParticipant = $this->getDoctrine()->getRepository(Participant::class);
            $errors = $repoParticipant->ajouterViaCsv($resultats, $em, $encoder, $validator);

            // s'il n'y a pas d'erreurs, tout s'est bien déroulé, on renvoie un emssage
            if (empty($errors)){
                $this->addFlash('success', 'vos utilisateurs ont bien été enregistrés');
                return $this->redirectToRoute('app_register');
            }


        } // s'il n'est pas valide, on lui envoie un message d'échec
        elseif ($loadform->isSubmitted()&&!$loadform->isValid())
        {
            $this->addFlash('danger','problème lors de l\'opération');
        }

        // s'il y a eu des erreurs, on renvoie vers la page de création avec les erreurs
        if (isset($errors)) {
            return $this->render('participant/register.html.twig', [
                'form' => $registerForm->createView(),
                'loadform' => $loadform->createView(), 'errors' => $errors,'taille'=>$taille
            ]);
        } // sinon on ne renvoie que vers la page d'accueil
        else{
            return $this->render('participant/register.html.twig', [
                'form' => $registerForm->createView(),
                'loadform' => $loadform->createView(),
            ]);
        }


    }

    /**
     * @Route("/liste", name="liste_participants", methods={"GET"})
     * @param ParticipantRepository $participantRepository
     * @param SiteRepository $siteRepository
     * @return Response
     */
    public function listeParticipants(ParticipantRepository $participantRepository, SiteRepository $siteRepository)
    {
        return $this->render('participant/listeUtilisateurs.html.twig', [
            'participant' => $participantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/user/supprimer/{id}", name="supprimer_participant", methods={"supprimer"})
     * @param Request $request
     * @param Participant $participant
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function supprimerParticipants(Request $request, Participant $participant, EntityManagerInterface $em)
    {
        if ($this->isCsrfTokenValid('supprimerParticipants'.$participant->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($participant);
            $em->flush();
        }
        $this->addFlash("danger", "L'utilisateur ".$participant->getPrenom()." ".$participant->getNom(). " vient d'être supprimé.");
        return $this->redirectToRoute('liste_participants');
    }

    /**
     * @Route("/user/modifactif/{id}", name="user_modifieractif")
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function modifactifparticipant($id, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $userRepo = $em->getRepository(Participant::class);
        $user = $userRepo->find($id);
        if ($user)
        {
            if ($user->getActif()==1)
            {
                $user->setActif(0);
                $em->flush();
                $this->addFlash('success',"l'utilisateur a été desactivé");
            }
            else
            {
                $user->setActif(1);
                $em->flush();
                $this->addFlash('success',"l'utilisateur a été activé");
            }
        }
        else
        {
            $this->addFlash('danger','L\'utilisateur n\'a pas été trouvé');
        }
        return $this->redirectToRoute('liste_participants');

    }
}
