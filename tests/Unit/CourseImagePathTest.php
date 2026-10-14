<?php

use App\Models\Course;

test('course image URLs from the application are stored as relative paths', function () {
    $course = new Course([
        'image' => 'http://CPRATING.test/storage/courses/example.jpg',
    ]);

    expect($course->getAttributes()['image'])->toBe('courses/example.jpg')
        ->and($course->imageStoragePath())->toBe('courses/example.jpg');
});

test('external course image URLs remain external URLs', function () {
    $course = new Course([
        'image' => 'https://cdn.example.com/courses/example.jpg',
    ]);

    expect($course->getAttributes()['image'])->toBe('https://cdn.example.com/courses/example.jpg')
        ->and($course->imageStoragePath())->toBeNull()
        ->and($course->imageUrl())->toBe('https://cdn.example.com/courses/example.jpg');
});
