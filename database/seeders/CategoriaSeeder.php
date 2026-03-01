<?php

namespace Database\Seeders;

use App\Models\Categoria\Category;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Mariscos',
                'type'        => 'food',
                'description' => 'Pescados y mariscos frescos',
                'is_active'   => true,
            ],
            [
                'name'        => 'Cervezas',
                'type'        => 'beverage',
                'description' => 'Cervezas nacionales e importadas',
                'is_active'   => true,
            ],
            [
                'name'        => 'Refrescos',
                'type'        => 'beverage',
                'description' => 'Bebidas carbonatadas y gaseosas',
                'is_active'   => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('Categorías creadas: ' . count($categories));
    }
}