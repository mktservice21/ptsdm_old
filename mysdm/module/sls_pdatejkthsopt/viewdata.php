<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caridataperiode") {
    
    $ntgl=$_POST['ubulan'];
    $hari_ini = date('Y-m-d', strtotime($ntgl));
    
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    
    $tgl_firstthnlalu = date('01 F Y', strtotime('-1 year', strtotime($hari_ini)));
    $tgl_lstthnlalu = date('t F Y', strtotime('-1 year', strtotime($hari_ini)));
    
    $mythn = date('Y', strtotime($ntgl));
    $hari_ini2 = date($mythn."-01-01");
    $tgl_thnlalu = date('01 F Y', strtotime('-1 year', strtotime($hari_ini2)));
    
    ?>
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Awal Bln. Thn. Lalu</label>
        <div class='col-md-6'>
            <div class='input-group date' id=''>
                <input type="text" class="form-control" id='e_frtthnlalu' name='e_frtthnlalu' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl_firstthnlalu; ?>' Readonly>
            </div>
        </div>
    </div>


    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Akhir Bln. Thn. Lalu</label>
        <div class='col-md-6'>
            <div class='input-group date' id=''>
                <input type="text" class="form-control" id='e_lstthnlala' name='e_lstthnlala' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl_lstthnlalu; ?>' Readonly>
            </div>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Awal Thn. Lalu</label>
        <div class='col-md-6'>
            <div class='input-group date' id=''>
                <input type="text" class="form-control" id='e_thnlalu' name='e_thnlalu' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl_thnlalu; ?>' Readonly>
            </div>
        </div>
    </div>
    <?PHP
}

?>