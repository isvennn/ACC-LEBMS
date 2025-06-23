<?php

namespace App\Traits;

trait HasFullName
{
    /**
     * Get the full name of the user.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        $middle = $this->middle_name ? ' ' . $this->middle_name : '';
        $extension = $this->extension_name ? ' ' . $this->extension_name : '';

        return "{$this->first_name}{$middle} {$this->last_name}{$extension}";
    }
}
