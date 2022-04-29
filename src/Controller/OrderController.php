<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order_index', methods: ['GET'])]
    public function index(Security $security): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $security->getUser()->getOrders()
        ]);
    }

    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(Security $security, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $order = new Order();
        $datetime = new DateTime('now');
        $order->setDateTime($datetime);
        foreach ($security->getUser()->getProductItems() as $p) {
            $order->addProductItem($p);
            $security->getUser()->removeProductItem($p);
        }
        $em->persist($order);

        $security->getUser()->addOrder($order);
        $em->persist($security->getUser());

        $em->flush();

        return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/show/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
}
