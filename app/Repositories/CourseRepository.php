<?php

namespace App\Repositories;

use App\Models\Course;
use App\Repositories\Interfaces\CourseRepositoryInterface;

class CourseRepository implements CourseRepositoryInterface
{
    public function getAllByTeacherId(int $teacherId)
    {
        // Menggunakan Eloquent untuk fetch data class & material terkait
        return Course::where('teacher_id', $teacherId)->with('lessons')->get();
    }

    public function findById(int $courseId)
    {
        return Course::findOrFail($courseId);
    }

    public function create(array $data)
    {
        return Course::create($data);
    }
}
