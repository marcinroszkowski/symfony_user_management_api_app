<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * Matches / exactly
     *
     * @Route("/", name="default_index")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }
}