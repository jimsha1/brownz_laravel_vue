<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class BookingPayment extends Model
{
    use HasFactory, SoftDeletes;

    /* payment modes */
    const PAYMENT_MODE_CASH = 'cash';
    const PAYMENT_MODE_CARD = 'card';
    const PAYMENT_MODE_ONLINE = 'online';
    const PAYMENT_MODE_CREDIT = 'credit';

    /* card types */
    const CARD_TYPE_MADA = 'mada';
    const CARD_TYPE_MASTER = 'master';
    const CARD_TYPE_VISA = 'visa';
    const CARD_TYPE_CREDIT = 'credit';
    const CARD_TYPE_DEBIT  = 'debit';

    protected $primaryKey = 'payment_id';
    protected $guarded = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['payment_mode_locale', 'card_type_locale'];

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

        /* Execute when deleting a record */
        static::deleting(function ($entity) {
            $entity->deleted_by = Auth::id() ?? $entity->deleted_by;
            $entity->save();
        });
    }

    /**
     * Get the payment mode in current locale using accessor.
     *
     * @return string
     */
    public function getPaymentModeLocaleAttribute()
    {
        return $this->enumToLocale($this->attributes['payment_mode']);
    }

    /**
     * Get the card type in current locale using accessor.
     *
     * @return string
     */
    public function getCardTypeLocaleAttribute()
    {
        return $this->enumToLocale($this->attributes['card_type']);
    }

    public function getPaymentModes()
    {
        return [

            self::PAYMENT_MODE_CASH,
            self::PAYMENT_MODE_CARD,
            self::PAYMENT_MODE_ONLINE,
            self::PAYMENT_MODE_CREDIT,

        ];
    }

    public function paymentModeDropdown($default = false)
    {
        $data = [];
        foreach ($this->getPaymentModes() as $item) {
            // Using locale settings
            $data[$item] = $this->enumToLocale($item);
        }

        return voco_nvp_dropdown($data, $default);
    }

    public function getCardTypes()
    {
        return [
            self::CARD_TYPE_DEBIT,
            self::CARD_TYPE_CREDIT,
            // self::CARD_TYPE_MASTER,
            // self::CARD_TYPE_VISA,
        ];
    }

    public function cardTypeDropdown($default = false)
    {
        $data = [];
        foreach ($this->getCardTypes() as $item) {
            // Using locale settings
            $data[$item] = $this->enumToLocale($item);
        }

        return voco_nvp_dropdown($data, $default);
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

    private function enumToLocale($value)
    {
        return __(ucwords(str_replace(['_', '-'], ' ', $value)));
    }
}
