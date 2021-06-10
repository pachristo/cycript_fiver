<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Coin extends Model
{
    protected $fillable = ['name', 'type', 'status', 'is_withdrawal', 'is_deposit', 'is_buy', 'is_sell'];
}
