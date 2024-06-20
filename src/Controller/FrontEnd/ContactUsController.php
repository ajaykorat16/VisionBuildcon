<?php

namespace App\Controller\FrontEnd;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contact', name: 'contact_us')]
class ContactUsController extends AbstractController
{
    #[Route('', name: '_index')]
    public function index(): Response
    {
        return $this->render('front-end/aboutUs/contact.html.twig');
    }
}