<?php

namespace App\Filament\Resources\Avatar\Pages;

use App\Filament\Resources\Avatar\AvatarResource;
use App\Models\User\Avatar;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class ListAvatars extends ListRecords
{
    protected static string $resource = AvatarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->createAnother(false)
                ->using(function (array $data, HasActions&HasSchemas $livewire): Avatar {
                    $paths = array_values(array_filter(Arr::wrap($data['images'] ?? [])));

                    if ($paths === []) {
                        throw ValidationException::withMessages([
                            'images' => [__('validation.required', ['attribute' => __('filament.fields.image')])],
                        ]);
                    }

                    return collect($paths)
                        ->map(fn (string $path): Avatar => Avatar::query()->create(['image' => $path]))
                        ->last();
                }),
        ];
    }
}
