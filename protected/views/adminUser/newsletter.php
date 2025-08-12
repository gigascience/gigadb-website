<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
echo "Email"."||"."First_name"."||"."Last_name"."||"."Affiliation";
?>
<br>
   <? 
   
foreach ($models as $model) {

    echo $model->email."||".$model->first_name."||".$model->last_name."||".$model->affiliation;
    ?>
<br>
   <? 
}
?>
