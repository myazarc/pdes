<?php

preg_match_all('/(\?\?|\?)/im', 'select ? from ? ?? ? ?',$matches);
var_dump($matches);

require_once './queryBuilder.php';

$qb=new myc\QueryBuilder();

$qb->select('k.*')->select('id')->select('t.id')->select('t.id','tid')->select('k.*,p.ID')->select('m.myc,m.myc2 as mm,m.myc3 mmm')
        ->select(myc\QueryBuilder::raw('select ?? from data d where d.KID=h.HID',['d.ID']),'newfield');
$qb->distinct('ID')->avg('TOTALS')->count('ID')->max('PRICE')->min('PRICE')->sum('st.PRICE');


var_dump($qb->getSelects());

