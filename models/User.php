<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property int $status
 *
 * @property Post[] $posts
 */
class User extends ActiveRecord implements IdentityInterface
{
    public ?string $password = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user';
    }

    /**
     * @return string[]
     */
    public function fields(): array
    {
        return ['id', 'username', 'email'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['username', 'password', 'email'], 'required'],
            [['status'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status'
        ];
    }

    /**
     * Gets query for [[Posts]].
     *
     * @return ActiveQuery
     */
    public function getPosts(): ActiveQuery
    {
        return $this->hasMany(Post::class, ['user_id' => 'id']);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function register(): bool
    {
        if ($this->validate()) {
            $this->setPassword($this->password);
            $this->generateAuthKey();
            $this->generatePasswordResetToken();
            if ($this->save(false)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public function setPassword($password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @throws Exception
     */
    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @throws Exception
     */
    public function generatePasswordResetToken(): void
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString(12);
    }

    /**
     * @throws \Exception
     */
    public function assignRole($role = 'customer'): void
    {
        $auth = Yii::$app->authManager;
        $userRole = $auth->getRole($role);
        $auth->revokeAll($this->id);
        $auth->assign($userRole, $this->id);
    }

    /**
     * @return void
     */
    public function revokeAllRoles(): void
    {
        $auth = Yii::$app->authManager;
        $auth->revokeAll($this->id);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Finds a user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail(string $email): null|static
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * @param $id
     * @return User|IdentityInterface|null
     */
    public static function findIdentity($id): User|IdentityInterface|null
    {
        return static::findOne($id);
    }

    /**
     * @param $token
     * @param $type
     * @return User|IdentityInterface|null
     */
    public static function findIdentityByAccessToken($token, $type = null): User|IdentityInterface|null
    {
        $userToken = Token::find()
            ->where(['token' => $token])
            ->andWhere(['>', 'expires_at', date('Y-m-d H:i:s')])
            ->one();

        if ($userToken) {
            return static::findOne($userToken->user_id);
        }

        return null;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * @param $authKey
     * @return bool
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }
}
