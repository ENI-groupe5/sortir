<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\LoadType;
use App\Form\ParticipantType;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            $em = $this->getDoctrine()->getManager();
            $hashed = $encoder->encodePassword($participant,$participant->getPassword());
            $participant->setPassword($hashed);
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
     */
    public function register(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new Participant();
        $registerForm = $this->createForm(RegisterFormType::class,$user);
        $registerForm->handleRequest($request);
        if ($registerForm->isSubmitted()&&$registerForm->isValid())
        {
            $user->setUpdatedAt(new \DateTime());
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
            $this->addFlash('danger','Enregistrement échoué');
        }
        return $this->render('participant/register.html.twig',[
            'form'=>$registerForm->createView()
        ]);
    }
    /**
     * @Route("/register/charger",name="app_charger")
     */
    public function chargerFichier(EntityManagerInterface $em,UserPasswordEncoderInterface $encoder){


        $csv = Reader::createFromPath('../assets/file/Classeur1.csv', 'r');
        $csv->setHeaderOffset(0);
        $resultats = $csv->getRecords();
        $repoParticipant = $this->getDoctrine()->getRepository(Participant::class);
        try {
            $repoParticipant->ajouterViaCsv($resultats,$em,$encoder);
        } catch (\Exception $e){
            $this->addFlash("danger", $e->getMessage());
            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success','vos utilisateurs ont bien été enregistrés');
        return $this->redirectToRoute('app_register');


    }
}
