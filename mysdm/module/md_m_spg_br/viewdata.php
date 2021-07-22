<?php

if ($_GET['module']=="viewspg"){
    include "../../config/koneksimysqli_it.php";
    
    $cabang = $_POST['ucab'];
    
    $query = "select id_spg, nama from MKT.spg where icabangid='$cabang' order by nama";
    $tampil=mysqli_query($cnit, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        $eidspg=(int)$a['id_spg'];
        $enmspg=$a['nama'];
        echo "<option value='$eidspg'>$enmspg</option>";
    }
    
}elseif ($_GET['module']=="viewdataspg"){
    ?> <script src="js/inputmask.js"></script> <?PHP
    include "../../config/koneksimysqli_it.php";
    include "../../config/koneksimysqli.php";
    
    $pidspg = $_POST['uspg'];
    $pidcabang = $_POST['ucabang'];
    
    $datebr = str_replace('/', '-', $_POST['utgl']);
    $cbulan= date("Y-m", strtotime($datebr));
    //$cbulan =  date("Y-m", strtotime($_POST['utgl']));
    
    $query = "select * from MKT.spg where id_spg='$pidspg'";
    $tampil=mysqli_query($cnit, $query);
    $a=mysqli_fetch_array($tampil);
    $penempatan=$a['penempatan'];
    $tgl1="";
    $tgl2="";
    $pharikerja="";
    
    $query = "select a.* from dbmaster.t_spg_gaji a where a.id_spg='$pidspg' AND a.icabangid='$pidcabang' "
            . " AND DATE_FORMAT(periode,'%Y%m') = (SELECT MAX(DATE_FORMAT(b.periode,'%Y%m')) "
            . " FROM dbmaster.t_spg_gaji b WHERE b.id_spg=a.id_spg AND b.icabangid=a.icabangid) ";
    
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ($ketemu==0) {
        $query = "select a.* from dbmaster.t_spg_gaji_cabang a WHERE icabangid='$pidcabang' 
            AND DATE_FORMAT(periode,'%Y%m') = (SELECT MAX(DATE_FORMAT(b.periode,'%Y%m')) 
            FROM dbmaster.t_spg_gaji_cabang b WHERE b.icabangid=a.icabangid)";
        $tampil=mysqli_query($cnmy, $query);
    }
    $g=mysqli_fetch_array($tampil);
    
    $pgaji=$g['gaji'];
    $pmakan=$g['umakan'];
    $ptotmakan="";
    $psewakendaraan=$g['sewakendaraan'];
    $ppulsa=$g['pulsa'];
    $pparkir=$g['parkir'];
    
    $pinsentif=0;
    if (!empty($pidspg)) {
        $query = "select sum(inct_total) jumlah from fe_it.t_spg_incentive where id_spg='$pidspg' AND inct_bulan='$cbulan'";
        $tampil=mysqli_query($cnit, $query);
        $in=mysqli_fetch_array($tampil);
        $pinsentif=$in['jumlah'];
    }
    
    $totalrp=0;
    if ($pgaji=="") $pgaji=0;
    if ($pmakan=="") $pmakan=0;
    if ($psewakendaraan=="") $psewakendaraan=0;
    if ($ppulsa=="") $ppulsa=0;
    if ($pparkir=="") $pparkir=0;
    if ($pinsentif=="") $pinsentif=0;
    
    $totalrp=(double)$pgaji+(double)$pmakan+(double)$psewakendaraan+(double)$ppulsa+(double)$pparkir+(double)$pinsentif
    ?>
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Penempatan <span class='required'></span></label>
            <div class='col-xs-6'>
                <input type='text' id='e_penempatan' name='e_penempatan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $penempatan; ?>' Readonly>
            </div>
        </div>

        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Mulai <span class='required'></span></label>
            <div class='col-xs-3'>
                <input type='text' id='e_mulai' name='e_mulai' class='form-control col-md-7 col-xs-12' value='<?PHP echo $tgl1; ?>' Readonly>
                s/d.
                <input type='text' id='e_sampai' name='e_sampai' class='form-control col-md-7 col-xs-12' value='<?PHP echo $tgl2; ?>' Readonly>
            </div>
        </div>

        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Insentif <span class='required'></span></label>
            <div class='col-xs-3'>
                <input type='text' id='e_insentif' name='e_insentif' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pinsentif; ?>' Readonly>
            </div>
        </div>

        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Gaji Pokok <span class='required'></span></label>
            <div class='col-xs-3'>
                <input type='text' id='e_gaji' name='e_gaji' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pgaji; ?>' Readonly>
            </div>
        </div>
    
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jml. Hari Kerja <span class='required'></span></label>
            <div class='col-xs-3'>
                <input type='text' id='e_hk' name='e_hk' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur='hit_total()' value='<?PHP echo $pharikerja; ?>'>
            </div>
        </div>

        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Uang Makan <span class='required'></span></label>
            <div class='col-xs-3'>
                <input type='text' id='e_makan' name='e_makan' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pmakan; ?>' Readonly>
                <input type='text' id='e_totmakan' name='e_totmakan' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotmakan; ?>' Readonly>
            </div>
        </div>
    
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sewa Kendaraan <span class='required'></span></label>
            <div class='col-xs-3'>
                <input type='text' id='e_sewa' name='e_sewa' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $psewakendaraan; ?>' Readonly>
            </div>
        </div>
    
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pulsa <span class='required'></span></label>
            <div class='col-xs-3'>
                <input type='text' id='e_pulsa' name='e_pulsa' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ppulsa; ?>' Readonly>
            </div>
        </div>
    
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Parkir <span class='required'></span></label>
            <div class='col-xs-3'>
                <input type='text' id='e_parkir' name='e_parkir' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pparkir; ?>' Readonly>
            </div>
        </div>
    
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total <span class='required'></span></label>
            <div class='col-xs-3'>
                <input type='text' id='e_total' name='e_total' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $totalrp; ?>' Readonly>
            </div>
        </div>
    
    <?PHP
}