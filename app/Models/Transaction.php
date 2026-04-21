<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'company_id', 'account_id', 'category_id', 'type', 
        'amount', 'description', 'date', 'invoice_id', 'bill_id', 'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
