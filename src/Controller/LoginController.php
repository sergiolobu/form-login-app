<?php

namespace App\Controller;

use App\Message\RecoverPasswordEmail;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * @var UserRepository $userRepository
     */
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/", name="login_route")
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'login.html.twig',
            array(
                'last_username' => $lastUsername,
                'error' => $error,
            )
        );
    }

    /**
     * @Route("/recover-pass", name="recover_pass")
     * @param Request $request
     * @param MessageBusInterface $bus
     * @return Response
     */
    public function recoverPassword(Request $request, MessageBusInterface $bus): Response
    {
        $username = $request->get('_username');
        $message = 'User not exist in DB';
        $data = $this->userRepository->getPasswordAndEmailByUsername($username);

        if ($data) {
            $message = 'Sending email: ' . $data[0]['email'];
            $bus->dispatch(new RecoverPasswordEmail($data[0]));
        }

        return $this->render(
            'login.html.twig',
            array(
                'recover_password_message' => $message,
                'last_username' => $username
            )
        );
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
    }
}