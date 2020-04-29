<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\SortieSearch;
use App\Form\FiltreSortieType;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SortieController
 * @package App\Controller
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function list(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator)
    {
       $search = new SortieSearch();
       $form = $this->createForm(FiltreSortieType::class,$search);
       $form->handleRequest($request);
        $sortieRepo = $em->getRepository(Sortie::class);

           $user= $this->getUser();
           $sorties = $paginator->paginate($sortieRepo->listSortieQuery($search,$user),
               $request->query->getInt('page',1),10);




       return $this->render('sortie/listsortie.html.twig',[
           'sorties'=>$sorties,
           'search'=>$form->createView()
       ]);
    }


    /**
     * Créer ou publier une sortie
     * @Route("/sortie/creer", name="sortie_creer")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function creer(Request $request, EntityManagerInterface $em)
    {
        //récupérer le user
        $user = $this->getUser();

        //créer instance sortie
        $sortie = new Sortie();

        //créer instance formulaire
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        //récupérer les données
        $sortieForm->handleRequest($request);

        //hydrater le champ organisateur
        $sortie->setOrganisateur($user->getUsername());

        //tester les données
        if($sortieForm->isSubmitted() && $sortieForm->isValid()){

            //rediriger selon enregistrer / publier
            if($sortieForm->get('creer')->isClicked()) {

                //sauvegarder données
                $em ->persist($sortie);
                $em->flush();

                //message flash
                $this->addFlash('success', 'La sortie a bien été créée');
                //redirection accueil
                $this->redirectToRoute('home');

            } elseif($sortieForm->get('publier')->isClicked()) {

                //hydrater l'etat
                $sortie->setSortieEtat('ouverte');

                //sauvegarder données
                $em ->persist($sortie);
                $em->flush();

                //message flash
                $this->addFlash('success', 'La sortie a bien été publiée');
                //redirection accueil
                $this->redirectToRoute('home');
            }
        }
        //afficher le formulaire
        return $this->render('sortie/creer.html.twig', [
            "sortieForm"=>$sortieForm ->createView()
        ]);
    }
}
