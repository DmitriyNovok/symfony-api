<?php

namespace App\Controller;

use App\Model\ErrorResponse;
use App\Service\RoleService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function __construct(private RoleService $roleService)
    {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Grants ROLE_AUTHOR to a user"
     * )
     * @OA\Response(
     *     response=404,
     *     description="User not found",
     *
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route('/api/v1/admin/grantAuthor/{userId}', methods: 'POST')]
    public function grantAuthor(int $userId): Response
    {
        $this->roleService->grantAuthor($userId);

        return $this->json(null);
    }
}
