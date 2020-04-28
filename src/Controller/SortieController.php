<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\SortieSearch;
use App\Form\FiltreSortieType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="sortie")
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
}
