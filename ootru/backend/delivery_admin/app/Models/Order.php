<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Carbon\Carbon;

class Order extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'client_id',
        'date',
        'pickup_point',
        'delivery_point',
        'country_id',
        'city_id',
        'parcel_type',
        'total_weight',
        'total_distance',
        'pickup_datetime',
        'delivery_datetime',
        'parent_order_id',
        'status',
        'payment_id',
        'payment_collect_from',
        'delivery_man_id',
        'fixed_charges',
        'extra_charges',
        'total_amount',
        'bid_type',
        'vehicle_charge',
        'pickup_confirm_by_client',
        'pickup_confirm_by_delivery_man',
        'reason',
        'weight_charge',
        'distance_charge',
        'insurance_charge',
        'total_parcel',
        'auto_assign',
        'cancelled_delivery_man_ids',
        'vehicle_id',
        'vehicle_data',
        'description',
        'assign_datetime',
        'currency',
        'milisecond',
        'packaging_symbols',
        'couriercompany_id',
        'rescheduledatetime',
        'is_shipped',
        'shipped_verify_at',
        'package_value',
        'partner_referral_code',
        'referral_code',
        'nearby_driver_ids',
        'accept_delivery_man_ids',
        'reject_delivery_man_ids',
        'payment_type',
        'coupon_code',
        'discount_amount',
        'sms_type',
        'is_reschedule'
    ];

    protected $casts = [
        'client_id' => 'integer',
        'country_id' => 'integer',
        'city_id' => 'integer',
        'parent_order_id' => 'integer',
        'payment_id' => 'integer',
        'delivery_man_id' => 'integer',

        'total_weight' => 'double',
        'total_distance' => 'double',
        'fixed_charges' => 'double',
        'weight_charge' => 'double',
        'distance_charge' => 'double',
        'vehicle_charge' => 'double',
        'total_amount' => 'double',
        'pickup_confirm_by_client' => 'integer',
        'pickup_confirm_by_delivery_man' => 'integer',
        'auto_assign' => 'integer',
        'total_parcel' => 'integer',
        'vehicle_id' => 'integer',
        // 'pickup_point' => 'array',
        // 'delivery_point' => 'array',
    ];
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id', 'id');
    }

    public function delivery_man()
    {
        return $this->belongsTo(User::class, 'delivery_man_id', 'id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }
    public function couriercompany()
    {
        return $this->belongsTo(CourierCompanies::class, 'couriercompany_id', 'id');
    }
    public function reschedule()
    {
        return $this->belongsTo(Reschedule::class, 'is_reschedule', 'id');
    }
    public function bids()
    {
        return $this->hasMany(OrderBid::class, 'order_id');
    }

    public function retunOrdered()
    {
        return $this->hasMany(Order::class, 'parent_order_id');
    }
    public function scopeMyOrder($query)
    {
        $user = auth()->user();
        if (in_array($user->user_type, ['admin'])) {
            return $query;
        }

        if ($user->user_type == 'client') {
            return $query->where('client_id', $user->id)->whereNotIn('status', ['pending']);
        }

        if ($user->user_type == 'delivery_man') {
            return $query->whereHas('delivery_man', function ($q) use ($user) {
                $q->where('delivery_man_id', $user->id);
            });
        }
        return $query;
    }
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    public function scopeStatus($query, $statuses)
    {
        if (is_array($statuses)) {
            return $query->whereIn('status', $statuses);
        }
        return $query->where('status', $statuses);
    }

    public function orderHistory()
    {
        return $this->hasMany(OrderHistory::class, 'order_id', 'id')->orderBy('created_at', 'desc')->withTrashed();
    }

    public function orderHistoryasc()
    {
        return $this->hasMany(OrderHistory::class, 'order_id', 'id')->orderBy('created_at', 'asc');
    }

    public function profofPictures()
    {
        return $this->hasMany(Profofpictures::class, 'order_id', 'id');
    }
    
    public function orderVehicleHistory()
    {
        return $this->belongsTo(OrderVehicleHistory::class, 'order_id', 'id');
    }

    public function customerSupport()
    {
        return $this->hasMany(CustomerSupport::class, 'order_id', 'id');
    }


    protected static function boot()
    {
        parent::boot();
        static::deleted(function ($row) {
            $row->orderHistory()->delete();
            if ($row->forceDeleting === true) {
                $row->orderHistory()->forceDelete();
                $row->bids()->delete();
                $row->orderVehicleHistory()->delete();
                $row->profofPictures()->delete();
                $row->customerSupport()->delete();
            }
        });
        static::restoring(function ($row) {
            $row->orderHistory()->withTrashed()->restore();
        });
    }

    public function getPickupPointAttribute($value)
    {
        $val = isset($value) ? json_decode($value, true) : null;
        return $val;
    }

    public function getDeliveryPointAttribute($value)
    {
        $val = isset($value) ? json_decode($value, true) : null;
        return $val;
    }

    public function getExtraChargesAttribute($value)
    {
        $val = isset($value) ? json_decode($value, true) : null;
        return $val;
    }

    public function setPickupPointAttribute($value)
    {
        $this->attributes['pickup_point'] = isset($value) ? json_encode($value) : null;
    }

    public function setDeliveryPointAttribute($value)
    {
        $this->attributes['delivery_point'] = isset($value) ? json_encode($value) : null;
    }

    public function setExtraChargesAttribute($value)
    {
        $this->attributes['extra_charges'] = isset($value) ? json_encode($value) : null;
    }

    public function getCreatedAtAttribute($value)
    {
        return isset($value) ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }
    public function getUpdatedAtAttribute($value)
    {
        return isset($value) ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }
    public function getDeletedAtAttribute($value)
    {
        return isset($value) ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }

    public function deliveryMenAccept()
{
    // Decode the JSON and cast to array to ensure it's always an array
    $deliveryManIds = (array) json_decode($this->accept_delivery_man_ids, true);

    // If the array is empty or invalid, return an empty collection
    if (empty($deliveryManIds)) {
        return collect();
    }

    // Query the User model using the decoded array of IDs
    return User::whereIn('id', $deliveryManIds)->get();
}

    public function deliveryMenReject()
    {
        $deliveryManIds = json_decode($this->reject_delivery_man_ids, true);

       
        return User::whereIn('id', $deliveryManIds)->get();
    }
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function getCancelledDeliveryManIdsAttribute($value)
    {
        $val = isset($value) ? json_decode($value, true) : [];
        return $val;
    }

    public function setCancelledDeliveryManIdsAttribute($value)
    {
        $this->attributes['cancelled_delivery_man_ids'] = isset($value) ? json_encode($value) : [];
    }

    public function getVehicleDataAttribute($value)
    {
        return isset($value) ? json_decode($value, true) : null;
    }

    public function setVehicleDataAttribute($value)
    {
        $this->attributes['vehicle_data'] = isset($value) ? json_encode($value) : null;
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }

    public function saveUserAddress()
    {
        return $this->belongsTo(UserAddress::class, 'client_id', 'user_id');
    }
}
