<?php

namespace App\Handler;
/**
 *
 */
class ConfigHandler
{
    public function userField()
    {
        return get_data_user('admins');
    }
}
