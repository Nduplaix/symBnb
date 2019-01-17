<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads")
     */
    
    public function index(AdRepository $repo)
    {
        //$repo=$this->getDoctrine()->getRepository(Ad::class); //si pas de $repo dans les params

        $ads=$repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    /**
     *permet de créer une annonce
     *@Route("/ads/new", name="ads_new")
     *@IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function create(Request $request, ObjectManager $manager){
        $ad = new Ad;

        $form = $this -> createForm(AdType::class, $ad);

        $form->handleRequest($request);//gere la requete $request

        if ($form->isSubmitted() && $form->isValid())//verifie si le formulaire est soumis ET valide
        {
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }
            $ad->setAuthor($this->getUser());
            $manager-> persist($ad);
            $manager->flush();

            $this->addFlash('success',"l'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistée !");

            return $this->redirectToRoute('ads_show',['slug'=>$ad->getSlug()]); //redirection
        }

        return $this->render('ad/create.html.twig',[
            "formulaire"=>$form->createView(), "ad"=>$ad
        ]);


    }


    /**
     * modifier une annonce
     *@Route("/ads/{slug}/edit", name="ads_edit")
     *
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function edit(Request $request, Ad $ad, ObjectManager $manager){

        $form = $this -> createForm(AdType::class, $ad);

        $form->handleRequest($request);//gere la requete $request

        if ($form->isSubmitted() && $form->isValid())//verifie si le formulaire est soumis ET valide
        {
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }
            $ad->setAuthor($this->getUser());
            $manager-> persist($ad);
            $manager->flush();

            $this->addFlash('success',"l'annonce <strong>{$ad->getTitle()}</strong> a bien été modifiée !");
            
            return $this->redirectToRoute('ads_show',['slug'=>$ad->getSlug()]); //redirection
        }

        return $this->render('ad/edit.html.twig',[
            "formulaire"=>$form->createView(), "ad"=>$ad
        ]);


    }

    /**
     * Permet de supprimer une annonce
     * @param Ad $ad
     * @param ObjectManager $manager
     *
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Vous n'avez pas le droit d'accéder à cette ressource")
     *
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager){
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash("success","L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée");

        return $this->redirectToRoute("ads");
    }


    /**
     * Affiche une seule annonce
     *
     * @Route("/ads/{slug}", name="ads_show")
     * 
     * @return Response
     */
    public function show(Ad $ad){
        //Je recup l'annonce qui correspond au slug
        //$ad=$repo->findOneBySlug($slug);// si pas de Ad dans les param mais un AdRepository $repo et un $slug

        return $this->render('ad/show.html.twig', ['ad'=>$ad , 'user'=>$this->getUser()]);
    }

}
