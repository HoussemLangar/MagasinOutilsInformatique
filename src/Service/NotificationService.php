<?php
namespace App\Service;

use App\Entity\Article;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class NotificationService
{
    private $mailer;
    private $twig;
    private $tokenStorage;

    public function __construct(MailerInterface $mailer, Environment $twig, TokenStorageInterface $tokenStorage)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
    }

    public function sendLowStockAlert(Article $article)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        if ($user && method_exists($user, 'getEmail')) {
            $email = (new Email())
                ->from('houssemlangar3@gmail.com')
                ->to('houssemlangar17@gmail.com')
                ->subject('Stock Alert: Low Quantity for ' . $article->getReference())
                ->html($this->twig->render('emails/low_stock_alert.html.twig', [
                    'article' => $article,
                    'user' => $user,
                ]));

            try {
                $this->mailer->send($email);
            } catch (\Exception $e) {
                throw new \Exception('Failed to send email: ' . $e->getMessage());
            }
        } else {
            throw new \Exception('User not found or user email method not available');
        }
    }
}

 
