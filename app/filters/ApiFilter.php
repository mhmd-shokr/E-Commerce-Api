<?php
namespace App\filters;
use Illuminate\Http\Request;

class ApiFilter{
    protected $allowedParms = [];
    protected $columnLikeToDb=[];

    protected $operatorMap=[];
    public function transform(Request $request){
        $eleQuery=[];
        //$operators=operatorMap associate to allowedParms
        // $parm = name of operator like 'eq'
        foreach($this->allowedParms as $parm=>$operators){
            $query=$request->query($parm);

            if(!isset($query)){ 
                continue;
            }
            //check if have convert like as `PostalCode=>postal_code`
            $column = $this->columnLikeToDb[$parm] ?? $parm;

            //?price=100
            if(!is_array($query)){
                if (in_array('eq', $operators)){
                    $eleQuery[]=[$column,$this->operatorMap['eq'],$query];
                }
                continue;
            }
            //?price[lt]=100&price[gt]=50
            foreach($operators as $operator){
                if(isset($query[($operator)] )){
                    $eleQuery[]=[$column,$this->operatorMap[$operator],$query[$operator]];
                }
            }
        }
        return $eleQuery;
    }
}