<?php

namespace App\Helpers;

use Hashids\Hashids;

class DisplayMessageHelper
{
    public static function displayError($errors)
    {
        foreach( $errors as $err ) {
            $data[] = $err;
        }
        return '
                <div class="alert alert-danger alert-styled-left alert-dismissible">
									<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
									<span class="font-weight-semibold">Oops!</span> '.
            implode(' ', $data).'
							    </div>
                ';
    }

    public static function displaySuccess($msg)
    {
        return '
            <div class="alert alert-success alert-bordered">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button> '.
            $msg.'  </div>
                ';
    }

    public static function hash($id)
    {
        $date = date('dMY').'TGMA';
        $hash = new Hashids($date, 14);
        return $hash->encode($id);
    }

    public static function decodeHash($str, $toString = true)
    {
        $date = date('dMY').'TGMA';
        $hash = new Hashids($date, 14);
        $decoded = $hash->decode($str);
        return $toString ? implode(',', $decoded) : $decoded;
    }

}
