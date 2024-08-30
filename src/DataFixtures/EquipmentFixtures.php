<?php

namespace App\DataFixtures;

use App\Entity\Equipment;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class EquipmentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Retrieve categories
        $categories = $manager->getRepository(Category::class)->findAll();

        // Ensure there are categories available
        if (empty($categories)) {
            throw new \Exception('No categories found. Please load the CategoryFixtures first.');
        }

        // Create sample equipment
        for ($i = 0; $i < 50; $i++) {
            $equipment = new Equipment();
            $equipment->setName($faker->word);
            $equipment->setDescription($faker->sentence);
            $equipment->setImage($faker->imageUrl);
            $equipment->setAvailabilityStatus($faker->randomElement(['Available', 'Not Available']));
            $equipment->setCategory($faker->randomElement($categories));

            // Optionally, you can set the rating and other fields
            $equipment->setRating($faker->randomFloat(1, 1, 5));

            $manager->persist($equipment);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class, // Ensure CategoryFixtures is loaded first
        ];
    }
}
