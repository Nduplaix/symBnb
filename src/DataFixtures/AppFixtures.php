<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Role;
use App\Entity\User;
use Faker\Factory;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;
    }


    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        $role = new Role();
        $role->setTitle('ROLE_ADMIN');
        $manager->persist($role);

        $users=[];
        $genres=['male', 'female'];

        for ($i = 0 ; $i<10; $i++){
            $user=new User();

            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId=$faker->numberBetween(1,99). '.jpg';

            $picture.=($genre == 'male' ? 'men/' : 'women/').$pictureId;

            $hash=$this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence())
                ->setDescription($content = "<p>" . join("</p><p>" , $faker->paragraphs(3)) . "</p>")
                ->setHash($hash)
                ->setPicture($picture);

            $manager->persist($user);
            $users[]=$user;

        }

        for ($i =0; $i < 30 ; $i++){
            $ad = new Ad();

            $title = $faker->sentence();
            $image = $faker->imageUrl(1000,350);
            $intro = $faker->paragraph(2);
            $content = "<p>" . join("</p><p>" , $faker->paragraphs(5)) . "</p>";

            $user= $users[mt_rand(0,count($users)-1)];

            $ad->setTitle($title)
                ->setCoverImage($image)
                ->setIntroduction($intro)
                ->setContent($content)
                ->setPrice(mt_rand(40,200))
                ->setRooms(mt_rand(1,6))
                ->setAuthor($user);
            
            for ($j = 0; $j < mt_rand(2,5); $j++){
                $image=new Image();

                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                $manager->persist($image);
            }
        
            $manager->persist($ad);
        }
        $manager->flush();
    }
}
