<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     */
    public function validatePassword(string $attribute): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool|string whether the user is logged in successfully
     * @throws Exception
     */
    public function login(): bool|string
    {
        if ($this->validate()) {
            $user = $this->getUser();

            if (Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0)) {
                return $this->generateAndStoreToken($user->id);
            }
            return false;
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|bool|null
     */
    public function getUser(): User|bool|null
    {
        if ($this->_user === false) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }

    /**
     * @throws Exception
     */
    protected function generateAndStoreToken($userId): ?string
    {
        $token = Yii::$app->security->generateRandomString();
        $expiresAt = time() + (3600 * 24);

        $tokenModel = new Token([
            'user_id' => $userId,
            'token' => $token,
            'type' => 'bearer',
            'expires_at' => $expiresAt,
            'created_at' => time(),
        ]);

        if ($tokenModel->save(false)) {
            return $token;
        }

        return null;
    }
}
