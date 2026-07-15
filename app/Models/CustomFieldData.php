<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFieldData extends Model
{
    protected $fillable = ['custom_field_id', 'model_type', 'model_id', 'value'];

    protected $casts = ['value' => 'array'];

    public function customField() { return $this->belongsTo(CustomField::class); }
    public function model() { return $this->morphTo(); }
}
