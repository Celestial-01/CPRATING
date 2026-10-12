<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Course> */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'course_name_en' => fake()->sentence(3),
            'course_name_th' => fake()->sentence(4),
            'course_code' => fake()->unique()->regexify('CP[0-9]{6}'),
            'description' => fake()->optional()->paragraph(),
            'credits' => 3,
            'major' => 'วิทยาลัยการคอมพิวเตอร์',
            'course_type' => 'วิชาเฉพาะ',
            'image' => null,
            'is_offered' => true,
            'publication_status' => Course::PUBLICATION_PUBLISHED,
        ];
    }

    public function draft(): static
    {
        return $this->state(['publication_status' => Course::PUBLICATION_DRAFT]);
    }

    public function hidden(): static
    {
        return $this->state(['publication_status' => Course::PUBLICATION_HIDDEN]);
    }

    public function notOffered(): static
    {
        return $this->state(['is_offered' => false]);
    }
}
