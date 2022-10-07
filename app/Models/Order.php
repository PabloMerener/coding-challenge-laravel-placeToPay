<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Order extends Model
{
    use HasFactory;

    const CREATED = 'CREATED';
    const PAYED = 'PAYED';
    const REJECTED = 'REJECTED';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::saving(
            fn ($model) => Validator::make($model->toArray(), [
                'user_id' => 'required|exists:App\Models\User,id',
                'customer_name' => 'required',
                'customer_email' => 'required|email',
                'customer_mobile' => 'required',
                'status' => 'required|in:CREATED,PAYED,REJECTED',
                'quantity' => 'required',
                'amount' => 'required',
            ])->validate()
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
