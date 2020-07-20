<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Admin;
use App\Entity\Event;


class EventController extends AbstractController
{
    /**
     * @Route("/event/new", name="newEventPath")
     */
    public function new(EntityManagerInterface $manager)
    {

              if(isset($_POST["createEvent"])){

               $admin = $this->getDoctrine()->getRepository(Admin::class)->find(1);

                $events = $admin->getEvents();
                   
                $events->add($event = new Event());
                
                $event->setName($_POST['eventName']);
                
                $event->setCode($_POST['eventCode']);

                $manager->persist($admin);

                $manager->flush();

                

              }

        return $this->render('event/new.html.twig');
    }
}
