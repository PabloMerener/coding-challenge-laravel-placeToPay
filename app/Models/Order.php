<?php

namespace App\Models;

use App\Models\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Order extends Model
{
    use HasFactory;

    const CREATED = 'CREATED';

    const APPROVED = 'APPROVED';

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

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new OrderScope);
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
