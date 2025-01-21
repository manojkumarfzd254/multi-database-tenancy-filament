<?php

namespace App\Filament\Client\Pages;

use Filament\Forms\Components\Actions\Action;
use Filament\Actions\SelectAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use App\Models\ClientSettings;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;

class SettingPage extends Page
{
    protected static ?string $navigationIcon    = 'heroicon-o-cog';
    protected static ?string $navigationLabel   = 'Settings';
    protected static string $view = 'filament.client.pages.setting-page';


    public function getHeading(): string
    {
        return __('Settings');
    }

    public $leaveSettings = [
        'casual_leaves' => null,
        'earned_leaves' => null,
        'maternity_leaves' => null,
        'paternity_leaves' => null,
        'carry_forward' => 'none',
        'maximum_carry_forward' => null,
    ];

    public $emailSettings = [
        'server' => null,
        'server_port' => null,
        'server_username' => null,
        'server_password' => null
    ];

    public function mount()
    {
        $getLeaveSettingData = ClientSettings::whereIn('module_name', ['LeaveSetting','EmailSetting'])->get();
        foreach ($getLeaveSettingData as $setting) {
            if($setting->module_name =='LeaveSetting'){
                $this->leaveSettings[$setting->key] = json_decode($setting->value, true);
            }else if($setting->module_name =='EmailSetting'){
                $this->emailSettings[$setting->key] = json_decode($setting->value, true);
            }
        }
    }

    public function getFormSchema(): array
    {
        return [
            Tabs::make('Settings')
                ->tabs([
                    Tabs\Tab::make('Leave Settings')
                        ->schema([
                            Grid::make()
                                ->columns(2) // Two columns for side-by-side layout
                                ->schema([
                                    Section::make('Leave Settings')
                                        ->schema([
                                            Grid::make()
                                                ->columns(2)
                                                ->schema([
                                                    TextInput::make('leaveSettings.casual_leaves')
                                                        ->label('Total Casual/Sick Leaves')
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->placeholder('In Days')
                                                        ->required(),

                                                    TextInput::make('leaveSettings.earned_leaves')
                                                        ->label('Total Earned Leaves')
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Assign monthly')
                                                        ->placeholder('In Days')
                                                        ->required(),

                                                    TextInput::make('leaveSettings.maternity_leaves')
                                                        ->label('Total Maternity Leaves')
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->placeholder('In Days')
                                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Only For Women')
                                                        ->required(),

                                                    TextInput::make('leaveSettings.paternity_leaves')
                                                        ->label('Total Paternity Leaves')
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->placeholder('In Days')
                                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Only For Men')
                                                        ->required(),

                                                    Select::make('leaveSettings.carry_forward')
                                                        ->label('Carry Forward Leaves')
                                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'By Default Set To None')
                                                        ->options([
                                                            'none' => 'None',
                                                            'earned_leaves' => 'Earned Leaves',
                                                        ])
                                                        ->selectablePlaceholder(false)
                                                        ->reactive(),

                                                    TextInput::make('leaveSettings.maximum_carry_forward')
                                                        ->label('Maximum Carry Forward')
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Specify the maximum number of earned leaves to carry forward next year.')
                                                        ->hidden(fn($get) => $get('leaveSettings.carry_forward') !== 'earned_leaves'),
                                                ]),
                                        ])
                                        ->footerActions([
                                            fn(string $operation): Action => Action::make('save')
                                                ->action(function (Section $component) {
                                                    $saveLeaveData = $this->leaveSettings;
                                                    foreach ($saveLeaveData as $key => $value) {
                                                        $prepareData = [
                                                            'module_name' => 'LeaveSetting',
                                                            'key' => $key,
                                                            'value' => json_encode($value),
                                                        ];
                                                        $checkData = Arr::except($prepareData, ['value']);
                                                        ClientSettings::updateOrCreate($checkData, $prepareData);
                                                    }

                                                    Notification::make()
                                                        ->title('Setting Saved successfully')
                                                        ->success()
                                                        ->send();
                                                }),
                                        ]),
                                ]),
                        ]),

                    Tabs\Tab::make('Email Settings')
                        ->schema([
                            Grid::make()
                                ->columns(2)
                                ->schema([
                                    Section::make('Email Settings')
                                        ->schema([
                                            Grid::make()
                                                ->columns(2)
                                                ->schema([
                                                    Select::make('emailSettings.server')
                                                        ->label('Email Server')
                                                        ->options([
                                                            'smtp' => 'SMTP',
                                                            'sendgrid' => 'SendGrid',
                                                            'mailgun' => 'Mailgun',
                                                        ])
                                                        ->required(),

                                                    TextInput::make('emailSettings.server_port')
                                                        ->label('Email Server Port')
                                                        ->numeric()
                                                        ->minValue(0)
                                                        ->required(),

                                                    TextInput::make('emailSettings.server_username')
                                                        ->label('Email Username')
                                                        ->required(),

                                                    TextInput::make('emailSettings.server_password')
                                                        ->label('Email Password')
                                                        ->password()
                                                        ->required(),
                                                ]),
                                        ])
                                        ->footerActions([
                                            fn(string $operation): Action => Action::make('save')
                                                ->action(function (Section $component) {
                                                    $this->validate($this->getValidationRules());
                                                    $saveEmailData = $this->emailSettings;
                                                    foreach ($saveEmailData as $key => $value) {
                                                        $prepareData = [
                                                            'module_name' => 'EmailSetting',
                                                            'key' => $key,
                                                            'value' => json_encode($value),
                                                        ];
                                                        $checkData = Arr::except($prepareData, ['value']);
                                                        ClientSettings::updateOrCreate($checkData, $prepareData);
                                                    }
                                                    Notification::make()
                                                        ->title('Setting Saved successfully')
                                                        ->success()
                                                        ->send();
                                                }),
                                        ]),
                                ]),
                        ]),
                ]),
        ];

    }

    protected function getValidationRules()
    {
        $rules = [
            'emailSettings.server' => 'required',
        ];

        if ($this->emailSettings['server'] === 'smtp') {
            $rules = array_merge($rules, [
                'emailSettings.server' => 'required|string|max:20',
                'emailSettings.server_port' => 'required|numeric|min:0',
                'emailSettings.server_username' => 'required|string|max:50',
                'emailSettings.server_password' => 'required|string|max:50',
            ]);
        }

        if ($this->emailSettings['server'] === 'sendgrid') {
            $rules = array_merge($rules, [
                'emailSettings.sendgrid_api_key' => 'required|string|max:255',
            ]);
        }

        if ($this->emailSettings['server'] === 'mailgun') {
            $rules = array_merge($rules, [
                'emailSettings.mailgun_api_key' => 'required|string|max:255',
            ]);
        }

        return $rules;
    }
}
