<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentReportReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_report_id',
        'admin_id',
        'message',
    ];

    public function report()
    {
        return $this->belongsTo(IncidentReport::class, 'incident_report_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
