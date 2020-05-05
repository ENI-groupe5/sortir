<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
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
        }
        $this->addFlash('success','Le lieu à bien été ajouté');
        //TODO
        return $this->render('',[
            'form'=>$form->createView(),
        ]);

    }
}
