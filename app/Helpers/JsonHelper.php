<?php

namespace App\Helpers;

class JsonHelper
{
    public static function jsonStoreSuccess()
    {
        return self::json(__('msg.store_ok'));
    }

    public static function json($msg, $ok = TRUE, $arr = [])
    {
        return $arr ? response()->json($arr) : response()->json([ 'ok' => $ok, 'msg' => $msg ]);
    }

    public static function jsonUpdateSuccess()
    {
        return self::json(__('msg.update_ok'));
    }
}
