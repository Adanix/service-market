<?php

namespace Database\Seeders;

use App\Models\County;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CountySeeder extends Seeder
{
    public const COUNTIES = [
        'Carlow', 'Cavan', 'Clare', 'Cork', 'Donegal', 'Dublin', 'Galway',
        'Kerry', 'Kildare', 'Kilkenny', 'Laois', 'Leitrim', 'Limerick',
        'Longford', 'Louth', 'Mayo', 'Meath', 'Monaghan', 'Offaly',
        'Roscommon', 'Sligo', 'Tipperary', 'Waterford', 'Westmeath',
        'Wexford', 'Wicklow',
    ];

    public function run(): void
    {
        foreach (self::COUNTIES as $county) {
            County::updateOrCreate(
                ['slug' => Str::slug($county)],
                [
                    'name' => $county,
                    'is_active' => true,
                ]
            );
        }
    }
}
