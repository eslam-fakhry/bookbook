<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['content','reporter_id'];

    public function reportable()
    {
        return $this->morphTo();
    }
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
