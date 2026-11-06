<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Outlet extends Model
{
    use HasFactory;

    protected $primaryKey = 'outlet_id';

    public static function boot()
    {
        parent::boot();

        /* Execute when creating a record */
        static::creating(function ($entity) {
            $entity->created_by = Auth::id() ?? $entity->created_by;

            // Generate outlet code upon creation
            $entity->outlet_code = next_reference_code(\App\Models\AutoIncrementMaster::AI_OUTLET_CODE);
        });

        /* Execute when creating/updating a record */
        static::saving(function ($entity) {
            $entity->updated_by = Auth::id() ?? $entity->updated_by;
        });
    }

    /**
     * Get record for created_by user.
     */
    public function createdUser()
    {
        return $this->hasOne(User::class, 'id', 'created_by')
                    ->select('id', 'name', 'username', 'email', 'profile_photo_path', 'user_status');
    }

    /**
     * Get record for updated_by user.
     */
    public function updatedUser()
    {
        return $this->hasOne(User::class, 'id', 'updated_by')
                    ->select('id', 'name', 'username', 'email', 'profile_photo_path', 'user_status');
    }

    /**
     * @return \Illuminate\Support\HtmlString
     */
    public function getOutletDropdown()
    {
        // Let it set default outlet to session
        $session_outlet = auth()->user()->currentSessionOutlet();
        $outlets = auth()->user()->outlets();

        if($outlets->count() === 0 || config('vocomi.settings.outlet.enabled') !== true) {
            return '';
        }

        if($outlets->count() === 1) {
            return new \Illuminate\Support\HtmlString(
                '<li class="nav-item px-3">'
                . '<span class="navbar-text text-muted">'
                . fa_icon('map-marker') . ' ' . $outlets->first()->name
                . '</span>'
                . '</li>'
            );
        }

        $html = '<li class="nav-item dropdown">'
            . '<a class="nav-link dropdown-toggle mr-3" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">'
            . '<span class="d-md-down-none">'
            . fa_icon('map-marker') . ' ' . $session_outlet->name
            . '</span>'
            . '</a>'
            . '<div class="dropdown-menu dropdown-menu-right menu-slidein">';

        foreach($outlets as $outlet) {
            if($outlet->outlet_id == $session_outlet->outlet_id) {
                $html .= '<a class="dropdown-item disabled" tabindex="-1" aria-disabled="true">' . $outlet->name . '</a>';
            } else {
                $html .= '<a class="dropdown-item" href="' . route('outlet.change', ['outlet_id' => $outlet->outlet_id]) . '">'
                . $outlet->name
                . '</a>';
            }
        }

        $html .= '</div></li>';

        return new \Illuminate\Support\HtmlString($html);
    }

    public function outletDropdownJson()
    {
        return self::select('name as text', 'outlet_id as id')->get();
    }
}
