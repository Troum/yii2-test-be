<?php

namespace app\components;

use Yii;

trait RoleTrait
{
    /**
     * @param $userModel
     * @return false|int|string
     */
    private function getUserRoleName($userModel): false|int|string
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser($userModel->id);
        $names = array_keys($roles);
        return reset($names);
    }

    /**
     * @param string $roleName
     * @return bool
     */
    private function isAdmin(string $roleName): bool
    {
        return $roleName === 'admin';
    }

    /**
     * @param string $roleName
     * @return bool
     */
    private function isModerator(string $roleName): bool
    {
        return $roleName === 'moderator';
    }

    /**
     * @param string $roleName
     * @return bool
     */
    private function isCustomer(string $roleName): bool
    {
        return $roleName === 'customer';
    }
}