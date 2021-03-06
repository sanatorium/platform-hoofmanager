<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsAttributesTable extends Migration {

    protected $attributes = [
        [
            'name' => 'Vyřazené',
            'type' => 'truefalse',
            'description' => 'Vyřazené zvíře',
            'slug' => 'withdrawn',
        ]
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $attributesRepo = app('Platform\Attributes\Repositories\AttributeRepositoryInterface');

        foreach( $this->attributes as $attribute )
        {
            $attributesRepo->firstOrCreate([
                'namespace'   => \Sanatorium\Hoofmanager\Models\Item::getEntityNamespace(),
                'name'        => $attribute['name'],
                'description' => $attribute['description'],
                'type'        => $attribute['type'],
                'slug'        => $attribute['slug'],
                'enabled'     => 1,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $attributesRepo = app('Platform\Attributes\Repositories\AttributeRepositoryInterface');

        foreach ($this->attributes as $attribute) {
            $attributesRepo->where('slug', $attribute['slug'])->delete();
        }

    }

}
