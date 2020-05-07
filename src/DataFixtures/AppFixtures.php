<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0;$i<30;$i++){
            $ville = new Ville();
            $ville->setNom($faker->city);
            $ville->setCodePostal($faker->postcode);
            $manager->persist($ville);
        }
        $manager->flush();


        for($i =0;$i<30;$i++)
        {
            $site = new Site();
            $site->setNom($faker->words(2,true));
            $manager->persist($site);
        }
        $manager->flush();

        for ($i = 0;$i<30;$i++){
            $lieu = new Lieu();
            $lieu->setNom($faker->words(2,true));
            $lieu->setRue($faker->streetAddress);
            $lieu->setLatitude($faker->latitude);
            $lieu->setLongitude($faker->longitude);

            try {
                $lieu->setLieuVille($manager->find(Ville::class, random_int(0, 29)));
            } catch (\Exception $e) {
            }
            $manager->persist($lieu);


        }

        $manager->flush();

        for ($i = 0;$i<100;$i++) {
            $participant = new Participant();
            $participant->setNom($faker->lastName);
            $participant->setPrenom($faker->firstName);
            $participant->setTelephone($faker->phoneNumber);
            $participant->setEmail($faker->email);
            $participant->setActif($faker->boolean);
            $participant->setUsername($faker->userName);
            $participant->setPassword($faker->password);
            try {
                $participant->setSite($manager->find(Site::class,random_int(0,29)));
            } catch (\Exception $e) {
            }
            $manager->persist($participant);
        }
        $manager->flush();

        for ($i = 0;$i<30;$i++){
            $sortie = new Sortie();
            $sortie->setNom($faker->words(3,true));
            $sortie->setDatHeureDebut($faker->dateTime);
            $sortie->setDuree($faker->randomNumber());
            try {
                $sortie->setDateLimiteInscription(new \DateTime($faker->date('d-m-Y')));
            } catch (\Exception $e) {
            }
            $sortie->setNbInscriptionsMax($faker->randomNumber(2));
            $sortie->setInfosSortie($faker->sentences(2,true));
            try {
                $sortie->setSortieEtat($manager->find(Etat::class, random_int(0, 2)));
            } catch (\Exception $e) {
            }

            try {
                $sortie->setOrganisateur($manager->find(Participant::class,random_int(0,99)));
            } catch (\Exception $e) {
            }
            try {
                $sortie->setSite($manager->find(Site::class,random_int(0,29)));
            } catch (\Exception $e) {
            }
            try {
                $sortie->setLieu($manager->find(Lieu::class,random_int(0,29)));
            } catch (\Exception $e) {
            }

        $manager->persist($sortie);
        }
        $manager->flush();

    }
}
