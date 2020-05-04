<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\SortieSearch;
use App\Form\AnnulerSortieType;
use App\Form\FiltreSortieType;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
       $search = new SortieSearch();
       $form = $this->createForm(FiltreSortieType::class,$search);
       $form->handleRequest($request);
        $sortieRepo = $em->getRepository(Sortie::class);
        $participanRepo = $em->getRepository(Participant::class);
           $user= $participanRepo->find($this->getUser()->getId());
           $sorties = $paginator->paginate($sortieRepo->listSortieQuery($search,$user),
               $request->query->getInt('page',1),10);
       return $this->render('sortie/listsortie.html.twig',[
           'sorties'=>$sorties,
           'search'=>$form->createView()
       ]);
    }


    /**
     * @Route("/mobile", name="sortie_list_mobile")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     */
    public function listMobile(Request $request,EntityManagerInterface $em, PaginatorInterface $paginator)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        $search = new SortieSearch();
        $form = $this->createForm(FiltreSortieType::class,$search);
        $form->handleRequest($request);
        $sortieRepo = $em->getRepository(Sortie::class);
        $user= $this->getUser();
        $sorties = $paginator->paginate($sortieRepo->sortieMobileQuery($user),
            $request->query->getInt('page',1),10);

        return $this->render('sortie/listsortiemobile.html.twig',[
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
     * @throws \Exception
     */
    public function creer(Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        //récupérer le user
        $user = $this->getUser();

        //créer instance sortie
        $sortie = new Sortie();
        $sortie->setDatHeureDebut(new \DateTime("+7 days"));
        $sortie->setDateLimiteInscription(new \DateTime());
        $sortie->setDuree(90);

        //créer instance formulaire
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        //récupérer les données
        $sortieForm->handleRequest($request);

        //hydrater le champ organisateur et site
        $sortie->setOrganisateur($user);
        $sortie->setSite($user->getSite());

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
        }   catch (\Exception $e){
            throw $this->createNotFoundException("erreur! veuillez vous rapprocher du service informatique");
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
                } catch (\Exception $e){
                    $this->addFlash("danger","erreur! un problème est survenu lors de la création");
                    return $this->redirectToRoute('home');
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
                    return $this->redirectToRoute('home');
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

    /**
     * @Route("/sortie/inscrire/{id}", name="sortie_inscrire")
     * requirements={"id": "\d+"})
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function inscrire ($id,EntityManagerInterface $em)
    {

        $partirepo = $em->getRepository(Participant::class);
        $user = $partirepo->find($this->getUser()->getId());
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);
        if ($sortie->getSortieEtat()->getId() == 2 && count($sortie->getParticipants()) < $sortie->getNbInscriptionsMax()&& $sortie->getOrganisateur()!=$user)
        {
            if (!$sortie->getParticipants()->contains($user)) {
                $sortie->getParticipants()[] = $user;
                $user->setSorties($sortie);
            }
        $em->flush();
        $this->addFlash('success', 'Vous êtes maintenant inscrit à la sortie '.$sortie->getNom());
          } else {
            $this->addFlash('danger','L\'inscription est impossible, la sortie est cloturée ou vous n\avez pas les droits');
        }
        
        return $this->redirectToRoute('home');

    }

    /**
     * @Route("/sortie/desinscrire/{id}",name="sortie_desinscrire")
     * requirements={"id": "\d+"})
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function desinscrire ($id,EntityManagerInterface $em)
    {
        $partirepo = $em->getRepository(Participant::class);
        $user  = $partirepo->find($this->getUser()->getId());
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie=$sortieRepo->find($id);
        if ($sortie->getParticipants()->contains($user)){
            $sortie->getParticipants()->removeElement($user);
            if($user->getSorties()===$sortie){
                $user->getSorties(null);
            }
        }
        if($user->getSorties()->contains($sortie)) {
            $user->getSorties()->removeElement($sortie);
            if ($sortie->getParticipants() === $user) {
                $sortie->setParticipants(null);
            }
        }
        $em->flush();
        if ($sortie->getParticipants()->contains($user)||$user->getSorties()->contains($sortie))
        {
            $this->addFlash('error','Desinscription échouée');
        } else{
            $this->addFlash('success','Vous êtes maintenant desinscrit de la sortie '.$sortie->getNom());
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/sortie/afficher/{id}",name="sortie_afficher",requirements={"id"="\d+"})
     */
    public function afficherUneSortie($id){
        // autoriser l'accès à l'affichage que pour les utilisateurs connectés
        $this->denyAccessUnlessGranted('ROLE_USER');

        // récupère le contenu de la sortie grâce à son id
        $repoSortie = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $repoSortie->find($id);

        // si l'id ne retourne pas de résultat, renvoi vers la page d'accueil sinon affichage de la sortie
        if (empty($sortie)) {
            $this->addFlash('danger', 'nous avons rencontré une erreur, veuillez réessayer!!');
            return $this->redirectToRoute('home');
        } else {
            return $this->render('sortie/afficher.html.twig',['sortie'=>$sortie]);
        }
    }

    /**
     * @Route("/sortie/modifier/{id}",name="sortie_modifier",requirements={"id"="\d+"})
     * @param $id
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function modifierUneSortie($id,EntityManagerInterface $em,Request $request){
        // autoriser l'accès à l'affichage que pour les utilisateurs connectés
        $this->denyAccessUnlessGranted('ROLE_USER');

        // récupère le contenu de la sortie grâce à son id
        $repoSortie = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $repoSortie->find($id);

        // si l'id ne retourne pas de résultat, renvoi vers la page d'accueil
        if (empty($sortie)) {
            $this->addFlash('danger', 'nous avons rencontré une erreur, veuillez réessayer!!');
            return $this->redirectToRoute('home');
        } else {
        // si pas d'erreur, autoriser l'accès à cette page uniquement si l'utilisateur est aussi l'organisateur
        $user = $this->getUser();
        $organisateur = $sortie->getOrganisateur();

            if ($user === $organisateur){
                //créer instance formulaire
                $sortieForm = $this->createForm(SortieType::class, $sortie);

                //récupérer les données
                $sortieForm->handleRequest($request);

                try {
                    //récupérer les lieux et leurs propriétés, les mettre dans un tableau
                    // et les écrire dans un nouveau fichier json pour les envoyer à la vue
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
                }   catch (\Exception $e){
                    throw $this->createNotFoundException("erreur! veuillez vous rapprocher du service informatique");
                }


                //tester les données
                if($sortieForm->isSubmitted() && $sortieForm->isValid()){
                    $etatrepo = $em->getRepository(Etat::class);

                    //si bouton enregistrer
                    if($request->request->get('enregistrer')){
                        try{
                            //sauvegarder données
                            $em->persist($sortie);
                            $em->flush();
                        } catch (\Exception $e){
                            $this->addFlash("danger","erreur! un problème est survenu lors de la modification");
                            return $this->redirectToRoute('home');
                        }
                        //message flash
                        $this->addFlash('success', 'La sortie a bien été modifiée');
                        //redirection accueil
                        return $this->redirectToRoute('home');

                    } //si bouton publier
                    elseif($request->request->get('publier')){
                        try{
                            $etat = $etatrepo -> find(2);
                            //pour hydrater $sortie
                            $sortie->setSortieEtat($etat);

                            //sauvegarder données
                            $em->persist($sortie);
                            $em->flush();
                        } catch (\Exception $e){
                            $this->addFlash("danger","erreur! un problème est survenu lors de la publication");
                            return $this->redirectToRoute('home');
                        }
                        //message flash
                        $this->addFlash('success', 'La sortie a bien été publiée');
                        //redirection accueil
                        return $this->redirectToRoute('home');

                    } // si bouton supprimer
                    elseif ($request->request->get('supprimer')){
                        try{

                            //sauvegarder données
                            $em->remove($sortie);
                            $em->flush();
                        } catch (\Exception $e){
                            $this->addFlash("danger","erreur! un problème est survenu lors de la suppression");
                            return $this->redirectToRoute('home');
                        }
                    //message flash
                    $this->addFlash('success', 'La sortie a bien été supprimée');
                    //redirection accueil
                    return $this->redirectToRoute('home');

                    }
                    else {
                        // le formulaire n'est pas valide
                        throw new \Exception("problème lors de la soumission du formulaire",404);
                    }

                }


                // afficher le formulaire
                return $this->render('sortie/modifier.html.twig',['sortieForm'=>$sortieForm->createView()]);
            } else {
                // ce n'est pas l'organisateur, renvoyer une erreur
               $this->addFlash('warning','vous n\'êtes pas autorisé à modifier cette sortie!');
               return $this->redirectToRoute('home');
            }
        }
    }

    /** Annuler une sortie
     * @Route("/sortie/annuler/{id}", name="sortie_annuler")
     * @param $id
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function annuler($id, EntityManagerInterface $em, Request $request)
    {
        // acces limité
        $this->denyAccessUnlessGranted('ROLE_USER');

        //récupérer la sortie rattachée à l'id
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);

        //récupérer lieu de la sortie
        $lieu = $sortie->getLieu();

        //récupérer la rue
        $lieuRepo = $em->getRepository(Lieu::class);
        $detailLieu = $lieuRepo->find($lieu);
        $rue = $detailLieu->getRue();

        //récupérer la ville et le cp
        $ville = $detailLieu->getLieuVille()->getNom();
        $cp = $detailLieu->getLieuVille()->getCodePostal();

        //créer instance formulaire + l'associer à $sortie
        $annulerForm = $this->createForm(AnnulerSortieType::class, $sortie);

        //l'utilisateur doit etre l'organisateur de la sortie ou un admin
        $user = $this->getUser();
        $organisateur = $sortie->getOrganisateur();
        $role = $user->getRoles();
        if ($user == $organisateur || $role == 'ROLE_ADMIN') {

            //la sortie ne doit pas être commencée : now < dateDébut
            // et ne peut être modifiée que si son etat est ouvert
            $now = new \DateTime('now');
            $etatSortie = $sortie->getSortieEtat()->getId();
            if ($now < $sortie->getDatHeureDebut() && $etatSortie == 2) {

                //récupérer les données : motif
                $annulerForm->handleRequest($request);

                if ($annulerForm->isSubmitted() && $annulerForm->isValid()) {

                    //modifier l'état > annulée
                    $etatrepo = $em->getRepository(Etat::class);
                    $etat = $etatrepo->find(3);
                    $sortie->setSortieEtat($etat);

                    //mettre à jour en BDD
                    $em->persist($sortie);
                    $em->flush();

                    //rediriger
                    $this->addFlash('success', 'La sortie a bien été annulée');
                    //redirection accueil
                    return $this->redirectToRoute('home');
                } else {
                    $this->addFlash('info', 'Veuillez renseigner le formulaire');
                }
            } else {
                $this->addFlash('danger', 'Annulation échouée : la sortie ne peut être annulée qu\'avant la date de son commencement et si son etat est ouvert !');
                //redirection accueil
                return $this->redirectToRoute('home');
            }
        } else {
            $this->addFlash('danger', 'Annulation échouée : accès limité à l\'organisateur de la sortie et aux administrateurs !');
            //redirection accueil
            return $this->redirectToRoute('home');
        }
        //envoyer le formulaire
        return $this->render('sortie/annuler.html.twig', [
            'annulerForm' => $annulerForm->createView(),
            'sortie' => $sortie,
            'rue' => $rue,
            'cp' => $cp,
            'ville' => $ville
        ]);
    }


    /**
     * @Route("sortie/publish/{id}", name="sortie_publier")
     * requirements={"id"="\d+"}
     * @param $id
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function publier($id, EntityManagerInterface $em){
        $user = $this->getUser();
        $sortieRepo = $em->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);
        if ($sortie->getOrganisateur()!=$user)
        {
            $this->addFlash('danger','Vous devez être l\'organisateur de cette sortie pour la publier');
            return $this->redirectToRoute("home");
        }
        $etatRepo = $em->getRepository(Etat::class);
        $etat = $etatRepo->find(2);
        $sortie->setSortieEtat($etat);
        $em->flush();
        $this->addFlash('success','La sortie '.$sortie->getNom(). ' est bien publiée');
        return $this->redirectToRoute("home");


    }
}
