<?php

namespace AppBundle\Security;

use AppBundle\Form\LoginForm;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator {

    use TargetPathTrait;

    private $formFactory;
    private $em;
    private $router;
    private $passwordEncoder;

    public function __construct(FormFactoryInterface $formFactory, EntityManager $em, RouterInterface $router, UserPasswordEncoderInterface $passwordEncoder) {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request) {
        return $request->getPathInfo() == '/login' && $request->isMethod('POST');
    }

    public function getCredentials(Request $request) {
        $form = $this->formFactory->create(LoginForm::class);
        $form->handleRequest($request);

        $data = $form->getData();
        $request->getSession()->set(
                Security::LAST_USERNAME, $data['_username']
        );

        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider) {
        $username = $credentials['_username'];

        return $this->em->getRepository('AppBundle:User')
                        ->findOneBy(['email' => $username]);
    }

    public function checkCredentials($credentials, UserInterface $user) {
        $password = $credentials['_password'];

        if ($this->passwordEncoder->isPasswordValid($user, $password)) {
            return true;
        }

        return false;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        if ($targetPath = $this->getTargetPath($request->getSession(), 'main')){
            return new RedirectResponse($targetPath);
        };
        
        return new RedirectResponse($this->router->generate('homepage'));
    }

    protected function getLoginUrl() {
        return $this->router->generate('security_login');
    }

}
