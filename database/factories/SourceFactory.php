<?php

namespace ChrisHardie\Feedmaker\Database\Factories;

use ChrisHardie\Feedmaker\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

class SourceFactory extends Factory
{
    protected $model = Source::class;

    public function definition()
    {
        return [
            'class_name' => $this->faker->word(),
            'source_url' => $this->faker->url(),
            'name' => $this->faker->sentence(),
            'active' => true,
            'frequency' => 60,
        ];
    }
}
