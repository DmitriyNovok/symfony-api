<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(
        private BookRepository $bookRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/books')]
    public function root(): Response
    {
        return $this->json(
            $this->bookRepository->findAll()
        );
    }
}
