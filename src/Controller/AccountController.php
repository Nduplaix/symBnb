<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * permet de créer et gérer le formulaire de co
     * @Route("/login", name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error= $utils->getLastAuthenticationError();
        $username=$utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError'=> $error != null,
            'username'=>$username
        ]);
    }


    /**
     * permet de se deconnecter
     *
     * @Route("/logout", name="account_logout")
     * 
     * @return void
     */
    public function logout(){
        //on laisse symfony faire!
    }

    /**
     * permet de créer et gérer un formulaire d'inscription
     *
     * @Route("/register", name="account_register")
     * 
     * @return Response
     */
        public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){
        $user= new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash= $encoder ->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $manager->persist($user);

            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compté a bien été enregistré"
            );

            return $this->redirectToRoute("account_login");
        }

        return $this->render('account/registration.html.twig',[
            'form'=>$form->createView()
        ]);
    }

}
