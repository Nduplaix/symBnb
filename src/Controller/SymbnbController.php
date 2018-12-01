<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SymbnbController extends AbstractController
{

    /**
     * @Route("/", name="homepage")
     */
    public function home(){
        return $this->render('symbnb/home.html.twig');
    }

    /**
     * @Route("/symbnb", name="symbnb")
     */
    public function index()
    {
        return $this->render('symbnb/index.html.twig', [
            'controller_name' => 'SymbnbController',
        ]);
    }
}
