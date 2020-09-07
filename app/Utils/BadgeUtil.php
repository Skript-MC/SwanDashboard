<?php

namespace App\Utils;

class BadgeUtil
{

    public static function getBadge(int $rank)
    {
        switch ($rank)
        {
            case 1:
                return 'Invité';
            case 2:
                return 'Membre';
            case 3:
                return 'Staff';
            case 4:
                return 'Admin';
        }
        return null;
    }

}

