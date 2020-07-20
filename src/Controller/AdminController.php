<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Admin;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="adminIndexPath")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/createadmin", name="createadminPath")
     */


    public function createAdmin(EntityManagerInterface $manager){

        $admin = new Admin();

        $manager->persist($admin);
        $manager->flush();

        return $this->render('admin/index.html.twig');

    }
}
