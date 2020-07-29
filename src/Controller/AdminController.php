<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Admin;

class AdminController extends AbstractController
{
    /**
     * @Route("/", name="adminIndexPath")
     */
    public function index()
    {

        return $this->render('admin/home.html.twig');

    }

    /**
     * @Route("/createadmin", name="createadminPath")
     *
    *public function createAdmin(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){

    *    $admin = $this->getDoctrine()->getRepository(Admin::class)->find(1);

    *    $hash = $encoder->encodePassword($admin, 'newton');
        
    *    $admin->setMail('claude@hotmail.fr');
    *    $admin->setPassword($hash);

    *    $manager->persist($admin);
    *    $manager->flush();

    *    return $this->render('admin/index.html.twig');

    *}
    
    **/

    
    /**
     * @Route("/login", name="security_login")
     */

    public function login(){
       

        return $this->render('admin/login.html.twig');

    }

    
    
    /**
     * @Route("/logout", name="logoutPath")
     */
                

    public function logout(){



    }

    
}
