<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\UserResource\Pages;
use App\Filament\Client\Resources\UserResource\RelationManagers;
use App\Models\Role;
use App\Models\User;
use Filament\Actions\Modal\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = "Filament Shield";
    protected static ?string $navigationLabel   = 'Staff';
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\Fieldset::make('Profile Image')
                        ->schema([
                            Forms\Components\FileUpload::make('profile_image')
                            ->image()
                            ->imageEditor()
                            ->columnSpan(2),
                        ])->columns(4),
                    // Personal Information Section
                    Forms\Components\Fieldset::make('Personal Information')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('First Name')
                                ->required()
                                ->maxLength(50)
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('last_name')
                                ->label('Last Name')
                                ->required()
                                ->maxLength(50)
                                ->columnSpan(1),
                            Forms\Components\DatePicker::make('date_of_birth')
                                ->required()
                                ->label('Date of Birth')
                                ->columnSpan(1),
                            Forms\Components\Select::make('gender')
                                ->label('Gender')
                                ->options([
                                    'male' => 'Male',
                                    'female' => 'Female',
                                    'others' => 'Others',
                                ])
                                ->required()
                                ->default('none')
                                ->placeholder('Select Gender')
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('personal_contact_number')
                                ->label('Personal Contact Number')
                                ->required()
                                ->numeric()
                                ->tel(),
                            Forms\Components\TextInput::make('emergency_contact_number')
                                ->label('Emergency Contact Number')
                                ->numeric()
                                ->tel(),
                            Forms\Components\TextInput::make('adhar_card_number')
                                ->label('Adhar Card Number')
                                ->numeric(),
                            Forms\Components\TextInput::make('pan_card_number')
                                ->label('Pan Card Number')
                                ->autocapitalize()
                                ->string(),
                            Forms\Components\TextInput::make('address')
                                ->required()
                                ->maxLength(200)
                                ->columnSpan(4),

                        ])
                        ->columns(4),

                    // Employment Details Section
                    Forms\Components\Fieldset::make('Employment Details')
                        ->schema([
                            Forms\Components\DatePicker::make('hire_date')
                                ->label('Hire Date')
                                ->required()
                                ->columnSpan(1),
                            Forms\Components\Select::make('probation_period')
                                ->label('Probation Period')
                                ->options([
                                    'none' => 'None',
                                    '90' => '90 days',
                                    '120' => '120 days',
                                    '180' => '180 days',
                                ])
                                ->required()
                                ->default('none')
                                ->placeholder('Select Probation Period')
                                ->columnSpan(1),
                                Forms\Components\Select::make('position')
                                ->label('Select Position')
                                ->searchable()
                                ->required()
                                ->default('none')
                                ->placeholder('Select Position')
                                ->columnSpan(2),
                        ])
                        ->columns(4),

                    // Account Details Section
                    Forms\Components\Fieldset::make('Account Details')
                        ->schema([
                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('password')
                                ->password()
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(1),
                            Forms\Components\Select::make('roles')
                                ->required()
                                ->searchable()
                                ->relationship('roles', 'name') // Define relationship if applicable
                                ->options(Role::all()->pluck('name', 'id')->toArray())
                                ->preload()
                                ->label('Roles')
                                ->columnSpan(1),
                        ])
                        ->columns(4),



                ]),
            ])
            ->columns(4);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('roles.name')
                    ->label('Roles')
                    ->colors([
                        'primary'  => fn($state): bool => $state === 'Admin',
                        'secondary' => fn($state): bool => $state === 'panel',
                        // Add custom color logic if needed
                    ])
                    ->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
