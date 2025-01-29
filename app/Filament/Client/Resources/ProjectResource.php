<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\ProjectResource\Pages;
use App\Filament\Client\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\LinkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\TextInput::make('project_name')
                        ->label('Project Name')
                        ->required()
                        ->maxLength(50)
                        ->columnSpan(4),

                    Forms\Components\DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required()
                        ->columnSpan(4),
                    Forms\Components\DatePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->columnSpan(4),
                    Forms\Components\FileUpload::make('project_logo')
                        ->image()
                        ->imageEditor()
                        ->columnSpan(4),
                    Forms\Components\FileUpload::make('supported_documents')
                        ->image()
                        ->imageEditor()
                        ->columnSpan(4),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'not-started' => 'Not-Started Yet',
                            'inprogress' => 'In-Progress',
                            'delay' => 'Delay',
                            'complete' => 'Complete',
                            'closed' => 'Closed',
                        ])
                        ->required()
                        ->default('none')
                        ->placeholder('Select Status')
                        ->columnSpan(4),
                    TextInput::make('project_url')
                        ->label('Project Url')
                        ->maxLength(50)
                        ->columnSpan(6),
                    TextInput::make('demo_project_url')
                        ->label('Project Url')
                        ->maxLength(50)
                        ->columnSpan(6),
                    Select::make('assign_to')
                        ->label('Project Assign to')
                        ->multiple()
                        ->options(function () {
                            return User::with('roles') // Eager load roles
                                ->get()
                                ->mapWithKeys(function ($user) {
                                    $roles = $user->roles->pluck('name')->join(', '); // Get roles as a string
                                    return [$user->id => "{$user->name} ({$roles})"];
                                });
                        })
                        ->searchable()->columnSpan(6),
                    RichEditor::make('project_description')->disableToolbarButtons([
                        'attachFiles',
                    ])->columnSpan(12),
                ])
                    ->columns(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project_name')
                    ->searchable(),
                    Tables\Columns\TextColumn::make('project_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->searchable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'not-started' => 'danger',
                        'inprogress' => 'info',
                        'delay' => 'warning',
                        'complete' => 'success',
                        'closed' => 'danger',
                    })
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
