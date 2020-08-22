<?php

namespace App\Dashboard;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public static function get($key)
    {
        $setting = Setting::where('key', $key)->first();
        if(!$setting) {
            return '';
        }
        return $setting->value;
    }
}
