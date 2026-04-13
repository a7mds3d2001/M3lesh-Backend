<?php

namespace App\Enums\User;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';

    public function label(): string
    {
        return match ($this) {
            self::Male => __('filament.fields.gender_male'),
            self::Female => __('filament.fields.gender_female'),
        };
    }
}
