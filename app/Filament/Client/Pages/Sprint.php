<?php

namespace App\Filament\Client\Pages;

use App\Models\Project;
use App\Models\ProjectSprint;
use App\Models\Task;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\ComponentContainer;
use Filament\Tables\Actions\AssociateAction;
use Illuminate\Support\Arr;
use Symfony\Component\CssSelector\Node\FunctionNode;

class Sprint extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'Sprint Tasks';
    protected static string $view = 'filament.client.pages.sprint';

    public $data = [
        'project_name' => null,
    ];
    public $activeProjectId = null;
    public $selectedSprintId = null;
    public $sprints = [];

    public function getFormSchema(): array
    {
        return [
            Grid::make(12)->schema([
                Select::make('data.project_name')
                    ->label('Search Project')
                    ->options(
                        Project::pluck('project_name', 'id')
                    )
                    ->searchable()
                    ->columnSpan(6)
                    ->default($this->activeProjectId)
                    ->suffix('Create Sprint')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {

                        $this->getTableQuery();
                        $this->getTableColumns();
                    })
                    ->createOptionModalHeading('Create New Sprint')
                    ->createOptionForm([
                        TextInput::make('sprint_title')
                            ->label('Sprint Title')
                            ->required()
                            ->maxLength(255),
                        Select::make('status')
                            ->label('Search Project')
                            ->options([
                                'planned' => 'Planned',
                                'in_progress' => 'In-Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'on_hold' => 'On-Hold'
                            ])
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data) {
                        if (!empty($this->activeProjectId)) {
                            $insertSprint = [
                                'project_id' => (int)$this->activeProjectId,
                                'sprint_title' => $data['sprint_title'],
                                'status' => 'planned'
                            ];

                            ProjectSprint::create($insertSprint);
                            Notification::make()
                                ->title('Success')
                                ->body('Sprint Created successfully')
                                ->success()
                                ->duration(3000)
                                ->send();
                            return $this->activeProjectId;
                        } else {
                            Notification::make()
                                ->title('Not Permit')
                                ->body('Please select a project before creating a sprint.')
                                ->danger()
                                ->duration(3000)
                                ->send();
                        }
                    }),
            ]),
        ];
    }


    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns(
                [
                    TextColumn::make('id')->label('Sprint Id')->sortable(),
                    TextColumn::make('project.project_name')->label('Project Name')->searchable(),
                    TextColumn::make('sprint_title')->label('Sprint Title')->searchable(),
                    TextColumn::make('status')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'planned' => 'primary',
                            'in_progress' => 'info',
                            'on_hold' => 'warning',
                            'complete' => 'success',
                            'cancelled' => 'danger',
                        })
                        ->searchable(),
                ]
            )
            ->actions([
                DeleteAction::make()
                    ->label('')
                    ->icon('heroicon-m-trash')
                    ->tooltip('Delete Sprint'),
                Tables\Actions\Action::make('manage_task')
                    ->label('Tasks')
                    ->icon('heroicon-o-plus-circle')
                    ->action(function ($record, array $data) {
                        $taskSequences=0;
                        foreach ($data as $taskData) {
                            $taskSequences++;
                            $updateOrInsertData = [
                                'project_id' => $record->project_id,
                                'project_sprints_id' => $record->id,
                                'task_title' => $taskData['task_title'],
                                // 'task_sequences'=>$taskSequences
                            ];

                            $record->tasks()->updateOrCreate($updateOrInsertData);
                            Notification::make()
                            ->title('Success')
                            ->body('Task has been created')
                            ->success()
                            ->duration(2000)
                            ->send();
                        }
                    })
                    ->form([
                        TableRepeater::make('')
                            ->headers([
                                Header::make('Task List'),
                            ])
                            ->schema([
                                TextInput::make('id')
                                ->hidden()
                                ->disabled(),
                                TextInput::make('task_title')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->deleteAction(function (Action $action) {
                                $action->requiresConfirmation();
                                return $action->before(function (array $arguments, TableRepeater $component,array $state){
                                    $activeId       = $arguments['item'];
                                    $removeEntry    = Arr::pull($state, $activeId);
                                    if($removeEntry['id']){
                                       Task::where('id',$removeEntry['id'])->delete();
                                       Notification::make()
                                       ->title('Success')
                                       ->body('Task has been removed')
                                       ->success()
                                       ->duration(2000)
                                       ->send();
                                    }
                                });
                            })
                            ->columnSpan('full')
                            ->reorderable(false)
                            ->default(fn($record) => $record ? $record->tasks->toArray() : []),
                    ]),
            ])
            ->filters([
                SelectFilter::make('project_id')
                    ->label('Projects')
                    ->options(Project::pluck('project_name', 'id')->toArray()),
            ], layout: FiltersLayout::Modal)->filtersFormWidth(MaxWidth::ExtraSmall);
    }

    public function getTableQuery(): Builder
    {
        $query = ProjectSprint::query()->with('tasks:id,task_title,project_id,project_sprints_id')->orderBy('created_at', 'desc');
        return $query;
    }

    public function updatedDataProjectName($value)
    {
        $this->activeProjectId = $value;
    }
}
