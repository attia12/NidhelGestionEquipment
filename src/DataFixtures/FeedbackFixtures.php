<?php

namespace App\DataFixtures;

use App\Entity\Feedback;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class FeedbackFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker=Factory::create('fr_FR');
        $years = range(2020, 2024); // Define the range of years
      

        
        for ($i = 0; $i < 50; $i++) {
            $feedback = new Feedback();
            $feedback->setName($faker->name);
            $feedback->setEmail($faker->email);
            $feedback->setMessage($faker->text(200));
            
            // Generate a unique datetime for each feedback
            $year = $faker->randomElement($years);
            $date = $faker->dateTimeBetween("$year-01-01", "$year-12-31");
            $feedback->setCreatedAt($date);

            $feedback->setApproved($faker->boolean);

            $manager->persist($feedback);
        }

        
        $manager->flush();
    }
}
