<?php

require_once './queryBuilder.php';

$qb=new myc\QueryBuilder();
/*
$qb->select('k.*')->select('id')->select('t.id')->select('t.id','tid')->select('k.*,p.ID')->select('m.myc,m.myc2 as mm,m.myc3 mmm')
        ->select(myc\QueryBuilder::raw('select ?? from data d where d.KID=h.HID',['d.ID']),'newfield');
$qb->distinct('ID')->avg('TOTALS')->count('ID')->max('PRICE')->min('PRICE')->sum('st.PRICE');


var_dump($qb->getSelects());
 * 
 */
$qb->where('q.ID', 5);
$qb->where(myc\QueryBuilder::raw('a not exist(test) ? ??',[1,2]));
$qb->whereOr(myc\QueryBuilder::raw('a not exist(test) ? ??',[1,2]));
$qb->whereGroup(function(myc\QueryBuilder $obj){
  
  $obj->where('ID',1);
  $obj->where('ID2',2);
  $obj->where('ID3',2);
  $obj->where(myc\QueryBuilder::raw('a not exist(test) ? ??',[1,2]));

});

$qb->whereBetween('test', '07-07-1995', '07-07-2019');
$qb->whereInOr('tests', [1,2,3]);
var_dump($qb->getWheres());

