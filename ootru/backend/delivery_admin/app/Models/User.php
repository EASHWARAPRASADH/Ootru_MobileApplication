<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements HasMedia
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use InteractsWithMedia;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username', 'user_type', 'country_id', 'city_id', 'address', 'contact_number','email_verified_at',
        'player_id', 'latitude', 'longitude', 'status', 'last_notification_seen' , 'login_type', 'uid', 'fcm_token', 'otp_verify_at'
        ,'app_version', 'last_location_update_at', 'app_source','last_actived_at','document_verified_at' ,'is_autoverified_document',
        'is_autoverified_email','is_autoverified_mobile','vehicle_id','referral_code','partner_referral_code','flag'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'country_id' => 'integer',
        'city_id' => 'integer',
        'status' => 'integer',
        'otp_verify_at' => 'datetime',
        'document_verified_at' => 'datetime',
        'last_location_update_at'   => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */

    // protected $appends = [
    //     'profile_photo_url',
    // ];


    public function routeNotificationForOneSignal()
    {
        return $this->player_id;
    }

    public function routeNotificationForFcm($notification)
    {
        return $this->fcm_token;
    }

    public static function userCount($user_type = null)
    {
        $query = self::query();
        $query->when($user_type, function($q) use($user_type){
            return $q->where('user_type',$user_type);
        });
        return $query->count();
    }

    public function scopeAdmin($query) {
        return $query->where('user_type', 'admin')->first();
    }

    public function country(){
        return $this->belongsTo(Country::class, 'country_id','id');
    }

    public function city(){
        return $this->belongsTo(City::class, 'city_id','id');
    }

    public function order(){
        return $this->hasMany(Order::class,'client_id','id')->withTrashed();
    }
    // public function customersupport(){
    //     return $this->hasMany(CustomerSupport::class,'user_id','id');
    // }

    public function deliveryManOrder(){
        return $this->hasMany(Order::class,'delivery_man_id','id')->withTrashed();
    }

    public function deliveryManDocument(){
        return $this->hasMany(DeliveryManDocument::class,'delivery_man_id', 'id')->withTrashed();
    }

    public function userBankAccount() {
        return $this->hasOne(UserBankAccount::class, 'user_id', 'id');
    }

    public function userWallet() {
        return $this->hasOne(Wallet::class, 'user_id', 'id');
    }

    public function userWithdraw(){
        return $this->hasMany(WithdrawRequest::class, 'user_id', 'id');
    }

    public function userAddress() {
        return $this->hasMany(UserAddress::class, 'user_id', 'id');
    }

    public function userNotification(){
        return $this->hasMany(Notification::class, 'notifiable_id', 'id');
    }
    public function payments()
    {
        return $this->hasMany(Payment::class, 'client_id');
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id','id');
    }
    public function claims(){
        return $this->hasMany(Claims::class,'client_id','id');
    }

    public function rating(){
        return $this->hasMany(Ratings::class, 'review_user_id', 'id');
    }

    protected static function boot(){
        parent::boot();
        static::deleted(function ($row) {
            switch ($row->user_type) {
                case 'client':
                    $row->order()->delete();
                    if($row->forceDeleting === true)
                    {
                        $row->order()->forceDelete();
                        $row->userAddress()->delete();
                        $row->userNotification()->delete();
                    }
                    break;
                case 'delivery_man':
                    if($row->forceDeleting === true){
                        $row->deliveryManOrder()->update(['delivery_man_id' => NULL ]);
                        $row->userNotification()->delete();
                    }
                    break;
                default:
                    # code...
                    break;
            }
        });
        static::restoring(function($row) {
            $row->order()->withTrashed()->restore();
        });

        // static::created(function ($row) {
        //     if(SettingData('email_verification', 'email_verification')) {
        //         $row->notify(new EmailVerification());
        //     }
        // });
    }

    public function getPayment()
    {
        return $this->hasManyThrough(
            Payment::class,
            Order::class,
            'delivery_man_id',
            'order_id',
            'id',
            'id'
        )->where('payment_status','paid');
    }

    public function userWalletHistory(){
        return $this->hasMany(WalletHistory::class, 'user_id', 'id');
    }
    public function deliveryManEarning(){
        return $this->hasMany(WalletHistory::class, 'user_id', 'id');
    }
}
