<?php
namespace Active\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ImgsFixture extends TestFixture
{
    public $fields = [
        'id' => ['type' => 'integer'],
        'product_id' => ['type' => 'integer'],
        'active' => ['type' => 'integer', 'default' => 0],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
    ];
    public $records = [
        [
            'id' => 1,
            'product_id' => 1,
            'active' => 1
        ],
        [
            'id' => 2,
            'product_id' => 1,
            'active' => 0
        ],
        [
            'id' => 3,
            'product_id' => 4,
            'active' => 0
        ],
    ];
}