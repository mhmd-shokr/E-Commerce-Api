<?php

namespace App\filters\v1;
use App\filters\ApiFilter;
use Illuminate\Http\Request;

class CategoryFilter extends ApiFilter{

    protected $allowedParms = [
        'name'          => ['eq'],
        'slug'          => ['eq'],
    ];

    protected $columnLikeToDb=[];


    protected $operatorMap=[
        'eq'=>'=',
        'lt'=>'<',
        'gt'=>'>',
        'lte'=>'<=',
        'gte'=>'>=',
    ];
}