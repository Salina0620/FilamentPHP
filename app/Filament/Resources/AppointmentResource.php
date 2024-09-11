<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Schedule;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Text;
use Filament\Forms\Form;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\ColorEntry;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\ImageEntry;
use Filament\Notifications\DatabaseNotification;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

 
class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            // Display patient's name as read-only for patients
            Forms\Components\Placeholder::make('patient_name')
            ->label('Patient')
            ->content(function () {
                $user = auth()->user();
                $patient = Patient::where('user_id', $user->id)->with('user')->first();
                return $patient ? $patient->user->name : 'No Patient Found';
            })
            ->extraAttributes(['class' => 'form-input']) // Add class for styling
            ->hidden(fn() => auth()->user()->role === 'admin'), // Show only for patients
    
        // Hidden field to submit patient_id for patients
        Forms\Components\Hidden::make('patient_id')
            ->default(function () {
                $user = auth()->user();
                $patient = Patient::where('user_id', $user->id)->first();
                return $patient ? $patient->id : null;
            })
            ->required(fn() => auth()->user()->role === 'patient'), // Required for patients
    
        // Select field for admins to select a patient
        Forms\Components\Select::make('patient_id')
            ->label('Patient')
            ->options(function () {
                $user = auth()->user();
                if ($user->role === 'admin') {
                    // Admin sees only users with the role of 'patient'
                    return Patient::join('users', 'patients.user_id', '=', 'users.id')
                        ->where('users.role', 'patient')
                        ->pluck('users.name', 'patients.id')
                        ->toArray();
                }
                return [];
            })
            ->required(fn() => auth()->user()->role === 'admin') // Required for admins
            ->hidden(fn() => auth()->user()->role === 'patient') // Hide the select field for patients
            ->extraAttributes(['class' => 'form-select']), // Add class for styling
        
        


                // Forms\Components\Select::make('patient_id')
                //     ->label('Patient')
                //     ->options(function () {
                //         $patients = Patient::with('user')->get();
                //         return $patients->pluck('user.name', 'id')->filter(function ($name) {
                //             return !is_null($name);
                //         })->toArray();
                //     })
                //     ->required(),
                Forms\Components\Select::make('department_id')
                    ->label('Department')
                    ->options(function () {
                        return Department::all()->pluck('name', 'id')->toArray();
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('doctor_id', null);
                    }),

                Forms\Components\Select::make('doctor_id')
                    ->label('Doctor')
                    ->options(function (callable $get) {
                        $departmentId = $get('department_id');
                        if ($departmentId) {
                            return Doctor::where('department_id', $departmentId)
                                ->with('user')
                                ->get()
                                ->pluck('user.name', 'id')
                                ->filter(function ($name) {
                                    return !is_null($name);
                                })->toArray();
                        }
                        return [];
                    })
                    ->required()

                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('schedule_id', null);
                    }),

             
                Forms\Components\DateTimePicker::make('date_time')
                    ->required(),

                // Table to display schedules
                Forms\Components\Placeholder::make('schedules_table')
                    ->label('Doctor’s Schedules')
                    ->content(function (callable $get) {
                        $doctorId = $get('doctor_id');
                        if ($doctorId) {
                            $schedules = Schedule::where('doctor_id', $doctorId)->get();
                            return view('partials.schedules_table', ['schedules' => $schedules]);
                        }
                        return '';
                    }),


            ]);;
    }
    public static function table(Table $table): Table
    {
        $user = Auth::user();
        $isDoctor = $user->hasRole('doctor');
        $isPatient = $user->hasRole('patient');
        //$doctorId = $isDoctor ? Doctor::where('user_id', $user->id)->value('id') : null;
        return $table
            ->modifyQueryUsing(function (Builder $query) use ($isDoctor, $isPatient, $user) {
                if ($isDoctor) {
                    $doctorId = Doctor::where('user_id', $user->id)->value('id');
                    $query->where('doctor_id', $doctorId);
                } elseif ($isPatient) {
                    $patientId = Patient::where('user_id', $user->id)->value('id');
                    $query->where('patient_id', $patientId);
                } elseif (auth()->user()->role === 'admin') {
                    // Admins can see all appointments
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('patient.user.name')
                    ->label('Patient Name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('doctor.user.name')
                    ->label('Doctor Name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Deparmtent Name')
                    ->sortable(),
                // Tables\Columns\TextColumn::make('status')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('date_time')
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
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->button(),
                Tables\Actions\Action::make('Reshedule')

                    ->visible(fn($record) => auth()->user()->role === 'doctor')
                    ->form(function ($record) {
                        return [
                            DateTimePicker::make('date_time')
                                ->default($record->date_time)
                            // ->native(false)
                        ];
                    })
                    ->action(function ($record, $data) {
                        $previouse_date_time = $record->date_time;
                        $record->date_time = $data['date_time'];
                        $record->save();
                        Notification::make()
                            ->title('Appointment Rescheduled')

                            ->body("Hello "   . $record->patient->user->name . " your appointment scheduled for " . $previouse_date_time . " with Dr." . $record->doctor->user->name . " has been rescheduled for " . $record->date_time .
                                "Apologies for the inconvenience. Please check the new time. Thank you!")
                            ->success()
                            ->duration(10)

                            ->sendToDatabase($record->patient->user);
                        event(new DatabaseNotificationsSent($record->patient->user));
                    })
                    ->icon('heroicon-m-clock')
                    ->color('warning')
                    ->button(),
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    // ->color('')
                    ->button()

            ])
            ->filters([
                //
                SelectFilter::make('doctor_id')
                    ->label('Doctor')
                    ->options(function () {
                        return Doctor::query()
                            ->with('user')
                            ->get()
                            ->pluck('user.name', 'id')
                            ->filter(function ($name) {
                                return !is_null($name);
                            })
                            ->toArray();
                    }),
                SelectFilter::make('patient_id')
                    ->label('Patient')
                    ->options(function () {
                        return Doctor::query()
                            ->with('user')
                            ->get()
                            ->pluck('user.name', 'id')
                            ->filter(function ($name) {
                                return !is_null($name);
                            })
                            ->toArray();
                    }),

                Filter::make('date_time')
                    ->form([
                        DatePicker::make('date_time')
                            ->label('Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            !empty($data['date_time']),
                            fn(Builder $query) => $query->whereDate('date_time', '=', Carbon::parse($data['date'])->format('Y-m-d'))
                        );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if (!empty($data['date_time'])) {
                            $selectedDate = Carbon::parse($data['date_time'])->toFormattedDateString();
                            $indicators[] = Indicator::make('Appointment on ' . $selectedDate)
                                ->removeField('date_time');
                        }

                        return $indicators;
                    })

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])

        ;
    }
    //
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Appointment Details')
                    ->schema([
                        TextEntry::make('patient.user.name')
                            ->label('Patient Name')
                            ->icon('heroicon-m-user')
                            ->color('primary'),
                        TextEntry::make('doctor.user.name')
                            ->label('Doctor Name')
                            ->icon('heroicon-m-user')
                            ->color('primary'),
                        TextEntry::make('department.name')
                            ->label('Department Name')
                            ->icon('heroicon-o-home')
                            ->color('primary'),
                       
                        TextEntry::make('date_time')
                            ->icon('heroicon-m-calendar')
                            ->color('primary')
                            ->label('Appointment Date & Time')
                            ->dateTime(),
                    ]),
                Section::make('Metadata')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->icon('heroicon-m-calendar')
                            ->color('primary')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->icon('heroicon-m-calendar')
                            ->color('primary')

                            ->dateTime(),
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'view' => Pages\ViewAppointment::route('/{record}'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}