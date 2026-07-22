<?php
namespace Database\Seeders;
use App\Models\Department;
use Illuminate\Database\Seeder;
class DepartmentSeeder extends Seeder {
    public function run(): void {
        foreach ([['name' => 'Pogrebno', 'code' => 'pogrebno'], ['name' => 'JNA', 'code' => 'jna'], ['name' => 'Cvecara', 'code' => 'cvecara']] as $d) {
            Department::firstOrCreate(['code' => $d['code']], $d);
        }
    }
}
