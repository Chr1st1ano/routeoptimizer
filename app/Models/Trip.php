<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_point',
        'destination',
        'distance_km',
        'duration_minutes',
        'route_type',
        'start_date',
        'end_date',
        'notes',
        'vehicle_type',      // <--- Added for Car/Jeep/Trike
        'traffic_condition'  // <--- Added for Heavy/Light/Moderate
    ];
}