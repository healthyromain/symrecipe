<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // Create 50 ingredients
        $ingredients = [];
        for ($i = 1; $i <= 50; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
                ->setQuantity(mt_rand(1, 10) . ' units')
                ->setPrice(mt_rand(1, 199));
            $manager->persist($ingredient);
            $ingredients[] = $ingredient;
        }

        // Create 20 recipes
        for ($i = 1; $i <= 20; $i++) {
            $recipe = new Recipe();
            $recipe->setName($this->faker->words(2, true))
                ->setTime(mt_rand(15, 120))
                ->setNbPersons(mt_rand(1, 8))
                ->setDifficulty(mt_rand(1, 5))
                ->setDescription($this->faker->sentences(3, true))
                ->setPrice(mt_rand(5, 50))
                ->setIsFavorite(mt_rand(0, 1) === 1);
            
            // Add 3-7 random ingredients to each recipe
            $nbIngredients = mt_rand(3, 7);
            $randomIngredients = array_rand($ingredients, $nbIngredients);
            
            // Handle both single and multiple results
            if (!is_array($randomIngredients)) {
                $randomIngredients = [$randomIngredients];
            }
            
            foreach ($randomIngredients as $index) {
                $recipe->addIngredient($ingredients[$index]);
            }
            
            $manager->persist($recipe);
        }

        $manager->flush();
    }
}
