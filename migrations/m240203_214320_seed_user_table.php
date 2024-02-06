<?php

use yii\db\Migration;

/**
 * Class m240203_214320_seed_user_table
 */
class m240203_214320_seed_user_table extends Migration
{
    /**
     * @return void
     * @throws \yii\base\Exception
     */
    public function safeUp(): void
    {
        $this->insertFakeUsers();
    }

    /**
     * @return void
     * @throws \yii\base\Exception
     */
    private function insertFakeUsers(): void
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 50; $i++) {
            $this->insert(
                'user',
                [
                    'username' => $faker->name(),
                    'auth_key' => Yii::$app->security->generateRandomString(12),
                    'password_hash' => Yii::$app->security->generateRandomString(12),
                    'password_reset_token' => Yii::$app->security->generateRandomString(12),
                    'email' => $faker->email(),
                    'status' => 10
                ]
            );
        }
    }
}
