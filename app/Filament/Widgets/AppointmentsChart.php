<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB; 
use App\Models\Appointment;
use App\Models\Department;

class AppointmentsChart extends ChartWidget
{
    protected function getData(): array
    {
        // Fetch the number of appointments per department
        $appointmentsByDepartment = Appointment::select(DB::raw('departments.name as department_name'), DB::raw('count(*) as total_appointments'))
            ->join('departments', 'appointments.department_id', '=', 'departments.id')
            ->groupBy('departments.name')
            ->pluck('total_appointments', 'department_name');
        
        // Create a complete range of numbers for the X-axis
        $allNumbers = range(1, max($appointmentsByDepartment->values()->toArray()) + 1);

        // Ensure all numbers are represented in the data
        $data = [];
        foreach ($allNumbers as $number) {
            $data[$number] = $appointmentsByDepartment->filter(function($count) use ($number) {
                return $count == $number;
            })->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Appointments by Department',
                    'data' => array_values($data), 
                    'borderColor' => '#4A90E2', 
                    'backgroundColor' => 'rgba(74, 144, 226, 0.2)', 
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => array_keys($data), 
        ];
    }

    protected function getType(): string
    {
        return 'line';      
    }
    protected static ?string $maxHeight = '1000px';
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'x' => [
                    'display' => true,
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'display' => true,
                    'grid' => [
                        'display' => true,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
        ];
    }

    protected function getWidgetView(): string
    {
        return 'filament.widgets.appointments-chart'; // Adjust the path if necessary
    }
    
}
