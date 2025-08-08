<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PetRegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_pet_register')]
    public function __invoke(): Response
    {
        return $this->render('pet/register.html.twig');
    }
}
