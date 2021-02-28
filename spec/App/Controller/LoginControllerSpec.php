<?php

namespace spec\App\Controller;

use App\Controller\LoginController;
use App\Message\RecoverPasswordEmail;
use App\Repository\UserRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class LoginControllerSpec extends ObjectBehavior
{
    function let(UserRepository $userRepository)
    {
        $this->beConstructedWith($userRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LoginController::class);
    }

    function it_should_return_login
    (
        Request $request,
        AuthenticationUtils $authenticationUtils,
        ContainerInterface $container,
        Environment $environment
    ) {
        $this->setContainer($container);
        $container->has('twig')->willReturn(true);
        $container->get('twig')->willReturn($environment);
        $environment->render(Argument::cetera())->willReturn('foo');

        $this->loginAction($request, $authenticationUtils)->shouldHaveType(Response::class);
    }

    function it_should_call_bus_when_username_exist_in_recover_password(
        Request $request,
        MessageBusInterface $bus,
        UserRepository $userRepository,
        ContainerInterface $container,
        Environment $environment
    ) {
        $this->setContainer($container);
        $container->has('twig')->willReturn(true);
        $container->get('twig')->willReturn($environment);
        $environment->render(Argument::cetera())->willReturn('foo');

        $request->get('_username')->willReturn('foo');
        $userRepository->getPasswordAndEmailByUsername('foo')->willReturn
        (
            [
                0 =>
                    [
                        'username' => 'foo',
                        'email' => 'x@gamil.com',
                        'password' => 'xxx',
                        'status' => true
                    ]
            ]
        );

        $bus->dispatch(Argument::type(RecoverPasswordEmail::class))->shouldBeCalled()->willReturn(new Envelope(Argument::cetera()));

        $this->recoverPassword($request,$bus)->shouldHaveType(Response::class);
    }

    function it_should_call_bus_when_username_not_exist_in_recover_password(
        Request $request,
        MessageBusInterface $bus,
        UserRepository $userRepository,
        ContainerInterface $container,
        Environment $environment
    ) {
        $this->setContainer($container);
        $container->has('twig')->willReturn(true);
        $container->get('twig')->willReturn($environment);
        $environment->render(Argument::cetera())->willReturn('foo');

        $request->get('_username')->willReturn('foo');
        $userRepository->getPasswordAndEmailByUsername('foo')->willReturn(false);

        $bus->dispatch(Argument::type(RecoverPasswordEmail::class))->shouldNotBeCalled();

        $this->recoverPassword($request,$bus)->shouldHaveType(Response::class);
    }
}
