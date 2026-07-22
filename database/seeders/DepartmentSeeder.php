<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Department;
class DepartmentSeeder extends Seeder {
    public function run(): void {
        $depts = [
            ['name' => 'Pogrebno', 'slug' => 'pogrebno'],
            ['name' => 'JNA', 'slug' => 'jna'],
            ['name' => 'Cvećara', 'slug' => 'cvecara'],
        ];
        foreach ($depts as $dept) {
            Department::firstOrCreate(['slug' => $dept['slug']], $dept);
        }
    }
}
