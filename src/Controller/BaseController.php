<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class BaseController extends AbstractController
{
    public function renderHeader(CategoryRepository $categoryRepository, Security $security): Response
    {
        $cartSize = ($this->isGranted('IS_AUTHENTICATED_FULLY') ? count($security->getUser()->getProductItems()) : 0);

        return $this->render('header.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'cartSize' => $cartSize
        ]);
    }
}
