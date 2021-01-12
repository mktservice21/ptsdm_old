<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="tmapilkanestimasi") {
    
    $pidno=$_POST['uid'];
    $pidkar=$_POST['uidkry'];
    $piddokt=$_POST['uiddr'];
    $pjumlahki=$_POST['ujmlki'];
    
    include "../../config/koneksimysqli.php";
    
    $pmintacn="";
    $pmintaroi="";
    $pjumlahki=str_replace(",","", $pjumlahki);
    
    $pallperiode="";
    $pperiode1="";
    $pperiode2="";
    
    
    if ($pidno=="0") $pidno="";
    if (!empty($pidno)) {
        $query = "SELECT * FROM hrd.t_estimasi_ki WHERE noid='$pidno'";
        $tampil= mysqli_query($cnmy, $query);
        $row= mysqli_fetch_array($tampil);
        
        $pmintacn=$row['est_perbln'];
        $pmintaroi=$row['est_roi'];
    }
    
    $query = "select distinct srid, dokterid, bulan from hrd.ks1 WHERE srid='$pidkar' and dokterid='$piddokt' ORDER BY bulan DESC LIMIT 6";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        while ($row=mysqli_fetch_array($tampil)) {
            if (empty($pperiode2)) $pperiode2=$row['bulan'];
            $pperiode1=$row['bulan'];
            
            $pallperiode .=$row['bulan'].",";
        }
        
    }
    
    if (!empty($pallperiode)) {
        $pallperiode=substr($pallperiode, 0, -1);
    }
    
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpcaricnkski00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpcaricnkski01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpcaricnkski02_".$puserid."_$now ";

    $query = "select * from hrd.ks1 WHERE srid='$pidkar' and dokterid='$piddokt' AND bulan between '$pperiode1' AND '$pperiode2'";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER TABLE $tmp00 ADD COLUMN cn DECIMAL(20,2), ADD COLUMN rp DECIMAL(20,2), ADD COLUMN cnrp DECIMAL(20,2), ADD COLUMN cnrp_min DECIMAL(20,2), ADD COLUMN jmlki DECIMAL(20,2), ADD COLUMN ists VARCHAR(5)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
    
    $query = "select tgl, karyawanid, dokterid, awal, cn from hrd.mr_dokt_a WHERE karyawanid='$pidkar' and dokterid='$piddokt'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    /*
    $query = "select brid, mrid as mrid, dokterid as dokterid, left(tgl,7) as bulan, jumlah, jumlah1 
		from hrd.br0 WHERE mrid='$pidkar' and dokterid='$piddokt' and 
		ifnull(batal,'')<>'Y' and ifnull(retur,'')<>'Y' and brid not in 
		(select distinct IFNULL(brid,'') from hrd.br0_reject)
		GROUP BY 1,2,3";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    */
    
    $query = "select distinct DATE_FORMAT(tgl, '%Y-%m') as tgl, ifnull(karyawanid,'') as karyawanid, "
            . " ifnull(dokterid,'') as dokterid, ifnull(cn,0) as cn from $tmp01";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        while ($row=mysqli_fetch_array($tampil)) {
            $pcn=$row['cn'];
            $nperiode=$row['tgl'];
            if (empty($pcn)) $pcn=0;
            
            $query = "UPDATE $tmp00 SET cn='$pcn' WHERE left(bulan,7)>='$nperiode'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp00 SET rp=IFNULL(qty,0)*IFNULL(hna,0)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp00 SET cnrp=case when IFNULL(cn,0)=0 then 0 else IFNULL(rp,0)*(IFNULL(cn,0)/100) end";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp00 SET cnrp=case when IFNULL(cn,0)=0 then 0 else (IFNULL(rp,0)*0.8) * (IFNULL(cn,0)/100) end WHERE IFNULL(apttype,'')<>'1'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
        }
        
    }
    
    /*
    $query = "insert into $tmp00 (srid, dokterid, bulan, ists, jmlki)
        select mrid as mrid, dokterid as dokterid, bulan, 'KI' as ists, SUM(jumlah) as jumlah 
        from $tmp02 
        GROUP BY 1,2,3,4";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp00 SET cnrp_min=IFNULL(cnrp,0)-IFNULL(jmlki,0)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    */
    
    
    $proi_sis=0;
    $ptotalks_sis=0;
    
    $query = "select sum(cnrp) as cnrp from $tmp00";// where bulan between '$pperiode1' AND '$pperiode2'
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        while ($row=mysqli_fetch_array($tampil)) {
            $ptotalks_sis=ROUND((DOUBLE)$row['cnrp']/6,2);
        }
        
    }
    
    if ((DOUBLE) $ptotalks_sis<>0) $proi_sis=ROUND((DOUBLE)$pjumlahki/(DOUBLE)$ptotalks_sis,2);
    
    $plihatks="<a class='btn btn-info btn-xs' href='eksekusi3.php?module=lihatdataksusr&ket=bukan&iid=$pidkar&ind=$piddokt' target='_blank'>Lihat Rincian KS</a>";
    
    ?>
    <script src="js/inputmask.js"></script>
    

    
    
    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
            &nbsp; 
        </label>
        <div class='col-md-4'>
            <input type='text' id='e_periode1' name='e_periode1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pperiode1; ?>' Readonly>
            <input type='text' id='e_periode2' name='e_periode2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pperiode2; ?>' Readonly>
            <input type='text' id='e_periodeall' name='e_periodeall' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pallperiode; ?>' Readonly>
        </div>
    </div>
    
    
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
            Estimasi Tiap Bulan Rp. 
        </label>
        <div class='col-md-4'>
            <input type='text' id='e_etimasiperbln' name='e_etimasiperbln' onblur="HitungROIPermitaan()" autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pmintacn; ?>'>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
            ROI 
        </label>
        <div class='col-md-4'>
            <input type='text' id='e_mintaroi' name='e_mintaroi' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pmintaroi; ?>' >
        </div>
    </div>
    
    
    
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
            Estimasi Perkiraan Sales 6 Bulan Rp. 
        </label>
        <div class='col-md-4'>
            <input type='text' id='e_cn' name='e_cn' onblur="HitungROIPermitaanSistem()" autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotalks_sis; ?>' Readonly>
        </div>
    </div>
    
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
            Perkiraan ROI 
        </label>
        <div class='col-md-4'>
            <input type='text' id='e_roi' name='e_roi' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $proi_sis; ?>' Readonly>
        </div>
    </div>
    

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
            &nbsp; 
        </label>
        <div class='col-md-8'>
            <?PHP echo $plihatks; ?> 
        </div>
    </div>
    
    
    <hr/>
    
    <script>
        function HitungROIPermitaan() {
            var ajmlki=document.getElementById('e_jumlahki').value;
            var ajmlcn=document.getElementById('e_etimasiperbln').value;
            var newchar = '';

            if (ajmlki=="") ajmlki="0";
            ajmlki = ajmlki.split(',').join(newchar);

            if (ajmlcn=="") ajmlcn="0";
            ajmlcn = ajmlcn.split(',').join(newchar);

            var nTotal_="0";
            if (ajmlcn!="0") {
                nTotal_ =(parseFloat(ajmlki)/parseFloat(ajmlcn)).toFixed(2);
            }

            //document.getElementById('e_mintaroi').value=nTotal_;
            HitungROIPermitaanSistem();

        }
        
        function HitungROIPermitaanSistem() {
            var ajmlki=document.getElementById('e_jumlahki').value;
            var ajmlcn=document.getElementById('e_cn').value;
            var newchar = '';

            if (ajmlki=="") ajmlki="0";
            ajmlki = ajmlki.split(',').join(newchar);

            if (ajmlcn=="") ajmlcn="0";
            ajmlcn = ajmlcn.split(',').join(newchar);

            var nTotal_="0";
            if (ajmlcn!="0") {
                nTotal_ =(parseFloat(ajmlki)/parseFloat(ajmlcn)).toFixed(2);
            }

            document.getElementById('e_roi').value=nTotal_;

        }
    </script>
    

    
    
    <?PHP
    
    hapusdata:
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
        mysqli_close($cnmy);
    
}

?>
