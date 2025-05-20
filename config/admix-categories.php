<?php

return [
    'name' => 'Categories',
    'icon' => 'category',
    'sort' => 100,
    'categories' => [
        [
            'model' => \Agenciafmd\Articles\Models\Article::class,
            'name' => 'Artigos',
            'slug' => 'articles',
            'types' => [
                [
                    'name' => 'Categorias',
                    'slug' => 'categories',
                    'is_nested' => false,
                    'has_description' => true,
                    'image' => [
                        'max_size' => '1024', // 1MB
                        'max_width' => '200',
                        'max_height' => '200',
                        'ratio' => 1,
                    ],
                ],
            ],
        ],
    ],
];
