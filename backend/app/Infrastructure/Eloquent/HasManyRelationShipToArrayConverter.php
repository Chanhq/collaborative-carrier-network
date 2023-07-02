<?php

namespace App\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasManyRelationShipToArrayConverter
{
    /**
     * @param HasMany $relationship
     * @return array<Model>
     */
    public function convert(HasMany $relationship): array
    {
        $models = [];

        foreach ($relationship->get() as $model) {
            $models[] = $model;
        }

        return $models;
    }
}
