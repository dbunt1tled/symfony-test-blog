<?php

namespace App\Controller\Admin\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ManageController  extends AbstractController
{
    /**
     * @Route("/admin2", name="admin2")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'title' => 'Main Page',
        ]);
    }
}
