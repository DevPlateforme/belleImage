<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Admin;
use App\Entity\Event;
use App\Entity\Image;
use App\Entity\Stripe;
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

                
                
                
                $manager->persist($event);
                $manager->flush();   
        
       }   


                       $images = $event->getImages();

    
        return $this->render('event/showone.html.twig', ['event' => $event , 'images' => $images]);

    }



    /**
     * @Route("/cart/addimg", name="addImgToCartPath")
     */

    function addImgToCartPath(){
        
    if(isset($_POST["imageId"])){

        $imageId = $_POST['imageId'];

            
        if(isset($_COOKIE['cart'])){

            $cookieValue = $_COOKIE['cart'];
 
            $cookieValue .= ',' . $imageId;
 
 
         setcookie("cart", $cookieValue );

         
         return new JsonResponse(['cookieContent' => $_COOKIE['cart']]);
 
 
         } else {
      
             setcookie('cart', $imageId );

             
         return new JsonResponse(['cookieContent' => $_COOKIE['cart']]);
         }


    }
    

      


           
    }
    

    
    /**
     * @Route("/event/seeCart", name="seeCartPath")
     */

    function checkout(){

        setcookie('cart', '80, 81');
        $cart = $_COOKIE['cart'];

        $cartSrcArray = [];

        
        if (isset($cart)){

            $cart = explode(',' , $cart);
            
            foreach($cart as $imageId){

                $repo = $this->getDoctrine()->getRepository(Image::class);

                $src = $repo->find($imageId)->getSrc();

                $cartSrcArray[] = $src ;

            }

        }
        
        return $this->render('cart/checkout.html.twig', ['cart' => $cartSrcArray]);


    }




    /**
     * @Route("/event/payment", name="checkoutPath")
     */


    function payment(){
        
        
        return $this->render('event/payment.html.twig', ['']);
    }


    
    /**
     * @Route("/event/stripe", name="stripePath")
     */


    function stripe(){
        
        
        return $this->render('cart/stripe.html.twig');
    }



    /**
     * @Route("/event/stripe/payment", name="stripePaymentPath")
     */


    function stripePayment(){


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

   	'amount' => 1000,
    'currency' => 'eur',
     'customer' => $customer->id]);

               };


          return $this->render('event/new.html.twig');
    }






    
}
