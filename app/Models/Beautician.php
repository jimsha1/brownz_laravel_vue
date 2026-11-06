<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Beautician extends Model
{
    use HasFactory;

    const BEAUTICIAN_UPLOAD_PATH = 'beauticians';
    const DEFAULT_SHIFT_START_TIME = '10:00:00';
    const DEFAULT_SHIFT_END_TIME = '21:30:00';
    const DEFAULT_SHIFT_HALF_TIME = '15:00:00';

    const STATUS_ACTIVE = 'A';
    const STATUS_INACTIVE = 'I';

    protected $primaryKey = 'beautician_id';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['name_locale', 'short_bio_locale', 'long_bio_locale'];
    protected $fillable = ['beautician_id', 'status', 'created_outlet_id', 'join_date'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($entity) {

            // Fetch the last beautician code entered
            $lastBeauticianCode = \App\Models\Beautician::latest('beautician_id')->first();
            $lastBeauticianCodeValue = $lastBeauticianCode ? $lastBeauticianCode->beautician_code : null;

            // Fetch the auto-increment value for beautician code
            $auto_emp_code = \App\Models\AutoIncrementMaster::where('auto_code', 'AI_BEAUTICIAN_CODE')->first();

            // Generate beautician code based on the last entered code
            if ($lastBeauticianCodeValue) {
                $numericPart = (int) substr($lastBeauticianCodeValue, strlen($auto_emp_code->auto_prefix));
                $newBeauticianCodeValue = $numericPart + 1;
            } else {
                $newBeauticianCodeValue = $auto_emp_code->auto_increment_general_value;
            }

            $formattedBeauticianCode = str_pad($newBeauticianCodeValue, 3, '0', STR_PAD_LEFT);

            $auto_emp_code->auto_increment_general_value = $newBeauticianCodeValue;
            $auto_emp_code->save();

            $entity->beautician_code = $auto_emp_code->auto_prefix.$formattedBeauticianCode;
        });
    }

    /**
     * Get the name in current locale using accessor.
     *
     * @return string
     */
    public function getNameLocaleAttribute()
    {
        return $this->attributes['name'] ?? null;
    }

    /**
     * Get the short bio in current locale using accessor.
     *
     * @return string
     */
    public function getShortBioLocaleAttribute()
    {
        return $this->attributes['short_bio_en'] ?? null;
    }

    /**
     * Get the long bio in current locale using accessor.
     *
     * @return string
     */
    public function getLongBioLocaleAttribute()
    {
        return $this->attributes['long_bio_en'] ?? null;
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id', 'beautician_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'beautician_id');
    }

    /**
     * Restrict to beauticians that are active AND linked to an active employee (+ active user).
     */
    public function scopeActiveListed($query)
    {
        return $query
            ->where('beauticians.status', self::STATUS_ACTIVE)
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('employees')
                    ->whereColumn('employees.user_id', 'beauticians.beautician_id')
                    ->where('employees.status', 'active');
            })
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('users')
                    ->whereColumn('users.id', 'beauticians.beautician_id')
                    ->where('users.user_status', 'active');
            });
    }

    /**
     * Base listing query used by dropdowns and selectors.
     */
    public static function activeListedQuery(string $userAlias = 'users')
    {
        $usersJoin = $userAlias === 'users' ? 'users' : "users as {$userAlias}";

        return self::query()
            ->join($usersJoin, 'beauticians.beautician_id', '=', "{$userAlias}.id")
            ->join('employees', 'employees.user_id', '=', 'beauticians.beautician_id')
            ->where('beauticians.status', self::STATUS_ACTIVE)
            ->where('employees.status', 'active')
            ->where("{$userAlias}.user_status", 'active');
    }

    /**
     * Sync beauticians.status from employee status.
     */
    public static function syncStatusFromEmployee(int $userId, string $employeeStatus): void
    {
        $beauticianStatus = $employeeStatus === 'active'
            ? self::STATUS_ACTIVE
            : self::STATUS_INACTIVE;

        self::where('beautician_id', $userId)->update(['status' => $beauticianStatus]);
    }

    public function beauticiansDropdown($default = false)
    {
        $beauticians_array = $this->beauticiansNameMap();

        return voco_nvp_dropdown($beauticians_array, $default);
    }

    /**
     * Flat list for Split Service selects (JSON-safe).
     *
     * @return array<int, array{id:string,text:string}>
     */
    public function beauticiansListForSplit(): array
    {
        $list = [];
        foreach ($this->beauticiansNameMap() as $id => $text) {
            $list[] = [
                'id' => (string) $id,
                'text' => (string) $text,
            ];
        }

        return $list;
    }

    /**
     * @return array<int|string, string>
     */
    private function beauticiansNameMap(): array
    {
        $db_beauticians = \Illuminate\Support\Facades\Cache::remember('salon.booking.beauticians_dropdown', 300, function () {
            return self::activeListedQuery()
                ->select('beauticians.beautician_id', 'beauticians.beautician_code', 'users.name', 'username', 'mobile')
                ->orderBy('users.name')
                ->get();
        });

        $beauticians_array = [];
        foreach ($db_beauticians as $row) {
            $beauticians_array[$row->beautician_id] = $row->name.' ('.$row->beautician_code.')';
        }

        return $beauticians_array;
    }
}
