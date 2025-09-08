<?php
namespace App\Repostries\cart;

use App\Models\Product;
use Collator;
use Illuminate\Support\Collection;

interface CartRepositryInterface{
    public function get() : Collection;
    public function add(Product $product,$quantity=1) ;
    public function update($id,$quantity) ;
    public function delete($id);
    public function empty();
    public function total() : float;


}