<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
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

        $adminUser= new User();
        $adminUser->setFirstName('Nicolas')
            ->setLastName("Duplaix")
            ->setEmail('nduplaix62@gmail.com')
            ->setHash($this->encoder->encodePassword($adminUser,"azerty"))
            ->setPicture("http://placehold.it/30x30")
            ->setIntroduction($faker->sentence())
            ->setDescription($content = "<p>" . join("</p><p>" , $faker->paragraphs(3)) . "</p>")
            ->addUserRole($role);

        $manager->persist($adminUser);

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

            //gestion des réservations

            for ($j = 0; $j <= mt_rand(0, 10); $j++)
            {
                $booking = new Booking();
                $createdAt = $faker->dateTimeBetween('-6 mounths');
                $startDate = $faker->dateTimeBetween('-3 mounths');
                $duration = mt_rand(3,10);
                $endDate = (clone $startDate)->modify("+$duration days");
                $amount = $ad->getPrice()*$duration;
                $booker = $users[mt_rand(0, count($users)-1 )];
                $comment = $faker->paragraph();

                $booking->setAd($ad)
                    ->setAmount($amount)
                    ->setBooker($booker)
                    ->setCreatedAt($createdAt)
                    ->setEndDate($endDate)
                    ->setStartDate($startDate)
                    ->setComment($comment);

                $manager->persist($booking);
            }

            $manager->persist($ad);
        }
        $manager->flush();
    }
}
