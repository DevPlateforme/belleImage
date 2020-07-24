<?php

namespace App\Entity;

use App\Repository\StripeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StripeRepository::class)
 */
class Stripe
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $apiKey;




    public function __construct(string $api_key){

           $this->api_key = $api_key;

      }

    public function api(string $endpoint, array $data){


        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => "https://api.stripe.com/v1/".$endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $this->api_key,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_POSTFIELDS => http_build_query($data)


        ]);

        $response = json_decode(curl_exec($ch));

        curl_close($ch);

        
    if(property_exists($response, 'error')){

        throw new Exception($response->error->message);
    }


        return $response;


    }



    public function getId(): ?int
    {
        return $this->id;
    }


}
