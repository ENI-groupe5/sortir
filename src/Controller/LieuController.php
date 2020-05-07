<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\LieuxSearch;
use App\Entity\Sortie;
use App\Form\LieuType;
use App\Form\LieuxSearchType;
use Doctrine\ORM\EntityManagerInterface;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    private $session;


    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }




    /**
     * @Route("/lieu", name="lieu")
     */
    public function index()
    {
        return $this->render('lieu/index.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }

    /**
     * @Route("/lieu/add", name="lieu_ajout")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function ajout(EntityManagerInterface $em, Request $request)
    {

        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class,$lieu);
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid())
        {
            $em->persist($lieu);
            $em->flush();
            $this->addFlash('success','Le lieu à bien été ajouté');
            $this->session->set('lieu',$lieu);
            return $this->redirectToRoute('sortie_creer');
        }

        return $this->render('lieu/newlieu.html.twig',[
            'form'=>$form->createView(),
            "context"=>'Ajouter un lieu',
            "context2"=>'Ajouter'
        ]);

    }


    /**
     * @Route("/lieu/modif/add", name="lieu_ajout_modif")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function modifajout(EntityManagerInterface $em, Request $request)
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class,$lieu);
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid())
        {
            $em->persist($lieu);
            $em->flush();
            $this->addFlash('success','Le lieu a bien été ajouté');
            return $this->redirectToRoute('sortie_modifier');
        }

        return $this->render('lieu/newlieu.html.twig',[
            'form'=>$form->createView(),
            "context"=>'Ajouter un lieu',
            "context2"=>'Ajouter'
        ]);

    }


    /**
     * @Route("/lieux/gerer",name="lieux_gerer")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return RedirectResponse|Response
     */
    public function gererlieux(EntityManagerInterface $em, Request $request,PaginatorInterface $paginator)
    {
        if ($this->isGranted('ROLE_ADMIN')) {

            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            $search = new LieuxSearch();
            $form = $this->createForm(LieuxSearchType::class, $search);
            $form->handleRequest($request);
            $lieuRepo = $em->getRepository(Lieu::class);
            $list = $paginator->paginate($lieuRepo->listallquery($search),
                $request->query->getInt('page', 1), 10);
            return $this->render('lieu/gerer.html.twig', [
                'form' => $form->createView(),
                'list' => $list
            ]);
        } else {
                $this->addFlash('danger','Vous n\'avez pas les droits d\'acces à cette page');
                return $this->redirectToRoute('home');
        }
    }


    /**
     * @Route("lieux/gerer/ajout",name="lieu_gerer_ajout")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newajout(EntityManagerInterface $em,Request $request){
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class,$lieu);
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid())
        {
            $em->persist($lieu);
            $em->flush();
            $this->addFlash('success','Le lieu a bien été ajouté');
            return $this->redirectToRoute('home');
        }

        return $this->render('lieu/newlieu.html.twig',[
            'form'=>$form->createView(),
            "context"=>'Ajouter un lieu',
            "context2"=>'Ajouter'
        ]);
    }

    /**
     * @Route("lieu/gerer/modifier/{id}", name="lieu_gerer_modifier")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function lieumodif(EntityManagerInterface $em,Request $request,$id)
    {
        $lieuRepo = $em->getRepository(Lieu::class);
        $lieu = $lieuRepo->find($id);
        $form = $this->createForm(LieuType::class,$lieu);
        $form->handleRequest($request);
            if($form->isSubmitted()&&$form->isValid())
            {
                $em->flush();
                $this->addFlash('success','le lieu '.$lieu->getNom().' a bien été modifié');
                return $this->redirectToRoute('lieux_gerer');
            }
            return $this->render('lieu/newlieu.html.twig',[
                'form'=>$form->createView(),
                "context"=>'Modifier un lieu',
                "context2"=>'Modifier'
            ]);
    }

    /**
     * @Route("lieu/gerer/supprimer/{id}",name="lieu_gerer_supprimer")
     * @param EntityManagerInterface $em
     * @param $id
     * @return RedirectResponse
     */
    public function lieudelete(EntityManagerInterface $em,$id)
    {
        $lieuRepo = $em->getRepository(Lieu::class);
        $lieu = $lieuRepo->find($id);
        $sortieRepo = $em->getRepository(Sortie::class);
        $sorties = $sortieRepo->findBy(array('lieu'=>$lieu));
        if ($sorties)
        {
            $this->addFlash('danger','Ce lieu est actuellement prévu pour une sortie, suppression impossible');
            return $this->redirectToRoute('lieux_gerer');
        } else
        {
        if ($lieu)
        {
            $em->remove($lieu);
            $em->flush();
            $this->addFlash('success','Le lieu '.$lieu->getNom().' a bien été supprimé');
            return $this->redirectToRoute('lieux_gerer');
        } else {
            $this->addFlash('danger','erreur dans la suppression du lieu');
            return $this->redirectToRoute('lieux_gerer');
        }
        }
    }

    /**
     * @Route("/lieu/recuperer/{id}" ,name="lieu_recuperer_id")
     * @param $id
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function recuplieu($id=0, EntityManagerInterface $em)
    {
        $lieuRepo = $em->getRepository(Lieu::class);
        $lieu = $lieuRepo->find($id);
        return $this->json(["nom"=>$lieu->getNom(),
            "rue"=>$lieu->getRue(),
            "ville"=>$lieu->getLieuVille()->getNom(),
            "cp"=>$lieu->getLieuVille()->getCodePostal(),
            "lat"=>$lieu->getLatitude(),
            "long"=>$lieu->getLongitude(),
            ]);
    }
}
