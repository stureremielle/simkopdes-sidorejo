<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';
    protected $fillable = ['key_name', 'value'];
    public $timestamps = false;

    public static function getValue(string $key, string $default = ''): string
    {
        $setting = self::where('key_name', $key)->first();
        return $setting ? ($setting->value ?? '') : $default;
    }
}
