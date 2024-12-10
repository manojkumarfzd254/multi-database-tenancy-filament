<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Pages\SimplePage;
use Livewire\Component;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class CompanyRegistration extends SimplePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.company-registration';
    // protected static ?string $title = 'A Step closer to managing your company effectively.';
    protected ?string $heading = 'SOMS Registration';
    protected ?string $subheading = 'A Step closer to managing your company effectively.';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function getFormSchema(): array
    {
        return [
            Wizard::make([
                Step::make('Details')
                ->completedIcon('heroicon-m-hand-thumb-up')
                ->icon('heroicon-m-building-office-2')
                // ->description('basic Details')
                    ->schema([
                        TextInput::make('domain')
                            ->required()
                            ->prefix('https://')
                            ->suffix('.hr_management.test')
                            ->suffixIcon('heroicon-m-globe-alt'),
                            
                        Grid::make()
                        ->columns(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Company Name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('email')
                                ->label('Company Email')
                                ->required()
                                ->prefixIcon('heroicon-m-envelope')
                                ->email()
                                ->maxLength(255),
                            TextInput::make('password')
                                ->password()
                                ->required()
                                ->revealable()
                                ->maxLength(255),
                            TextInput::make('confirm_password')
                                ->label('Confirm Password')
                                ->password()
                                ->required()
                                // ->confirmed('password')
                                ->same('password')
                                ->revealable(),
                            PhoneInput::make('mobile_number')
                                ->label('Company Mobile Number')
                                ->prefixIcon('heroicon-m-device-phone-mobile')
                                ->defaultCountry('india')
                                ->allowDropdown(true)
                                ->autoPlaceholder('polite')
                                ->required(),
                                // ->maxLength(15),
                            TextInput::make('landline_number')
                                ->label('Landline Number')
                                ->maxLength(20)
                                // ->tel()
                                ->prefixIcon('heroicon-m-phone'),
                        ])
                        
                    ]),
                Step::make('Address')
                ->completedIcon('heroicon-m-hand-thumb-up')
                ->icon('heroicon-m-map-pin')
                    ->schema([
                        TextInput::make('company_address')
                            ->url()
                            ->required()
                            ->prefixIcon('heroicon-m-map-pin'),
                        Grid::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('company_owner_name')
                                    ->label('Company Owner Name')
                                    ->prefixIcon('heroicon-m-user')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('owner_email')
                                    ->label('Owner Email')
                                    ->required()
                                    ->prefixIcon('heroicon-m-envelope')
                                    ->email()
                                    ->maxLength(255),
                            ])
                    ]),
                Step::make('Business Details')
                    ->completedIcon('heroicon-m-hand-thumb-up')
                    ->icon('heroicon-m-clipboard-document-list')
                        ->schema([
                            
                    ]),
            ])
            ->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button
                    type="submit"
                    size="sm"
                >
                    Submit
                </x-filament::button>
            BLADE)))
            ->statePath('data'),
        ];
    }

    public function create(): void
    {
        dd($this->form->getState());
    }
}
