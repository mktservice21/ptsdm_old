<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="cekdatasudahada") {
    $bolehinput="boleh";
    
    $pid=$_POST['uid'];
    $pidkar=$_POST['ukry'];
    $pbln=$_POST['ubln'];
    $ptgl_pl1=$_POST['up01'];
    $ptgl_pl2=$_POST['up02'];
    $pkdperiode=$_POST['ukdperiode'];
    $pdivisi=$_POST['udivisi'];
    
    $pbulan= date("Ym", strtotime($pbln));
    
    $ptgl_pl1 = str_replace('/', '-', $ptgl_pl1);
    $ptgl_pl2 = str_replace('/', '-', $ptgl_pl2);
    $ptgl1= date("Y-m-d", strtotime($ptgl_pl1));
    $ptgl2= date("Y-m-d", strtotime($ptgl_pl2));
    
    $pbln_pl1=date("Ym", strtotime($ptgl_pl1));
    $pbln_pl2=date("Ym", strtotime($ptgl_pl2));
    
    
    if ( ($pbulan<>$pbln_pl1) OR ($pbulan<>$pbln_pl2) ) {
        echo "Bulan dan Periode tidak sesuai...."; exit;
    }
    
    include "../../../config/koneksimysqli.php";
    
    $query = "select idrutin from dbmaster.t_brrutin0 WHERE kode='1' AND ( (periode1 between '$ptgl1' AND '$ptgl2') OR (periode2 between '$ptgl1' AND '$ptgl2') ) "
            . " AND "
            . " karyawanid='$pidkar' AND IFNULL(stsnonaktif,'')<>'Y' AND idrutin<>'$pid'";//AND kodeperiode='$pkdperiode' 
    
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    
    if ($ketemu>0) {
        $row= mysqli_fetch_array($tampil);
        $nidrutin=$row['idrutin'];
        if (!empty($nidrutin)) $bolehinput="GAGAL.... Periode yang dipilih Tidak bisa tersimpan. karena sudah ada inputan, dengan ID : $nidrutin";
    }
    
    
    if (empty($pid)) {
        
        $query_spd = "select * from dbmaster.t_suratdana_br WHERE "
                . " IFNULL(stsnonaktif,'')<>'Y' AND kodeperiode='$pkdperiode' AND DATE_FORMAT(tglf,'%Y%m')='$pbulan' AND "
                . " jenis_rpt='RTNETH'";

        $tampil2= mysqli_query($cnmy, $query_spd);
        $ketemu2= mysqli_num_rows($tampil2);
        if ($ketemu2>0) {
            $pbulan_ym= date("F Y", strtotime($pbln));
            $bolehinput="GAGAL.... Periode ($pkdperiode) $pbulan_ym yang dipilih Sudah closing";
        }
        
    }
    
    
    mysqli_close($cnmy);
    echo $bolehinput; exit;
    
}elseif ($pmodule=="getkodeperiode") {
    
    $pdivisi=$_POST['udivid'];
    $ptgl=$_POST['utanggal'];
    if (empty($ptgl)) $ptgl=date("Y-m-d");
    
    $tglini=date("Y-m", strtotime($ptgl));
    $hariiniserver=date("d", strtotime($ptgl));
    
    $pbln=$_POST['ubulan'];
    $pbulan =  date("Y-m", strtotime($pbln));
    

    echo "<option value='' selected>-- Pilihan --</option>";

    if ($pbulan<$tglini){
        echo "<option value='2' selected>Periode 2</option>";
    }else{
        if ($pbulan==$tglini){
            if ((int)$hariiniserver > 20) {
                echo "<option value='2' selected>Periode 2</option>";
            }else{
                echo "<option value='1'>Periode 1</option>";
                echo "<option value='2' selected>Periode 2</option>";
            }
        }else{
            echo "<option value='1'>Periode 1</option>";
            echo "<option value='2' selected>Periode 2</option>";
        }
    }
        
    
    
}elseif ($pmodule=="getperiode") {
    $pbln=$_POST['ubulan'];
    $pkdperiode=$_POST['ukode'];
    $pkry=$_POST['ukry'];
    $pdivisi=$_POST['udivid'];
    
    $bulan= date("Y-m-d", strtotime($pbln));
    $pnbln= date("Ym", strtotime($pbln));
    
    if ($pkdperiode==1) {
        $periode1= date("Y-m-d", strtotime($bulan));
        $periode2= date("Y-m-15", strtotime($bulan));
    }elseif ($pkdperiode==2) {
        include "../../../config/koneksimysqli.php";
        
        $query = "select idrutin from dbmaster.t_brrutin0 WHERE kode='1' AND karyawanid='$pkry' AND DATE_FORMAT(bulan,'%Y%m')='$pnbln' AND kodeperiode='1'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        mysqli_close($cnmy);
        if ((INT)$ketemu>0) {
            $periode1= date("Y-m-16", strtotime($bulan));
            $periode2= date("Y-m-t", strtotime($bulan));
        }else{
            $periode1= date("Y-m-16", strtotime($bulan));
            $periode2= date("Y-m-t", strtotime($bulan));
        }
        
    }
    
    $bln1=""; $bln2="";
    if (!empty($pkdperiode)) {
        $bln1= date("d/m/Y", strtotime($periode1));
        $bln2= date("d/m/Y", strtotime($periode2));
    }
    echo "$bln1, $bln2"; exit;
    
}elseif ($pmodule=="getdatakaryawan") {
    include "../../../config/koneksimysqli.php";
    
    $pkaryawanid=$_POST['ukry'];
    $pdivisi=$_POST['udivisi'];
    $pjabatanid="";
    $pidcabang="";
    $pidarea="";
    $pidnopol="";
    
    
    $query = "select karyawanId as karyawanid, nama, iCabangId as icabangid, areaId as areaid, jabatanId as jabatanid "
            . " from hrd.karyawan WHERE karyawanid='$pkaryawanid'";
    $tampilk= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampilk);
    $pidcabang=$row['icabangid'];
    $pidarea=$row['areaid'];
    $pjabatanid=$row['jabatanid'];
    $pkaryawannm=$row['nama'];
    
    if ($pdivisi=="HO" AND empty($pidcabang)) {
        $pidcabang="0000000001";//ETH HO
        $pidarea="0000000001";//ETH HO
    }
    
    if ($pdivisi=="HO" AND empty($pidarea)) {
        if ($pidcabang="0000000001") $pidarea="0000000001";//ETH HO
    }
    
    
    $pidnopol="";
    //$query = "select nopol as lcfields from dbmaster.t_kendaraan_pemakai WHERE karyawanid='$pkaryawanid' AND IFNULL(stsnonaktif,'')<>'Y' order by tglawal desc LIMIT 1";
    //$tampiln= mysqli_query($cnmy, $query);
    //$rown= mysqli_fetch_array($tampiln);
    //$pidnopol=$rown['lcfields'];
    
    ?>
        <div hidden class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
            <div class='col-md-4 col-sm-4 col-xs-7'>

                <input type='text' id='e_jabatanid' name='e_jabatanid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pjabatanid; ?>' Readonly>
                <input type='text' id='e_cabangid' name='e_cabangid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcabang; ?>' Readonly>
                <input type='text' id='e_areaid' name='e_areaid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidarea; ?>' Readonly>
                <input type='text' id='e_nmkaryawan' name='e_nmkaryawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkaryawannm; ?>' Readonly>
                
            </div>
        </div>

        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Polisi Kendaraan <span class='required'></span></label>
            <div class='col-md-4 col-sm-4 col-xs-7'>
                <input type='hidden' id='e_nopolidX' name='e_nopolidX' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidnopol; ?>' Readonly>
                
                <?PHP
                    $psudhpilihnopol=false;
                    echo "<select class='form-control input-sm' id='e_nopolid' name='e_nopolid' onchange=''>";
                        echo "<option value='' selected>--Tidak Ada--</option>";
                        $query = "select nopol from dbmaster.t_kendaraan_pemakai WHERE karyawanid='$pkaryawanid' "
                                . " AND IFNULL(stsnonaktif,'')<>'Y' order by tglawal desc";
                        $tampilk=mysqli_query($cnmy, $query);
                        while ($krow= mysqli_fetch_array($tampilk)) {
                            $pidnopolis=$krow['nopol'];

                            if ($psudhpilihnopol==false) {
                                echo "<option value='$pidnopolis' selected>$pidnopolis</option>";
                                $psudhpilihnopol=true;
                            }else{
                                echo "<option value='$pidnopolis'>$pidnopolis</option>";
                            }
                        }
                    echo "</select>";
                ?>
                
            </div>
        </div>

    <?PHP
    mysqli_close($cnmy);
}elseif ($pmodule=="caridataabsentotal") {
    
    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_ubahget_id.php";
    
    $pkodeperiode=$_POST['ukode'];
    $pbln=$_POST['ubulan'];
    $pkaryawanid=$_POST['ukry'];
    $pdivisi=$_POST['udivid'];
    $pnbln =  date("Ym", strtotime($pbln));
    
    $query = "select nama from hrd.karyawan WHERE karyawanid='$pkaryawanid'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pkaryawannm=$row['nama'];
    
    $ptotalsemua=0;
    $pjmlwfh=0;
    $pjmlwfo=0;
    $pjmlwfo_val=0;
    $pjmlwfo_inv=0;

    //cari absensi
    
    if ($pkodeperiode=="2") {
        include "cari_absen_karyawan.php";
        $pjumlahabs = CariAbsensiByKaryawan("inc", $pkaryawanid, $pbln);

        $pjmlwfh=$pjumlahabs[0];
        $pjmlwfo=$pjumlahabs[1];
        $pjmlwfo_val=$pjumlahabs[2];
        $pjmlwfo_inv=$pjumlahabs[3];
    }
    
    //echo "WFH : $pjmlwfh, WFO : $pjmlwfo, WFO val : $pjmlwfo_val, WFO inval: $pjmlwfo_inv<br/>";

    //END cari absensi
    
    ?>
    <script src="js/inputmask.js"></script>
    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
        <div class='col-md-4'>
            <?PHP
            $pkaryidcode=encodeString($pkaryawanid);
            $bulan_pilih=encodeString($pnbln);
            $pviewdataabsen = "<a class='btn btn-warning btn-xs' href='eksekusi3.php?module=showdataabsensi&i=$pkaryidcode&b=$bulan_pilih' target='_blank'>List Absensi $pkaryawannm</a>";
            echo $pviewdataabsen;
            ?>
        </div>
    </div>

    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah WFH <span class='required'></span></label>
        <div class='col-md-4'>
            <input type='text' id='e_jmlwfh' name='e_jmlwfh' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlwfh; ?>' readonly>
        </div>
    </div>

    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah WFO (Valid) <span class='required'></span></label>
        <div class='col-md-4'>
            <input type='hidden' id='e_jmlwfo' name='e_jmlwfo' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlwfo; ?>' readonly>
            <input type='text' id='e_jmlwfoval' name='e_jmlwfoval' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlwfo_val; ?>' readonly>
        </div>
    </div>

    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:red;">Jumlah WFO (Invalid) <span class='required'></span></label>
        <div class='col-md-4'>
            <input type='text' id='e_jmlwfoinv' name='e_jmlwfoinv' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlwfo_inv; ?>' readonly>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total Rp. <span class='required'></span></label>
        <div class='col-md-4'>
            <input type='text' id='e_totalsemua' name='e_totalsemua' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotalsemua; ?>' readonly>
        </div>
    </div>

    <?PHP
    
    mysqli_close($cnmy);
    
}elseif ($pmodule=="cariinputandetail") {
    
    ?><script src="js/inputmask.js"></script><?PHP
    include "../../../config/koneksimysqli.php";
    
    $pstsmobile=$_SESSION['MOBILE'];
    
    
    $pjabatanid =$_POST['ujbt'];
    $pkaryawanid =$_POST['ukry'];
    $pidrutin =$_POST['uid'];
    $pdivisi =$_POST['udivisi'];
    $pidact =$_POST['uact'];
    
    $ptotalsemua=$_POST['utotal'];
    $pjmlwfh=$_POST['ujmlwfo'];
    $pjmlwfo=$_POST['ujmlwfo'];
    $pjmlwfo_val=$_POST['ujmlwfo_val'];
    $pjmlwfo_inv=$_POST['ujmlwfo_inv'];
    
    if ($pstsmobile=="Y") {
        echo "<br/>&nbsp;";
        echo "<div style='overflow-x:auto;'>";
            include "inputdetailmobileadmcb.php";
        echo "</div>";
    }else{
        include "inputdetailbradmcb.php";
    }
    
}elseif ($pmodule=="xx") {
    
}

?>

