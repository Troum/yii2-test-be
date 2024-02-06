<?php

use Carbon\Carbon;
use Random\RandomException;
use yii\db\Migration;

/**
 * Class m240203_214321_seed_post_table
 */
class m240203_214321_seed_post_table extends Migration
{

    /**
     * @return void
     * @throws RandomException
     */
    public function safeUp(): void
    {
        $this->insertFakePosts();
    }

    /**
     * @return void
     * @throws RandomException
     */
    private function insertFakePosts(): void
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 50; $i++) {
            $this->insert(
                'post',
                [
                    'title' => $faker->sentence(),
                    'content' => $faker->sentences(15, true),
                    'user_id' => random_int(1,50),
                    'created_at'  => Carbon::now()->format('Y-m-d'),
                    'is_published'  => false,
                    'published_at' => null
                ]
            );
        }
    }
}
