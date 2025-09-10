<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SiteImage extends Model
{
    use HasUlids;

    protected $fillable = [
        'site_imageable_type',
        'site_imageable_id',
        'file_path',
        'file_size',
        'captured_at',
    ];

    public function site_imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
