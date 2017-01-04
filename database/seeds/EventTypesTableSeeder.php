<?php

use Illuminate\Database\Seeder;

class EventTypesTableSeeder extends Seeder
{
    private static $common_event_types = array(
        "Негативные и навязчивые мысли",
        "Навязчивые и негативные воспоминания",
        "Неправильные поступки",
        "Слова паразиты",
        "Вредные привычки",
        "Грехи",
        "Обиды",
        "Болезни",
        "Лень",
        "Невнимательность",
        "Ошибки",
        "Чувство вины",
        "Психологические травмы",
        "Чужие и навязанные извне идеи",
        "Воздействия общепринятого мнения и моды",
        "Обжорство",
        "Инфантилизм",
        "Вампиризм",
        "Претензии к себе",
        "Претензии к другим",
        "Пугающие мысли о смерти",
        "Пугающие мысли о старении",
        "Мысли о лишнем и недостаточном весе",
        "Недовольство своей внешностью"
    );

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('event_types')->truncate();
        $common  = self::$common_event_types;
        foreach ($common as $event_type){
            DB::table('event_types')->insert([
                'description' => $event_type
            ]);
        }
    }
}