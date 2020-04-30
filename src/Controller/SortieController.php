<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
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
        $this->denyAccessUnlessGranted(['ROLE_USER','ROLE_ADMIN']);
        //récupérer le user
        $user = $this->getUser();

        //créer instance sortie
        $sortie = new Sortie();

        //créer instance formulaire
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        //récupérer les données
        $sortieForm->handleRequest($request);

        //hydrater le champ organisateur
        $sortie->setOrganisateur($user);

        try {
            //récupérer les lieux et leurs propriétés, les mettre dans un tableau
            // et les écrire dans un nouveau fichier json
            $repoLieux = $this->getDoctrine()->getRepository(Lieu::class);
            $lieux = $repoLieux->findAll();
            $response = array();
            $posts = array();
                for ($i = 0; $i < count($lieux); $i++) {
                    $id = $lieux[$i]->getId();
                    $nom = $lieux[$i]->getNom();
                    $rue = $lieux[$i]->getRue();
                    $latitude = $lieux[$i]->getLatitude();
                    $longitude = $lieux[$i]->getLongitude();
                    $nomVille = $lieux[$i]->getLieuVille()->getNom();
                    $cp = $lieux[$i]->getLieuVille()->getCodePostal();
                    $posts[] = array('id' => $id, 'nom' => $nom, 'rue' => $rue, 'latitude' => $latitude, 'longitude' => $longitude, 'nomVille' => $nomVille, 'cp' => $cp);
                }
            $response['posts'] = $posts;
            $fp = fopen('results.json', 'w');
            fwrite($fp, json_encode($response));
            fclose($fp);
        }   catch (Exception $e){
            $this->addFlash("danger","erreur! veuillez vous rapprocher du service informatique");
        }

        //tester les données
        if($sortieForm->isSubmitted() && $sortieForm->isValid()){

            //rediriger selon enregistrer / publier
            if($request->request->get('creer')){

                //recupérer l'état "Créée"
                $etatrepo = $em->getRepository(Etat::class);
                try {
                $etat = $etatrepo -> find(1);
                //pour hydrater $sortie
                $sortie->setSortieEtat($etat);

                    //sauvegarder données
                    $em->persist($sortie);
                    $em->flush();
                } catch (Exception $e){
                    $this->addFlash("danger","erreur! un problème est survenu lors de la création");
                }
                //message flash
                $this->addFlash('success', 'La sortie a bien été créée');
                //redirection accueil
                return $this->redirectToRoute('home');

            } elseif($request->request->get('publier')){
                //récupérer etat "Publiée"
                $etatrepo = $em->getRepository(Etat::class);
                try {
                    $etat = $etatrepo->find(2);
                    //hydrater l'etat
                    $sortie->setSortieEtat($etat);

                    //sauvegarder données
                    $em->persist($sortie);
                    $em->flush();
                }catch (\Exception $e){
                    $this->addFlash("danger","erreur! un problème est survenu lors de la publication");
                }
                //message flash
                $this->addFlash('success', 'La sortie a bien été publiée');
                //redirection accueil
                return $this->redirectToRoute('home');
            } else {
                throw new \Exception("problème lors de la soumission du formulaire",404);
            }

        }
        //afficher le formulaire
        return $this->render('sortie/creer.html.twig', [
            "sortieForm"=>$sortieForm ->createView()
        ]);
    }
}
