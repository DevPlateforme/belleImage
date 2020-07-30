<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Admin;
use App\Entity\Event;
use App\Entity\Image;
use App\Entity\Stripe;
use App\Repository\AdminRepository;
use App\Repository\EventRepository;
use Symfony\Component\String\Slugger\SluggerInterface;



class EventController extends AbstractController
{


    

    /**
     * @Route("/event/search", name="searchEventPath")
     */

    public function find(EntityManagerInterface $manager)
    {

        if(isset($_POST['eventCode'])){


            if($this->getDoctrine()->getRepository(Event::class)->findOneBy(['code' => $_POST['eventCode']])){

                $event = $this->getDoctrine()->getRepository(Event::class)->findOneBy(['code' => $_POST['eventCode']]);

                $id = $event->getId();


                $response = new Response();

                $eventCookie = new Cookie('eventCookie', $id , strtotime('+1 day'));

                $response->headers->setCookie($eventCookie);
                $response->send();

    
            return $this->redirectToRoute('showOneEventPath', [ 'eventId' => $id]);

            } 

            return $this->redirectToRoute('adminIndexPath');

        }
    
    }



    /**
     * @Route("/event/new", name="newEventPath")
     */

    public function new(EntityManagerInterface $manager)
    {

        $event = new Event();

           if(isset($_POST["createEvent"])){

                $admin = $this->getDoctrine()->getRepository(Admin::class)->find(1);

                $events = $admin->getEvents();
                   
                $events->add($event);
                
                $event->setName($_POST['eventName']);
                
                $event->setCode($_POST['eventCode']);

                $manager->persist($admin);

                $manager->flush();


                return $this->redirectToRoute('showOneEventPath', [ 'eventId' => $event->getId()]);

           }

        return $this->render('event/new.html.twig');
    }







    /**
     * @Route("/event/showone/{eventId}", name="showOneEventPath")
     */
    function showOne($eventId, EntityManagerInterface $manager, Request $request,  SluggerInterface $slugger){
        
        $cart = null;

        if($request->cookies->get('cart')){

            $cart = $request->cookies->get('cart');

            $cart = explode(',' , $cart);
        }


        $event= $this->getDoctrine()->getRepository(Event::class)->find($eventId);

        $eventName = $event->getName();

        
        $image = new Image();
        

        if (isset($_POST['fileSent'])) {

            $event->addImage($image);



                    $image->setName('name');


                    $path = $this->getParameter('eventsDirectory'). $eventName . '/' . $_FILES['myFile']['name'];


                    if (!file_exists($this->getParameter('eventsDirectory'). $eventName)) {
                        mkdir($this->getParameter('eventsDirectory'). $eventName, 0777, true);
                    }

                    move_uploaded_file($_FILES['myFile']['tmp_name'], $path);


                    $image->setSrc($path);

                    $image->setAssetSrc('assets/uploads' . '/' . $eventName . '/' . $_FILES['myFile']['name']);
    
                    $manager->persist($image);
    
                    $manager->flush();


                    return $this->redirectToRoute('showOneEventPath', ['eventId' => $eventId ]);

              
            }
            

        $images = $event->getImages();

        

    
        return $this->render('event/showone.html.twig', ['cart' => $cart , 'event' => $event , 'images' => $images]);

    }



    
             

    /**
     * @Route("/event/image/delete", name="deleteImagePath")
     */

    

    function deleteImage(Request $request, EntityManagerInterface $manager){

        if(isset($_POST['eventId'])){

            $eventId = $_POST['eventId'];

            $imageId = $_POST['imageId'];


            $filesystem = new Filesystem();

        $event = $this->getDoctrine()->getRepository(Event::class)->find($eventId);

        $images = $event->getImages();

        $img = $this->getDoctrine()->getRepository(Image::class)->find($imageId);


        foreach($images as $image){

            if($image->getId() == $imageId){

                $event->removeImage($img);

                $manager->persist($event);
                $manager->flush();
            }         

        }

        $path = $img->getSrc();

        $filesystem->remove($path);


        return $this->redirectToRoute('showOneEventPath', [ 'eventId' => $eventId]);


        }

        
    }

    
    /**
     * @Route("/events/showall", name="showAllEventsPath")
     */

    function showall(Request $request){
              
        $events = $this->getDoctrine()->getRepository(Event::class)->findAll();

        return $this->render('event/showall.html.twig', ['events' => $events]);

    }

     

    /**
     * @Route("/cart/addimg", name="addImgToCartPath")
     */

    function addImgToCartPath(Request $request){

        $response = new Response(
           
        );


    if(isset($_POST["imageId"])){

        $imageId = $_POST['imageId'];

            
        if($request->cookies->get('cart') != null){

            $cookie = $request->cookies->get('cart');
 
            $cookie .= ',' . $imageId;

            $updatedCookieValue = $cookie;


            $cookie = new Cookie('cart', $updatedCookieValue , strtotime('+1 day'));
            $response->headers->setCookie($cookie);
            $response->send();


            $cookie = $request->cookies->get('cart'); 

         
         return new JsonResponse(['cookieContent'=>'article ajouté!']);
 
 
         } else {

            $cookie = new Cookie('cart', $imageId, strtotime('+1 day'));
            $response->headers->setCookie($cookie);
            $response->send();


             
         return new JsonResponse(['cookieContent'=>'premier article ajouté!']);

           }

      }

           
    }
    

    
    /**
     * @Route("/event/seeCart", name="seeCartPath")
     */

    function checkout(Request $request){
           
        $eventSeen = null;
       
           if($request->cookies->get('eventCookie')){

            $eventSeen = $request->cookies->get('eventCookie');

           }
        if ($request->cookies->get('cart') != null){

            $cart = $request->cookies->get('cart');

            $cart = explode(',' , $cart);

            $cartArray = [];
            
            foreach($cart as $imageId){

                $repo = $this->getDoctrine()->getRepository(Image::class);

                $img = $repo->find($imageId);

                
                $cartArray[] = $img;

            }

            return $this->render('cart/checkout.html.twig', ['eventSeen' => $eventSeen , 'cart' => $cartArray, 'cartValue' => 5*count($cart)]);


        }
        
        return $this->render('cart/checkout.html.twig', ['eventSeen' => $eventSeen , 'cart' => null, 'cartValue' => null]);

        


    }



    
    /**
     * @Route("/event/paymentPage", name="paymentPagePath")
     */


    function paymentPage(Request $request){

        if($request->cookies->get('cart') != null){
        
        $cookie = $request->cookies->get('cart');

        $cookie = explode(',' , $cookie);

        $imageNumber = count($cookie);
        
        
        return $this->render('cart/stripe.html.twig', ['imagesNumber' => $imageNumber]);


        }

    }



    /**
     * @Route("/event/stripe/payment/{imagesNumber}", name="stripePaymentPath")
     */


    function payment($imagesNumber){


        $token = $_POST['stripeToken'];
        $email = $_POST['mail'];
        $name = $_POST['name'];

 

      
       if(filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($name) && !empty($token)){

 
        $stripe = new Stripe('sk_test_51H03AQHsoMXBsfUuXefwcN7pALjO3Bg7zHL204QfsI8YIs6N4WmTCjkPFMmYYw7DMwJVPhUzrpL7wnllbtMpVbuj00QMBYI2uJ');

	       $customer = $stripe->api('customers', [
		     'source' => $token,
             'description' => $name,
             'email' => $email
	        ]);



   //Charge the client

   
          $stripe->api('charges', [

         	'amount' => $imagesNumber * 500,
            'currency' => 'eur',
            'customer' => $customer->id]);

                   };


            $error = $stripe->error;

           if($error != null){

            return $this->render('event/paymentError.html.twig', ['error' => $error]);

            }




          return $this->render('event/collect.html.twig');

             }


    
     

             

    /**
     * @Route("/event/cart/deleteFromCart", name="clearCartPath")
     */


    function clearCart(Request $request){

        $cart = $request->cookies->get('cart');

        $cart = explode(',' , $cart);

        $updatedCookieValue = implode(",", $cart);


    
        $response = new Response();
        $response->headers->clearCookie('cart');
        $response->send();


        return $this->redirectToRoute('seeCartPath');
         
            
    }



    
}
