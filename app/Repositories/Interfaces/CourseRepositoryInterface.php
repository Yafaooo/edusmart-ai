<?php

namespace App\Repositories\Interfaces;

interface CourseRepositoryInterface
{
    /**
     * Mengambil semua kelas berdasarkan ID Guru.
     */
    public function getAllByTeacherId(int $teacherId);

    /**
     * Mengambil detail kelas berdasarkan ID.
     */
    public function findById(int $courseId);

    /**
     * Membuat kelas baru.
     */
    public function create(array $data);
}
