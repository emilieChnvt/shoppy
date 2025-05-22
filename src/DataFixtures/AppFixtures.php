<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 30; $i++) {
            $product = new Product();
            $product->setName($faker->word());
            $product->setDescription($faker->text());
            $product->setPrice($faker->randomFloat(2, 10, 100));
            $product->setCreateAt(new \DateTimeImmutable());
            $product->setStock($faker->numberBetween(1, 1000));
            $manager->persist($product);


        }


        $manager->flush();
    }
}
