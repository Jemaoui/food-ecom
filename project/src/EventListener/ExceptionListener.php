<?php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'kernel.exception')]
class ExceptionListener extends AbstractController
{
    private $requestStack;
    private $router;

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $router)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        // Récupérer l'exception
        $exception = $event->getThrowable();

        // Si l'exception est de type AccessDeniedException ou autre exception spécifique, personnalisez l'affichage
        if ($exception instanceof \Symfony\Component\Security\Core\Exception\AccessDeniedException) {
            $response = $this->render('error/access_denied.html.twig', [
                'message' => $exception->getMessage(),
            ]);
        } elseif ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $response = $this->render('error/not_found.html.twig', [
                'message' => 'Page not found.',
            ]);
        } else {
            // Gérer d'autres exceptions ici, ou afficher une page d'erreur générique
            $response = $this->render('error/general_error.html.twig', [
                'message' => $exception->getMessage(),
            ]);
        }

        // Remplacer la réponse par notre page d'erreur personnalisée
        $event->setResponse($response);
    }
}
