<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionDetails extends Model
{
    protected $table = 'm_election_details';

    function schedule()
    {
        return $this->belongsTo(Schedule::class, 'SCHEDULEID', 'PHASE_NO');
    }
}
