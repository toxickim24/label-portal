<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ContactTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
    ];

    /**
     * Get all contacts with this tag.
     */
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'contact_tag');
    }
}
