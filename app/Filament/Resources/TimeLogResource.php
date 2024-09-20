<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeLogResource\Pages;
use App\Filament\Resources\TimeLogResource\RelationManagers;
use App\Models\Department;
use App\Models\Project;
use App\Models\Subproject;
use App\Models\TimeLog;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;


class TimeLogResource extends Resource
{
    protected static ?string $model = TimeLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Select User')
                    ->required()
                    ->options(User::all()->pluck('name', 'id')->toArray())
                    ->reactive(),
                Select::make('project_id')
                    ->label('Select Project')
                    ->required()
                    ->options(Project::all()->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('subproject_id', null)),

                Select::make('subproject_id')
                    ->label('Select Sub-Project')
                    ->required()
                    ->options(function (callable $get) {
                        $project = Project::find($get('project_id'));
                        if (!$project) {
                            return Subproject::all()->pluck('name', 'id');
                        }
                        return $project->subprojects->pluck('name', 'id');
                    })
                    ->reactive(),

                Select::make('department_id')
                    ->label('Select Department')
                    ->required()
                    ->options(Department::all()->pluck('name', 'id')->toArray())
                    ->reactive(),

                DatePicker::make('date')
                    ->label('Select Date')
                    ->required() // Makes the field required
                    ->placeholder('Select a date')
                    ->default(now()), // Set default value to current date

                TimePicker::make('start_time')
                    ->seconds(false)
                    ->label('Start Time')
                    ->format('H:i:s')
                    ->required()
                    ->placeholder('Select start time')
                    ->default(now()->startOfDay())
                    ->reactive()
                    ->afterStateUpdated(function (Get $get,$set, $state) {
                        $end_time = $get('end_time');
                        $startTime = Carbon::parse($state); // Parse the start time
                        $endTime = Carbon::parse($end_time);   // Parse the end time

                        $hours = $startTime->diffInHours($endTime); // Get the difference in hours
                        $set('total_hours', $hours);
                    }), // Set default start time to midnight

                TimePicker::make('end_time')
                    ->seconds(false)
                    ->label('End Time')
                    ->format('H:i:s')
                    ->required()
                    ->placeholder('Select end time')
                    ->default(now()->endOfDay())->reactive()
                    ->afterStateUpdated(function (Get $get,$set, $state) {
                        $start_time = $get('start_time');
                        $startTime = Carbon::parse($start_time); // Parse the start time
                        $endTime = Carbon::parse($state);   // Parse the end time

                        $hours = $startTime->diffInHours($endTime); // Get the difference in hours
                        $set('total_hours', $hours);
                    }), // Set default end time to the end of the day

                Forms\Components\TextInput::make('total_hours')
                    ->required()
                    ->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subproject_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time'),
                Tables\Columns\TextColumn::make('end_time'),
                Tables\Columns\TextColumn::make('total_hours')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
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
            'index' => Pages\ListTimeLogs::route('/'),
            'create' => Pages\CreateTimeLog::route('/create'),
            'edit' => Pages\EditTimeLog::route('/{record}/edit'),
        ];
    }
}
