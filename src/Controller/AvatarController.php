<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvatarController extends AbstractController
{
    #[Route('/avatar/{username}', name: 'generate_avatar')]
    public function generateAvatar(string $username): Response
    {
        $hash = md5(strtolower(trim($username)));
        $avatarUrl = 'https://www.gravatar.com/avatar/' . $hash . '?d=identicon';  
        return $this->redirect($avatarUrl);
    }
}
