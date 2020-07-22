<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Admin;
use App\Entity\Event;
use App\Entity\Image;
use App\Repository\AdminRepository;
use App\Repository\EventRepository;


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

    /**
     * @Route("/event/showone/{eventId}", name="showOneEventPath")
     */
    function showOne($eventId, EntityManagerInterface $manager){
    
        $event= $this->getDoctrine()->getRepository(Event::class)->find($eventId);

        $eventName = $event->getName();


        if (isset($_POST['addImages']) ){
                /** 
                   
           *$eventPath = 'events/' . $eventName ;
           *$imagePath = $eventPath . '/' . $_FILES['imgFile']['name'];
           *move_uploaded_file($_FILES['imgFile']['tmp_name'], $imagePath);
                
               **/

                $event->addImage($img = new Image());
                $img->setSrc('https://cache.cosmopolitan.fr/data/photo/w1000_ci/5l/tendances-mariage-2020.jpg');
                
                
                $event->addImage($img = new Image());
                $img->setSrc('https://i-df.unimedias.fr/2019/10/23/mariage_.jpg?auto=format%2Ccompress&crop=faces&cs=tinysrgb&fit=crop&h=700&w=1200');

                
                
                $event->addImage($img = new Image());
                $img->setSrc('https://frenchdistrict.com/wp-content/uploads/2014/05/marier-las-vegas-mariage-new-une2.jpg');

                
                $event->addImage($img = new Image());
                $img->setSrc('https://cdn-elle.ladmedia.fr/var/plain_site/storage/images/people/la-vie-des-people/news/le-discours-emouvant-de-stephanie-de-monaco-au-mariage-de-son-fils-louis-3800463/90689545-1-fre-FR/Le-discours-emouvant-de-Stephanie-de-Monaco-au-mariage-de-son-fils-Louis.jpg');

                

                
                $manager->persist($event);
                $manager->flush();   
        
       }   
    
        return $this->render('event/showone.html.twig', ['event' => $event]);

    }



    
}
