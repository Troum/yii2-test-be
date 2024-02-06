<?php

use yii\db\Migration;
use yii\rbac\DbManager;

/**
 * Class m240203_191319_add_rbac_roles_permissions
 */
class m240203_191319_add_rbac_roles_permissions extends Migration
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function safeUp(): void
    {
        $auth = new DbManager;
        $auth->init();

        $auth->removeAll();

        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'Create a post';
        $auth->add($createPost);

        $publishPost = $auth->createPermission('publishPost');
        $publishPost->description = 'Publish or unpublish a post';
        $auth->add($publishPost);

        $setRole = $auth->createPermission('setRole');
        $setRole->description = 'Add role to user';
        $auth->add($setRole);

        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $createPost);
        $auth->addChild($admin, $publishPost);
        $auth->addChild($admin, $setRole);

        $moderator = $auth->createRole('moderator');
        $auth->add($moderator);
        $auth->addChild($moderator, $publishPost);

        $customer = $auth->createRole('customer');
        $auth->add($customer);
        $auth->addChild($customer, $createPost);
    }

    /**
     * @return void
     */
    public function safeDown(): void
    {
        $auth = new DbManager;
        $auth->init();

        $auth->removeAll();
    }
}
