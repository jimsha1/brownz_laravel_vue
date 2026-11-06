<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes,HasApiTokens;

    /* Gender */
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    const GENDER_UNSPECIFIED = 'unspecified';

    /* User statuses */
    const USER_STATUS_PENDING = 'pending';
    const USER_STATUS_ACTIVE = 'active';
    const USER_STATUS_INACTIVE = 'inactive';
    const USER_STATUS_BLOCKED = 'blocked';
    const USER_STATUS_DELETED = 'deleted';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'profile_photo_path',
        'date_of_birth',
        'mobile',
        'address',
        'gender',
        'shift_id',
        'user_status',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $user_outlets = null;
    protected $current_user_outlet = null;

    public static function boot()
    {
        parent::boot();

        /* Execute when creating a record */
        static::creating(function ($entity) {
            $entity->created_by = Auth::id() ?? $entity->created_by;
        });

        /* Execute when creating/updating a record */
        static::saving(function ($entity) {
            $entity->updated_by = Auth::id() ?? $entity->updated_by;
        });
    }

    public function getGenders()
    {
        return [
            self::GENDER_MALE,
            self::GENDER_FEMALE,
            self::GENDER_UNSPECIFIED,
        ];
    }

    public function genderDropdown($default = false)
    {
        return voco_scalar_dropdown($this->getGenders(), $default);
    }

    public function getUserStatuses()
    {
        return [
            self::USER_STATUS_PENDING,
            self::USER_STATUS_ACTIVE,
            self::USER_STATUS_INACTIVE,
            self::USER_STATUS_BLOCKED,
            self::USER_STATUS_DELETED,
        ];
    }

    public function userStatusDropdown($default = false)
    {
        return voco_scalar_dropdown($this->getUserStatuses(), $default);
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
     * Set the user's username.
     *
     * @param  string  $value
     * @return void
     */
    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = strtolower($value);
    }

    public function avatarUrl($size = 50, $zoomCrop = true)
    {
        return $this->getAvatarUrl($this, $size, $zoomCrop);
    }

    /**
     *
     * @param \App\User|object $user Require attributes "profile_photo_path" and "email" when passing non-null values
     * @param int $size
     * @param boolean $zoomCrop
     * @return string
     */
    public function getAvatarUrl($user = null, $size = 50, $zoomCrop = true)
    {
        if (empty($size) || $size < 10 || $size > 500) {
            $size = 50;
        }

        if (null === $user) {
            $user = Auth::user();
        }

        $avatar = $user->profile_photo_path;

        if (empty($avatar)) {
            return 'https://robohash.org/' . md5(trim($user->email))
                    . '?gravatar=hashed'
                    . '&set=set3'
                    . '&bgset=bg1'
                    . '&size=' . $size . 'x' . $size;
        }

        if (!empty($avatar) && file_exists('uploads/avatar/' . $avatar)) {
            $image = 'uploads/avatar/' . $avatar;
        } else {
            $image = 'images/default-avatar.png';
        }

        $url = 'components/pt/phpThumb.php?src=../../' . $image . '&w=' . $size . '&h=' . $size;

        if ($zoomCrop === true) {
            $url .= '&zc=1';
        }

        $extension = pathinfo($image, PATHINFO_EXTENSION);
        if (in_array($extension, ['png', 'gif', 'ico'])) {

            // keep transparency
            $url .= '&f=' . $extension;
        }

        return url($url);
    }

    public function outlets()
    {
        if($this->user_outlets === null) {
            if($this->hasRole(\App\Models\AclRole::ROLE_SUPER_ADMIN)) {
                $this->user_outlets = \App\Models\Outlet::get();
            } else {
                $this->user_outlets = \App\Models\Outlet::join('user_has_outlets', 'outlets.outlet_id', '=', 'user_has_outlets.outlet_id')
                    ->where('user_has_outlets.user_id', $this->id)
                    ->select('outlets.*')
                    ->get();
            }
        }

        return $this->user_outlets;
    }

    public function hasOutlet($outlet)
    {
        $outlet_id = ($outlet instanceof \App\Models\Outlet) ? $outlet->$outlet_id: $outlet;

        return in_array($outlet_id, $this->outlets()->pluck('outlet_id')->all());
    }

    public function setSessionOutlet($outlet)
    {
        // Scrap `outlet_id`
        $outlet_id = ($outlet instanceof \App\Models\Outlet) ? $outlet->$outlet_id: $outlet;

        // Check whether user has access to the outlet
        foreach($this->outlets() as $user_outlet) {
            if($user_outlet->outlet_id == $outlet_id) {
                // Once located, save to session
                session(['session_outlet' => $user_outlet]);

                // Return from this point
                return true;
            }
        }

        // No matching outlet found for this user
        return false;
    }

    public function currentSessionOutlet()
    {
        // Check session for list of outlets.
        // Get first among the list of accessible outlets, then store in session.
        if (!session()->exists('session_outlet')) {
            session(['session_outlet' => $this->outlets()->first()]);
        }

        return session('session_outlet');
    }
    public function beautician()
    {
        return $this->hasOne(Beautician::class,'beautician_id', 'id');
    }
    public function employee()
    {
        return $this->hasOne(Employee::class,'user_id', 'id');
    }
    public function shift(){
        return $this->hasOne(Shift::class,'shift_id', 'shift_id');
    }

    // public function split($startDate, $endDate){
    //     return $this->hasMany(SplitServiceAmount::class,'beautician_id', 'id')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    // }
    public function split()
    {
        return $this->hasMany(SplitServiceAmount::class, 'beautician_id', 'id')
                    ->where('commission_percentage', '!=', 0);
    }
    
}
