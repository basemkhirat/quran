<?php

use Illuminate\Database\Seeder;

class TafasirTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tafasir')->delete();

        $rows = [
            ['name' => 'saadi','caption' => 'السعدي'],
            ['name' => 'waseet','caption' => 'الوسيط للنطنطاوي'],
            ['name' => 'bakhaway','caption' => 'البغوي'],
            ['name' => 'ibn-kaser','caption' => 'ابن كثير'],
            ['name' => 'qurtobi','caption' => 'القرطبي'],
            ['name' => 'tabar','caption' => 'الطبري'],
            ['name' => 'ibn-ashor','caption' => 'ابن عاشور'],
            ['name' => 'earab','caption' => 'إعراب القرآن'],
        ];

        foreach ($rows as $row)
        {
            App\Models\Tafasir::create($row);
        }

    }
}
