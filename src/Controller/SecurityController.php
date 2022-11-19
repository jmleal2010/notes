<?php

namespace App\Controller;

use FOS\UserBundle\Form\Type\RegistrationFormType;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityController extends AbstractController
{
    private $userManager;
    private $tokenStorage;

    public function __construct(UserManagerInterface $userManager, TokenStorageInterface $tokenStorage)
    {
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route(name="security_register",path="/register", methods={"POST", "GET"})
     */
    public function registerAction(Request $request): Response
    {
        $user = $this->userManager->createUser();
        $user->setEnabled(true);

        $form = $this->createForm(RegistrationFormType::class);

        $form->setData($user);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $this->userManager->updateUser($user);

                $url = $this->generateUrl('app_index');
                $response = new RedirectResponse($url);

                return $response;
            }

        }

        return $this->render('@FOSUser/Registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
