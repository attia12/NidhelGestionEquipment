<?php
namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
        $categories = [
            ['name' => 'Office Equipment', 'description' => 'Equipment used in office environments'],
            ['name' => 'Outdoor Equipment', 'description' => 'Equipment used for outdoor activities'],
            ['name' => 'Fitness Equipment', 'description' => 'Equipment used for fitness and exercise'],
            ['name' => 'Audio-Visual Equipment', 'description' => 'Equipment for audio and visual purposes'],
        ];

        foreach ($categories as $data) {
            $category = new Category();
            $category->setName($data['name']);
            $category->setDescription($data['description']);

            $manager->persist($category);
        }

        $manager->flush();
    }
}
