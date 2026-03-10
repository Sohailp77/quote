<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenant;

class CompanySetting extends Model
{
    use HasTenant;

    protected $fillable = ['key', 'value', 'group'];

    protected $casts = [
        'value' => 'array',
    ];

    public static function getTaxConfiguration()
    {
        $setting = self::where('group', 'tax')->where('key', 'configuration')->first();
        return $setting ? $setting->value : [
            'strategy' => 'single',
            'primary_label' => 'Tax',
            'secondary_labels' => [],
        ];
    }

    /**
     * Return a flat key => value map of all company-group settings.
     */
    public static function getCompanyProfile(): array
    {
        return self::where('group', 'company')->pluck('value', 'key')->all();
    }

    //get currency symbol
    public static function getCurrencySymbol(): string
    {
        $setting = self::where('group', 'company')->where('key', 'currency_symbol')->first();
        return $setting ? $setting->value : '₹';
    }
}
