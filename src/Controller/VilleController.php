<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    /**
     * @Route("/ville", name="ville")
     */
    public function index()
    {
        return $this->render('ville/index.html.twig', [
            'controller_name' => 'VilleController',
        ]);
    }

    /**
     * @Route("/ville/ajout", name="ville_ajout")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     */
    public function ajouter(EntityManagerInterface $em, Request $request)
    {
        $ville = new Ville();
        $form = $this->createForm(VilleType::class,$ville);
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid())
        {
            $em->persist($ville);
            $em->flush();
            $this->addFlash('success','La ville '.$ville->getNom().' à bien été ajoutée');
            return $this->redirectToRoute('home');
        }
        return $this->render('ville/ajouterville.html.twig',[
            'form'=>$form->createView()
        ]);

    }
}
