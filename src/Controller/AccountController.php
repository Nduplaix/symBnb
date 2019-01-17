<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profile
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function profile(Request $request, ObjectManager $manager){
        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données du profil ont bien été enregistrée avec succès!"
            );
        }

        return $this->render('account/profile.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de mot de passe
     *
     * @Route("/account/password-update", name="account_password")
     *@IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager){
        $passwordUpdate = new PasswordUpdate();

        $user= $this->getUser();

        $form= $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            //vérifier que le oldPassword du form soit le meme que le password du user
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())){
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous 
                avez tapé n'est pas le mot de passe actuel"));
            }
            else{
                $newPassword= $passwordUpdate->getNewPassword();

                $hash=$encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);

                $manager->persist($user);

                $manager->flush();


                $this->addFlash('success',"Votre mot de passe a bien été modifié !");


                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render('account/password.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     *
     * @Route("/account", name="account_index")
     *@IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function myAccount(){
        return $this->render('user/index.html.twig', [
            'user'=>$this->getUser()
         ]);
    }

}
