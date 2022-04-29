<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductItem;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart_index', methods: ['GET'])]
    public function index(Security $security): Response
    {
        return $this->render('cart/index.html.twig', [
            'products_added' => $security->getUser()->getProductItems()
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_addProduct', methods: ['POST'])]
    public function addProductToCart(Product $product, ManagerRegistry $doctrine, Security $security, Request $request): Response
    {
        $em = $doctrine->getManager();

        $productItem = new ProductItem();
        $productItem->setProduct($product)
                    ->setQuantity($request->request->get('quantity'))
                    ->setUser($security->getUser());
        $em->persist($productItem);

        $security->getUser()->addProductItem($productItem);
        $em->persist($security->getUser());
        
        $em->flush();

        return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/remove/{id}', name: 'app_cart_removeProduct', methods: ['GET'])]
    public function removeProductToCart(ProductItem $productItem, Security $security, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $em->remove($productItem);
        $em->persist($security->getUser());
        $em->flush();

        return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    }
}
