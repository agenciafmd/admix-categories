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
                    'has_color' => false,
                    'has_title' => false,
                    'has_description' => true,
                    'has_select' => false,
                    'select' => [
                        'label' => '',
                        'options' => [
                            [
                                'value' => '',
                                'label' => '-',
                            ],
                        ],
                    ],
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
