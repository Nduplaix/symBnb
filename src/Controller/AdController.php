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
     * modifier une annonce
     *@Route("/ads/{slug}/edit", name="ads_edit")
     *@Route("/ads/new", name="ads_new")
     * 
     * @return Response
     */
    public function edit(Request $request, Ad $ad=null, ObjectManager $manager){

        if ($ad == null){
            $ad=new Ad();
            $edit=false;
        }
        else{
            $edit=true;
        }

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
            "formulaire"=>$form->createView(), "ad"=>$ad, 'edit'=>$edit
        ]);


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
