<?php

use App\Model\Coin;
use Illuminate\Database\Seeder;

class CoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Coin::create(['name'=>'Bitcoin', 'type'=>'BTC']);
        Coin::create(['name'=>'Tether USD', 'type'=>'USDT']);
        Coin::create(['name'=>'Ether', 'type'=>'ETH']);
        Coin::create(['name'=>'Litecoin', 'type'=>'LTC']);
        Coin::create(['name'=>'Ether', 'type'=>'DOGE']);
        Coin::create(['name'=>'Bitcoin Cash', 'type'=>'BCH']);
        Coin::create(['name'=>'Dash', 'type'=>'DASH']);
    }
}
