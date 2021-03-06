<?php

namespace App\Helpers;

use App\Models\Setting;

class GetSystemInfoHelper
{
    public static function getPanelOptions()
    {
        return '    <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                        <a class="list-icons-item" data-action="remove"></a>
                    </div>
                </div>';
    }

    public static function getNextSession(): string
    {
        $currentSession = self::getCurrentSession();
        $oldYear = explode('-', $currentSession);
        return ++$oldYear[0].'-'.++$oldYear[1];
    }

    public static function getCurrentSession()
    {
        return self::getSetting('current_session');
    }

    public static function getSetting($type)
    {
        return Setting::where('type', $type)->first()->description;
    }

    public static function getSystemName()
    {
        return self::getSetting('system_name');
    }

    public static function getAppCode()
    {
        return self::getSetting('system_title') ?: 'TGMA';
    }

}
