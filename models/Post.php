<?php

namespace app\models;

use Carbon\Carbon;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $content
 * @property int $user_id
 * @property boolean $is_published
 * @property string|null $created_at
 * @property string|null $published_at
 *
 * @property User $user
 */
class Post extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['content'], 'string'],
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['title'], 'string', 'max' => 256],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }
    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'id', 'title', 'user_id', 'content', 'is_published', 'published_at',
            'created_at' => function ($model) {
                return Carbon::parse($model->created_at)->format('d.m.Y');
            },
            'published_at' => function ($model) {
                return Carbon::parse($model->created_at)->format('d.m.Y');
            }
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'user_id' => 'Author ID',
            'user' => 'Related user',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return string[]
     */
    public function extraFields(): array
    {
        return ['user'];
    }

    /**
     * Finds a post by id
     *
     * @param int $id
     * @return static|null
     */
    public static function findById(int $id): null|static
    {
        return static::findOne(['id' => $id]);
    }

}
