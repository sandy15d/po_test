<?php
    $mobile =array("1234",'5678');
    $person = array("abc","xyz");

    $final = [];
   foreach($mobile as $mkey => $m){
       foreach($person as $pkey => $p){
            if($mkey == $pkey){
                $final[$pkey]['mobile'] = $m;
                $final[$pkey]['person'] = $p;
            }
       }
   }

   print_r($final);die;
