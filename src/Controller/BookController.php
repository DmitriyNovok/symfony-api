<?php

namespace App\Controller;

use App\Model\BookListResponse;
use App\Model\ErrorResponse;
use App\Service\BookService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    public function __construct(private BookService $bookService)
    {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Returns books inside a category",
     *
     *     @Model(type=BookListResponse::class)
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Book category not found",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(
        path: '/api/v1/category/{id}/books',
        methods: 'GET'
    )]
    public function bookByCategory(int $id): Response
    {
        return $this->json(
            $this->bookService->getBookByCategory($id)
        );
    }
}
