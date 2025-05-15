<?php

return [
    'name' => 'Categories',
    'icon' => 'category',
    'sort' => 100,
    'categories' => [
        [
            'model' => \Agenciafmd\Products\Models\Product::class,
            'name' => 'Produtos',
            'slug' => 'products',
            'types' => [
                [
                    'name' => 'Categorias',
                    'slug' => 'categories',
                ],
                [
                    'name' => 'Tags',
                    'slug' => 'tags',
                ],
            ],
        ],
        [
            'model' => \Agenciafmd\Admix\Models\User::class,
            'name' => 'Usuários',
            'slug' => 'users',
            'types' => [
                [
                    'name' => 'Categorias',
                    'slug' => 'categories',
                ],
                [
                    'name' => 'Tags',
                    'slug' => 'tags',
                ],
                [
                    'name' => 'Tipos',
                    'slug' => 'types',
                ],
            ],
        ],
        [
            'model' => \Agenciafmd\Articles\Models\Article::class,
            'name' => 'Artigos',
            'slug' => 'articles',
            'types' => [
                [
                    'name' => 'Categorias',
                    'slug' => 'categories',
                ],
            ],
        ],
    ],
];
