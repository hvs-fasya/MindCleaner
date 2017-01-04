<?php

use Illuminate\Database\Seeder;

class SpheresTableSeeder extends Seeder
{
    private static $common_spheres = array(
        "Выживание (еда, тело, дом)",
        "Удовольствие (секс, деньги)",
        "Воля (работа, карьера)",
        "Любовь, радость",
        "Правда, самовыражение",
        "Интуиция, творчество",
        "Духовность, способности, цельность",
    );

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('spheres')->truncate();
        $common  = self::$common_spheres;
        foreach ($common as $sphere){
            DB::table('spheres')->insert([
                'description' => $sphere
            ]);
        }
    }
}