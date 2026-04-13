<?php

namespace App\Filament\Resources\Post\RelationManagers;

use App\Models\Post\PostComment;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PostCommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('filament.post.comments_tab');
    }

    public static function getModelLabel(): string
    {
        return __('filament.post.comment_singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.post.comments_tab');
    }

    public function isReadOnly(): bool
    {
        return ! auth()->guard('admin')->user()->can('edit_posts');
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('user'))
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('filament.post.commented_by'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('display_body')
                    ->label(__('filament.post.comment_text'))
                    ->getStateUsing(fn (PostComment $record): string => $record->displayBody())
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([])
            ->recordActions([
                DeleteAction::make()
                    ->label(__('filament.actions.delete'))
                    ->visible(fn (): bool => auth()->guard('admin')->user()->can('edit_posts'))
                    ->action(function (PostComment $record): void {
                        $record->post()->decrement('comments_count');
                        $record->delete();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
