<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController  extends AbstractController
{
    /**
     * @Route("/administrator", name="administrator")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('admin/index/index.html.twig', [
            'title' => 'Admin Panel',
        ]);
    }
}
