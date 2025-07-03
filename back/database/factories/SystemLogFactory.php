<?php

namespace Database\Factories;

use App\Models\SystemLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class SystemLogFactory extends Factory
{
    protected $model = SystemLog::class;

    public function definition()
    {
        return [
            'fk_user' => 1,
            'fk_action' => 1,
            'name_table' => $this->faker->word(),
            'record_id' => $this->faker->randomNumber(),
            'description' => $this->faker->sentence(),
        ];
    }
}