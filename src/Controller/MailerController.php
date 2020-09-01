<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email;
use App\Repository\EventRepository;
use App\Entity\Image;



class MailerController extends AbstractController
{
    /**
     * @Route("/mailer/{emailReceiver}", name="mailerPath")
     */
    public function mailer(MailerInterface $mailer, $emailReceiver, Request $request)
    {
        //get the path of each image of the shopping cart
        if ($request->cookies->get('cart') != null){

            $cart = $request->cookies->get('cart');

            $cart = explode(',' , $cart);

            $cartArray = [];
            
            foreach($cart as $imageId){

                $repo = $this->getDoctrine()->getRepository(Image::class);

                $img = $repo->find($imageId);

                $cartArray[] = $img;

            }
        }

        $email = new Email();

        $email->from('middleweightsoul@gmail.com')
            ->to($emailReceiver)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Voici vos photos!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');
            
            foreach($cartArray as $image){
                $email->attachFromPath($image->getSrc());
            }

        $mailer->send($email);

        return $this->redirectToRoute('adminIndexPath');

    }
}
