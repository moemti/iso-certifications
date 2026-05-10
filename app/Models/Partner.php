<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_name',
        'cui',
        'trade_register_number',
        'address',
        'city',
        'county',
        'postal_code',
        'phone',
        'email',
        'contact_person',
        'bank_account',
        'bank_name',
        'notes',
    ];

    protected $dates = ['deleted_at'];

    public static function rules($id = null)
    {
        $cuiRule = 'required|integer|digits:10|unique:partners,cui';
        if ($id) {
            $cuiRule .= ',' . $id;
        }

        return [
            'company_name' => 'required|string|max:255',
            'cui' => $cuiRule,
            'trade_register_number' => 'nullable|integer',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'county' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'contact_person' => 'required|string|max:255',
            'bank_account' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
