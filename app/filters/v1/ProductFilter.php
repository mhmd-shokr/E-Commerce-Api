<?php

namespace App\filters\v1;
use App\filters\ApiFilter;
use Illuminate\Http\Request;


class ProductFilter extends ApiFilter{


    protected $allowedParms = [
        'name'          => ['eq'],
        'slug'          => ['eq'],
        'regular_price' => ['eq','lt','gt','lte','gte'],
        'sale_price'    => ['eq','lt','gt','lte','gte'],
    ];
    

    protected $columnLikeToDb=[
        'regular_price'=>'regularPrice',
        'sale_price'=>'salePrice',
    ];

    protected $operatorMap=[
        'eq'=>'=',
        'lt'=>'<',
        'gt'=>'>',
        'lte'=>'<=',
        'gte'=>'>=',
    ];



}