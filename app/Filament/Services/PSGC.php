<?php

namespace App\Filament\Services;

use Illuminate\Support\Facades\Storage;

class PSGC
{
    protected static array $provinces = [];
    protected static array $municipalities = [];
    protected static array $barangays = [];

    protected static function loadJson(string $path): array
    {
        if (!Storage::exists($path)) {
            return [];
        }
        $data = json_decode(Storage::get($path), true);
        return is_array($data) ? $data : [];
    }

    public static function getProvinces(): array
    {
        if (empty(static::$provinces)) {
            static::$provinces = static::loadJson('geo/provinces.json');
        }
        return collect(static::$provinces)
            ->mapWithKeys(fn ($province) => [$province['code'] => $province['name']])
            ->toArray();
    }

    public static function getMunicipalities(string $provinceCode): array
    {
        if (empty(static::$municipalities)) {
            static::$municipalities = static::loadJson('geo/municipalities.json');
        }
        if (!isset(static::$municipalities[$provinceCode])) {
            return [];
        }
        return collect(static::$municipalities[$provinceCode])
            ->mapWithKeys(fn ($city) => [$city['code'] => $city['name']])
            ->toArray();
    }

    public static function getBarangays(string $municipalityCode): array
    {
        if (empty(static::$barangays)) {
            static::$barangays = static::loadJson('geo/barangays.json');
        }
        if (!isset(static::$barangays[$municipalityCode])) {
            return [];
        }
        return collect(static::$barangays[$municipalityCode])
            ->mapWithKeys(fn ($barangay) => [$barangay['code'] => $barangay['name']])
            ->toArray();
    }
}
