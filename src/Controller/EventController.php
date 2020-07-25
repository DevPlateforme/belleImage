<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\ImageType;
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
    function showOne($eventId, EntityManagerInterface $manager, Request $request,  SluggerInterface $slugger){
    
        $event= $this->getDoctrine()->getRepository(Event::class)->find($eventId);

        $eventName = $event->getName();


        
        $event->addImage($image= new Image());
        

        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $imageFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('events_directory'). $event->getName(),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $image->setSrc($newFilename);
            }

            // ... persist the $product variable or any other work

            return $this->render('event/new.html.twig');


            
        }

        
        $images = $event->getImages();

        return $this->render('event/showone.html.twig', ['event' => $event , 'images' => $images, 'form' => $form->createView()]);

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

         
         return new JsonResponse(['cookieContent'=>$cookie]);
 
 
         } else {

            $cookie = new Cookie('cart', $imageId, strtotime('+1 day'));
            $response->headers->setCookie($cookie);
            $response->send();

            $cookie = $request->cookies->get('cart'); 

             
         return new JsonResponse(['cookieContent'=>$cookie]);

           }

      }

           
    }
    

    
    /**
     * @Route("/event/seeCart", name="seeCartPath")
     */

    function checkout(){

        $cartSrcArray = [];

        if (isset($_COOKIE['cart'])){

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

            echo $error;
            }


          return $this->render('event/collect.html.twig');

             }

    
}
