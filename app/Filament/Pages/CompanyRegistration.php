<?php

namespace App\Filament\Pages;

use App\Models\Country;
use App\Models\State;
use App\Models\Tenant;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\SimplePage;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class CompanyRegistration extends SimplePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.company-registration';
    // protected static ?string $title = 'A Step closer to managing your company effectively.';
    protected ?string $heading = 'SOMS Registration';
    protected ?string $maxContentWidth = 'full';
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
                            ->suffix('.'.config('tenancy.central_domains')[0])
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
                                Select::make('country_id')
                                    ->label('Country')
                                    ->required()
                                    ->options(Country::all()->pluck('name', 'id'))
                                    ->afterStateUpdated(fn(callable $set) => $set('state_id', null))
                                    ->reactive()
                                    ->searchable(),
                                Select::make('state_id')
                                    ->label('State')
                                    ->options(fn (callable $get) => State::where('country_id', $get('country_id'))->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                            ])
                    ]),
                Step::make('Business Details')
                    ->completedIcon('heroicon-m-hand-thumb-up')
                    ->icon('heroicon-m-clipboard-document-list')
                        ->schema([
                           
                        Grid::make()
                            ->columns(2)
                            ->schema([
                                FileUpload::make('company_logo')
                                ->label('Upload Logo')
                                ->image()
                                ->required()
                                ->directory('uploads/company/logo'),
                                Select::make('area_of_business')
                                    ->label('Area of Business')
                                    ->required()
                                    ->searchable()
                                    ->options([
                                        "Construction" => "Construction",
                                        "Consulting" => "Consulting",
                                        "Education" => "Education",
                                        "Finance" => "Finance",
                                        "Healthcare" => "Healthcare",
                                        "Hospitality" => "Hospitality",
                                        "Real Estate" => "Real Estate",
                                        "Retail" => "Retail",
                                        "IT Service" => "IT Service",
                                        "Others" => "Others",
                                    ])
                            ])

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

    public function create()
    {
        $input = $this->form->getState()['data'];
        $tenant = new Tenant();
        $tenant->name = $input['name'];
        $tenant->email = $input['email'];
        $tenant->password = $input['password'];
        $tenant->mobile_number = $input['mobile_number'];
        $tenant->landline_number = $input['landline_number'];
        $tenant->company_address = $input['company_address'];
        $tenant->company_owner_name = $input['company_owner_name'];
        $tenant->owner_email = $input['owner_email'];
        $tenant->country_id = $input['country_id'];
        $tenant->state_id = $input['state_id'];
        $tenant->company_logo = $input['company_logo'];
        $tenant->area_of_business = $input['area_of_business'];
        $tenant->save();
        $tenant->domains()->create([
            'domain' => $this->data['domain'].".".config('tenancy.central_domains')[0],
        ]);

        Notification::make()
            ->title('Success')
            ->success()
            ->body('Your registration was successfully please log in.')
            ->send();

        return redirect()->to("http://" . $this->data['domain'] . ".".config('tenancy.central_domains')[0]."/client/login");
    }

    // public function getMaxContentWidth(): MaxWidth
    // {
    //     return MaxWidth::FiveExtraLarge; 
    // }
}
