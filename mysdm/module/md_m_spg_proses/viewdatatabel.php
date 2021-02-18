<?php
    ini_set("memory_limit","500M");
    ini_set('max_execution_time', 0);
  
    session_start();
    include "../../config/fungsi_sql.php";
    include "../../config/library.php";
    include "../../module/md_m_spg_proses/caridata.php";
    
    $piduser=$_SESSION['USERID'];
    $pidcard=$_SESSION['IDCARD'];
    $pidgroup=$_SESSION['GROUP'];

    $ptanggalminta = date("d F Y");
    
    $date1=$_POST['utgl'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $ptanggal= date("d F Y", strtotime($date1));
    
    $jhari= date("d", strtotime($date1));
    $jbln= date("m", strtotime($date1));
    $jthn= date("Y", strtotime($date1));
    
    $dateins=$_POST['utglinsentif'];
    $tglins= date("Y-m-01", strtotime($dateins));
    $pthnblnins= date("F", strtotime($tglins));
    $ptanggal2= date("d F Y", strtotime($dateins));
    $bulaninsentif= date("Ym", strtotime($dateins));
    $prinsentif= date("F Y", strtotime($dateins));
    
    $mybulan= date("F Y", strtotime($date1));
    
    $bulan= date("Ym", strtotime($date1));
    $pidcabang=$_POST['ucabang'];
    $cket=$_POST['usts'];
    $hidensudahapv2="";
    if ( (INT)$cket==4) $hidensudahapv2="hidden";
    
    $_SESSION['SPGMSTPRSCAB']=$pidcabang;
    $_SESSION['SPGMSTPRSTGL']=date("F Y", strtotime($date1));
    
    //$tmp01 = CariDataSPG($bulan, $pidcabang, "", $cket, $bulaninsentif);
    $tmp01 = CariDataSPGGajiTJ($bulan, $pidcabang, "", $cket, $bulaninsentif);
    $ketemudata = mysqli_num_rows(mysqli_query($cnmy, "select * from $tmp01"));
    
    $jmlkerja = 0;
    $jmlkerja_aspr = 0;
    $query = "select * from dbmaster.t_spg_jmlharikerja WHERE DATE_FORMAT(periode,'%Y%m')='$bulan'";
    $tampilnp = mysqli_query($cnmy, $query);
    while ($np= mysqli_fetch_array($tampilnp)) {
        if (!empty($np['jumlah'])) $jmlkerja=$np['jumlah'];
        if (!empty($np['jml_aspr'])) $jmlkerja_aspr=$np['jml_aspr'];
    }
    
    
    
    $userid=$_SESSION['IDCARD'];
    $now=date("mdYhis");
    $tmp02 =" dbtemp.DSPGSUBMIT02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSPGSUBMIT03_".$userid."_$now ";
    $tmp04 =" dbtemp.DSPGSUBMIT04_".$userid."_$now ";
    $tmp05 =" dbtemp.DSPGSUBMIT05_".$userid."_$now ";
    
    
    $query = "select DISTINCT DATE_FORMAT(periode,'%Y%m') bulan, icabangid, alokid  
            from dbmaster.t_spg_gaji_br0 where IFNULL(stsnonaktif,'')<>'Y' and DATE_FORMAT(periode,'%Y%m')='$bulan'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select DISTINCT DATE_FORMAT(bulan,'%Y%m') bulan, icabangid 
        from dbmaster.t_spg_validate where DATE_FORMAT(bulan,'%Y%m')='$bulan'";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET icabangid='JKT_MT' WHERE icabangid='0000000007' AND alokid='001'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET icabangid='JKT_RETAIL' WHERE icabangid='0000000007' AND alokid='002'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "DELETE FROM $tmp02 WHERE CONCAT(bulan,icabangid) IN (select distinct IFNULL(CONCAT(IFNULL(bulan,''),IFNULL(icabangid,'')),'') FROM $tmp03)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.*, b.nama nama_cabang from $tmp02 a LEFT JOIN MKT.icabang_o b on a.icabangid=b.icabangid_o";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $adacabangblmvalidate=false;
    $cb_blmsubmit="";
    $query = "select * from $tmp04";
    $tampilcb = mysqli_query($cnmy, $query);
    $ketemucb = mysqli_num_rows($tampilcb);
    if ($ketemucb>0) {
        while ($cb= mysqli_fetch_array($tampilcb)) {
            $nidcabang=$cb['icabangid'];
            $nnmcabang=$cb['nama_cabang'];
            $nalokid=$cb['alokid'];
            
            
            if ($nidcabang=="JKT_MT") $nnmcabang="JAKARTA MT";
            elseif ($nidcabang=="JKT_RETAIL") $nnmcabang="JAKARTA RETAIL";
            
            $cb_blmsubmit=$cb_blmsubmit."".$nnmcabang.", ";
            //$adacabangblmvalidate=true; //dimatikan dulu
        }

        if (!empty($cb_blmsubmit)) {
            $cb_blmsubmit=substr($cb_blmsubmit, 0, -2);
            //echo $cb_blmsubmit;
        }
    }
    // cek validate / submit dari cabang (semua cabang input harus validate)
    
    
    $query = "select * from dbmaster.t_spg_gaji_br_inc WHERE periode='$tgl1'";
    $query = "create TEMPORARY table $tmp05 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER TABLE $tmp01 ADD COLUMN tpotongmkn DECIMAL(20,2), ADD COLUMN jmlbayarmkn DECIMAL(20,2)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN $tmp05 as b on a.id_spg=b.id_spg SET a.tpotongmkn=b.persentase, a.jmlbayarmkn=b.jml_bayar";
    mysqli_query($cnmy, $query); 
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
?>
<script src="js/inputmask.js"></script>
<form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">
        
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>

                <div hidden class='col-sm-3'>
                    <button type='button' class='btn btn-default btn-xs'>Periode & cabang</button> <span class='required'></span>
                   <div class="form-group">
                        <div class='input-group date' id=''>
                            <input type='text' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                            <input type='text' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                            <input type="text" class="form-control" id='e_periodepilih' name='e_periodepilih' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo "$ptanggal"; ?>' Readonly>
                            <input type="text" class="form-control" id='e_periodepilih2' name='e_periodepilih2' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo "$ptanggal2"; ?>' Readonly>
                            <input type="text" class="form-control" id='e_cabangpilih' name='e_cabangpilih' autocomplete="off" required='required'  value='<?PHP echo "$pidcabang"; ?>' Readonly>
                            <input type="text" class="form-control" id='e_status' name='e_status' autocomplete="off" required='required'  value='<?PHP echo "$cket"; ?>' Readonly>
                        </div>
                   </div>
               </div>

                
                <div class='col-sm-3'>
                    <button type='button' class='btn btn-default btn-xs' onclick='HitungJumlahTotalCexBox()'>Grand Total</button> <span class='required'></span>
                   <div class="form-group">
                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo ""; ?>' Readonly>
                   </div>
               </div>
                
                <?PHP if ($cket=="1") { ?>
                
                    <div class='col-sm-6'>
                        <div style="padding-bottom:10px;">&nbsp;</div>
                       <div class="form-group">
                           <input type='button' class='btn btn-success btn-sm' id="s-submit" value="Proses" onclick='disp_confirm_proses("Simpan ?", "input")'>
                           <div style="color:blue; font-weight:bold; font-size:15px;"><br/>Periode incentive yang dipilih : <?PHP echo "$prinsentif"; ?></div>
                       </div>
                   </div>
                
                <?PHP }elseif ($cket=="2") { ?>
                
                    <div class='col-sm-6'>
                        <div style="padding-bottom:10px;">&nbsp;</div>
                       <div class="form-group">
                           <input type='button' class='btn btn-info btn-sm' id="s-submit" value="Un Proses" onclick='disp_confirm_set("hapus", "chkbox_br[]")'>
                       </div>
                   </div>
                
                <?PHP } ?>
                
            </div>
        </div>
        
        <div class="title_left">
            <h4 style="font-size : 12px;">
                <?PHP
                    $noteket = strtoupper($cket);
                    $text="Data Yang Belum Proses";
                    if ($noteket=="2") $text="Data Yang Sudah Proses";
                    if ($noteket=="3") $text="Data Yang Sudah diPending";
                    if ($noteket=="4") $text="Data Yang Sudah Proses FINANCE";
                    if ($noteket=="5") $text="Data Yang Sudah Proses MANAGER";
                    echo "<b>$text</b>";
                ?>
                <br/><br/><br/><b><span style="color:red;">Jumlah Hari Kerja SPG Bulan <?PHP echo "$mybulan : $jmlkerja"; ?> Hari</span></b>
                <br/><br/><b><span style="color:red;">Jumlah Hari Kerja ASPR Bulan <?PHP echo "$mybulan : $jmlkerja_aspr"; ?> Hari</span></b>
                <?PHP if ($cket=="1") { ?>
                <br/><br/><b><span style="color:blue;">Klik pada nama SPG untuk mengisi No BPJS Ketenagakerjaan</span></b>
                <?PHP } ?>
            </h4>
            <?PHP
            //if ($adacabangblmvalidate==true AND $cket=="1") {
            if (!empty($cb_blmsubmit)) {
                echo "<h1 style='font-size : 20px;'>Inputan Cabang : $cb_blmsubmit belum klik SUBMIT</h1>";
            }
            ?>
        </div>
        <div class="clearfix"></div>
        
        <?PHP
            $chkall="";
            $npilihperinc=$pthnblnins;
            $npilihperinc=$pthnblnins;
            if ($noteket!="1") {
                $npilihperinc="";
            }
            if ($cket=="1" AND $ketemudata>0) { 
                $chkall = "<input type='checkbox' id='chkbtnbr' value='deselect' onClick=\"SelAllCheckBox('chkbtnbr', 'chkbox_br[]')\" checked />";
            }else { 
                $chkall = "<input type='checkbox' id='chkbtnbr' value='select' onClick=\"SelAllCheckBox('chkbtnbr', 'chkbox_br[]')\" />";
            }

            $chkall = "<input type='checkbox' id='chkbtnbr' value='select' onClick=\"SelAllCheckBox('chkbtnbr', 'chkbox_br[]')\" />";
            if ((INT)$cket==4) $chkall="";
        ?>
        <table id='dtabelspgpros' class='table table-striped table-bordered' width='100%'>
            <thead>
                <th width='10px'>No</th>
                <th width='20px'>
                    <?PHP echo $chkall; ?>
                </th>
                <th width='200px' align="center">Nama SPG</th>
                <th width='50px' align="center">Jabatan</th>
                <th width='200px' align="center">Total</th>
                <th width='200px' align="center">Std.<br/>hk</th>
                <th width='50px' align="center">Inc Bln</th>
                <th align="center" nowrap>Incentive</th>
                <th align="center" nowrap>Tambahan</th>
                <th align="center" nowrap>Selisih<br/>(Lebih/Kurang)</th>
                <th align="center" nowrap>Hari<br/>Kerja</th>
                <th align="center" nowrap>S</th>
                <th align="center" nowrap>I</th>
                <th align="center" nowrap>A</th>

                <th align="center" nowrap>UC</th>

                <th align="center" nowrap>Gaji<br/>Pokok</th>

                <th align="center" nowrap>Sewa<br/>Kendaraan</th>
                <th align="center" nowrap>Pulsa</th>
                <th align="center" nowrap>BBM</th>
                <th align="center" nowrap>Parkir</th>
                <th align="center" nowrap>GP &<br/>Tunjangan</th>

                <th align="center" nowrap>U. Makan</th>
                <th align="center" nowrap>T. Makan</th>
                
                <th width='150px' align="center">Potongan Mkn</th>
                <th width='150px' align="center">Jml Bayar Mkn</th>
                
                <th width='150px' align="center">BPJS Kerja Karyawan</th>
                <th width='150px' align="center">BPJS Kerja SDM</th>
                    
                <th width='200px' align="center">Total</th>
                <th width='200px' align="center">Total dan BPJS</th>
                <th width='200px' align="center">Gaji System</th>
                    
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $sql = "select * FROM $tmp01 ORDER BY nama";
                $query = mysqli_query($cnmy, $sql);
                while( $sp=mysqli_fetch_array($query) ) {
                    
                    $nincabang=$pidcabang;
                    if ($pidcabang=="JKT_MT") {
                        $nincabang="0000000007001";
                    }elseif ($pidcabang=="JKT_RETAIL") {
                        $nincabang="0000000007002";
                    }
                    
                    if ($cket=="1") $idno=$sp['id_spg'];
                    else $idno=$sp['id_spg'].$nincabang;
                    
                    $pidbrspg=$sp['idbrspg'];
                    $pidspg=$sp['id_spg'];
                    $pnmspg=$sp['nama'];
                    $ptempatspg=$sp['penempatan'];
                    
                    $pidalok=$sp['alokid'];
                    $pidarea=$sp['areaid'];
                    $pidjbt=$sp['jabatid'];
                    $pidzona=$sp['id_zona'];
                    
                    $pnmarea=$sp['nama_area'];
                    $pnmzona=$sp['nama_zona'];
                    $pnmjabatan=$sp['nama_jabatan'];
                    
                    $pblnbpjs=$sp['bulan_bpjs'];
                    
                    
                    if ($noteket=="1") {
                        $pbtnbpjs="btn btn-warning btn-xs";
                        $pcolor="";
                        if (!empty($pblnbpjs)) {
                            //$pcolor=" style='color:blue;' ";
                            $pbtnbpjs="btn btn-dark btn-xs";
                        }

                        $pnbpjs_kerja = "<button type='button' class='$pbtnbpjs' data-toggle='modal' data-target='#myModal' onClick=\"TambahDataBPJSKerja('$pidspg')\">$pnmspg ($pidspg)</button>";
                    }else{
                        $pnbpjs_kerja = "$pnmspg ($pidspg)";
                    }
                    
                    $pjmlhk=$sp['jml_harikerja'];
                    $phkjmlsistem=$sp['njmlharisistem'];
                    
                    $pjmlsakit=$sp['jml_sakit'];
                    $pjmlizin=$sp['jml_izin'];
                    $pjmlalpa=$sp['jml_alpa'];
                    $pjmluc=$sp['jml_uc'];
                    
                    $pinsentif=$sp['insentif'];
                    $pinsentif_booster=$sp['insentif_tambahan'];
                    $psisa_lebihkurang=$sp['lebihkurang'];
                    
                    $pperiodeinc=$sp['periode_insentif'];
                    if (!empty($pperiodeinc) AND $pperiodeinc<>"0000-00-00") $pperiodeinc= date("F Y", strtotime($pperiodeinc));
                    
                    
                    $pasligaji=$sp['gaji_asli'];
                    $pgaji=$sp['gaji'];
                    $psewa=$sp['sewakendaraan'];
                    $ppulsa=$sp['pulsa'];
                    $pbbm=$sp['bbm'];
                    $pparkir=$sp['parkir'];
                    $plain=$sp['lain'];
                    $ptotaltunjangan=$sp['ntunjangan'];
                    
                    $pmakan=$sp['umakan'];
                    
                    $pjmlpotongmkn=$sp['tpotongmkn'];
                    $pjmlbyrmkn=$sp['jmlbayarmkn'];
                    
                    $pbpjskerja_kry=$sp['jmlbpjs_kry'];
                    $pbpjskerja_sdm=$sp['jmlbpjs_sdm'];
                    
                    
                    $pperiodeinc=$sp['periode_insentif'];
                    if (!empty($pperiodeinc) AND $pperiodeinc<>"0000-00-00") $pperiodeinc= date("F Y", strtotime($pperiodeinc));
                    if ((INT)$cket==1){
                        $pperiodeinc=$npilihperinc;
                    }else{
                    }
                    
                    $papvtgl2=$sp['apvtgl2'];
                    $bolehapv=true;
                    if (!empty($papvtgl2) AND $papvtgl2 <> "0000-00-00 00:00:00") $bolehapv=false;
                    
                    
                    if (empty($pinsentif)) $pinsentif=0;
                    if (empty($pinsentif_booster)) $pinsentif_booster=0;
                    if (empty($psisa_lebihkurang)) $psisa_lebihkurang=0;
                    
                    
                    if (empty($pgaji)) $pgaji=0;
                    if (empty($psewa)) $psewa=0;
                    if (empty($ppulsa)) $ppulsa=0;
                    if (empty($pbbm)) $pbbm=0;
                    if (empty($pparkir)) $pparkir=0;
                    if (empty($plain)) $plain=0;
                    if (empty($ptotaltunjangan)) $ptotaltunjangan=0;
                    if (empty($pmakan)) $pmakan=0;
                    if (empty($pjmlpotongmkn)) $pjmlpotongmkn=0;
                    if (empty($pjmlbyrmkn)) $pjmlbyrmkn=0;
                    
                    if (empty($pbpjskerja_kry)) $pbpjskerja_kry=0;
                    if (empty($pbpjskerja_sdm)) $pbpjskerja_sdm=0;
                    
                    
                    $ptotmakan=(double)$pmakan*(double)$pjmlhk;
                    $ipotonganmakan=(double)$ptotmakan*(double)$pjmlpotongmkn;
                    
                    $ptotalgp_tunjangan=(double)$pgaji+(double)$ptotaltunjangan;
                    
                    
                    $nstyle_text=" style='text-align:right; background-color: transparent; border: 0px solid;' ";
                    $nstyle_right=" style='text-align:right;' ";
                    
                    $nrd_incbot="";
                    if ($cket!="1") {
                        $nrd_incbot="Readonly";
                        
                    }elseif ($cket=="1") { 
                        $psisa_lebihkurang=0;
                        $psisa_lebihkurang=0-(DOUBLE)$ipotonganmakan;
                        
                    }
                    
                    
                    if ((double)$pjmlhk==0) {
                        $pgaji=0;
                        $psewa=0;
                        $ppulsa=0;
                        $pbbm=0;
                        $pparkir=0;
                        $ptotalgp_tunjangan=0;
                        $pmakan=0;
                        $ptotmakan=0;
                        $ptotaltunjangan=0;
                        $pbpjskerja_kry=0;
                        $pbpjskerja_sdm=0;
                        
                        $ptotalspg=(double)$pinsentif+(double)$pinsentif_booster+(double)$psisa_lebihkurang;
                        $ptotalspgbpjs=0;
                    }else{
                        $ptotalspg=(double)$ptotalgp_tunjangan+(double)$ptotmakan+(double)$pinsentif+(double)$pinsentif_booster+(double)$psisa_lebihkurang;
                        $ptotalspgbpjs=(double)$ptotalgp_tunjangan+(double)$ptotmakan+(double)$pinsentif+(double)$pinsentif_booster+(double)$psisa_lebihkurang-(double)$pbpjskerja_kry;
                    }
                    
                    
                    $pjmlhk=number_format($pjmlhk,0,",",",");
                    $phkjmlsistem=number_format($phkjmlsistem,0,",",",");
                    $pjmlsakit=number_format($pjmlsakit,0,",",",");
                    $pjmlizin=number_format($pjmlizin,0,",",",");
                    $pjmlalpa=number_format($pjmlalpa,0,",",",");
                    $pjmluc=number_format($pjmluc,0,",",",");
                    $ptotaltunjangan=number_format($ptotaltunjangan,0,",",",");
                    $ptotalgp_tunjangan=number_format($ptotalgp_tunjangan,0,",",",");
                    
                    $pgaji=number_format($pgaji,0,",",",");
                    $psewa=number_format($psewa,0,",",",");
                    $ppulsa=number_format($ppulsa,0,",",",");
                    $pbbm=number_format($pbbm,0,",",",");
                    $pparkir=number_format($pparkir,0,",",",");
                    $plain=number_format($plain,0,",",",");
                    
                    $pmakan=number_format($pmakan,0,",",",");
                    $ptotmakan=number_format($ptotmakan,0,",",",");
                    $pjmlpotongmkn=number_format($pjmlpotongmkn,0,",",",");
                    $pjmlbyrmkn=number_format($pjmlbyrmkn,0,",",",");
                    $ipotonganmakan=number_format($ipotonganmakan,0,",",",");
                    
                    $pinsentif=number_format($pinsentif,0,",",",");
                    $pinsentif_booster=number_format($pinsentif_booster,0,",",",");
                    
                    $pbpjskerja_kry=number_format($pbpjskerja_kry,0,",",",");
                    $pbpjskerja_sdm=number_format($pbpjskerja_sdm,0,",",",");
                    
                    $ptotalspg=number_format($ptotalspg,0,",",",");
                    
                    $ptotalspgbpjs=number_format($ptotalspgbpjs,0,",",",");
                    
                    $pasligaji=number_format($pasligaji,0,",",",");
                    
                    
                    $txt_hrkerja="<input type='text' value='$pjmlhk' size='3px' id='txthrkerja[$idno]' name='txthrkerja[$idno]' class='inputmaskrp2' autocomplete='off' Readonly $nstyle_text>";
                    $txt_stdkerja="<input type='text' value='$phkjmlsistem' size='3px' id='txtstdkerja[$idno]' name='txtstdkerja[$idno]' class='inputmaskrp2' autocomplete='off' Readonly $nstyle_text>";
                    
                    $txt_sakit="<input type='text' value='$pjmlsakit' size='3px' id='txtsakit[$idno]' name='txtsakit[$idno]' class='inputmaskrp2' autocomplete='off' Readonly $nstyle_text>";
                    $txt_izin="<input type='text' value='$pjmlizin' size='3px' id='txtizin[$idno]' name='txtizin[$idno]' class='inputmaskrp2' autocomplete='off' Readonly $nstyle_text>";
                    $txt_alpa="<input type='text' value='$pjmlalpa' size='3px' id='txtalpa[$idno]' name='txtalpa[$idno]' class='inputmaskrp2' autocomplete='off' Readonly $nstyle_text>";
                    $txt_uc="<input type='text' value='$pjmluc' size='3px' id='txtuc[$idno]' name='txtuc[$idno]' class='inputmaskrp2' autocomplete='off' Readonly $nstyle_text>";
                    
                    
                    $txt_incentif="<input type='text' size='8px' id='txtincentif[$idno]' name='txtincentif[$idno]' class='inputmaskrp2' autocomplete='off' "
                            . " value='$pinsentif' onblur=\"HitungJumlahTotalCexBox()\" Readonly $nstyle_text>";
                    $txt_incbooster="<input type='text' size='8px' id='txtincbot[$idno]' name='txtincbot[$idno]' class='inputmaskrp2' autocomplete='off' "
                            . " value='$pinsentif_booster' onblur=\"HitungJumlahTotalCexBox()\" $nrd_incbot $nstyle_right>";
                    $txt_lebihkurang="<input type='text' size='8px' id='txtlebihkurang[$idno]' name='txtlebihkurang[$idno]' class='inputmaskrp2' autocomplete='off' "
                            . " value='$psisa_lebihkurang' onblur=\"HitungJumlahTotalCexBox()\" $nrd_incbot $nstyle_right>";
                    
                    $txt_gp="<input type='text' value='$pgaji' size='8px' id='txtgp[$idno]' name='txtgp[$idno]' class='inputmaskrp2' autocomplete='off' onblur=\"HitungJumlahTotalCexBox()\" Readonly $nstyle_text>";
                    $txt_sewa="<input type='text' value='$psewa' size='8px' id='txtsewa[$idno]' name='txtsewa[$idno]' class='inputmaskrp2' autocomplete='off' onblur=\"HitungJumlahTotalCexBox()\" Readonly $nstyle_text>";
                    $txt_pulsa="<input type='text' value='$ppulsa' size='8px' id='txtpulsa[$idno]' name='txtpulsa[$idno]' class='inputmaskrp2' autocomplete='off' onblur=\"HitungJumlahTotalCexBox()\" Readonly $nstyle_text>";
                    $txt_bbm="<input type='text' value='$pbbm' size='8px' id='txtbbm[$idno]' name='txtbbm[$idno]' class='inputmaskrp2' autocomplete='off' onblur=\"HitungJumlahTotalCexBox()\" Readonly $nstyle_text>";
                    $txt_parkir="<input type='text' value='$pparkir' size='8px' id='txtparkir[$idno]' name='txtparkir[$idno]' class='inputmaskrp2' autocomplete='off' onblur=\"HitungJumlahTotalCexBox()\" Readonly $nstyle_text>";
                    $txt_lain="<input type='hidden' value='$plain' size='8px' id='txtlain[$idno]' name='txtlain[$idno]' class='inputmaskrp2' autocomplete='off' onblur=\"HitungJumlahTotalCexBox()\" Readonly $nstyle_text>";
                    $txt_tujang="<input type='hidden' value='$ptotaltunjangan' size='8px' id='txttunjang[$idno]' name='txttunjang[$idno]' class='inputmaskrp2' autocomplete='off' onblur=\"HitungJumlahTotalCexBox()\" Readonly $nstyle_text>";
                    $txt_tottujanggp="<input type='text' value='$ptotalgp_tunjangan' size='8px' id='txttottunjanggp[$idno]' name='txttottunjanggp[$idno]' class='inputmaskrp2' autocomplete='off' onblur=\"HitungJumlahTotalCexBox()\" Readonly $nstyle_text>";
                    
                    $txt_mkn="<input type='text' value='$pmakan' size='8px' id='txtmakan[$idno]' name='txtmakan[$idno]' class='inputmaskrp2' autocomplete='off' onblur=\"HitungJumlahTotalCexBox()\" Readonly $nstyle_text>";
                    $txt_totmkn="<input type='text' value='$ptotmakan' size='8px' id='txttotmakan[$idno]' name='txttotmakan[$idno]' class='inputmaskrp2' autocomplete='off' onblur=\"HitungJumlahTotalCexBox()\" Readonly $nstyle_text>";
                    
                    $txt_totpotongmkn="<input type='text' value='$pjmlpotongmkn' id='txtpotongmkn[$idno]' name='txtpotongmkn[$idno]' class='inputmaskrp2' autocomplete='off' size='8px' Readonly $nstyle_text>";
                    $txt_byrmkn="<input type='text' value='$pjmlbyrmkn' id='txtbyrmkn[$idno]' name='txtbyrmkn[$idno]' class='inputmaskrp2' autocomplete='off' size='8px' Readonly $nstyle_text>";
                    
                    $txt_inpbpjskry="<input type='text' value='$pbpjskerja_kry' id='txtbpjskry[$idno]' name='txtbpjskry[$idno]' class='inputmaskrp2' autocomplete='off' size='8px' Readonly $nstyle_text>";
                    $txt_inpbpjssdm="<input type='text' value='$pbpjskerja_sdm' id='txtbpjssdm[$idno]' name='txtbpjssdm[$idno]' class='inputmaskrp2' autocomplete='off' size='8px' Readonly $nstyle_text>";
                    
                    $txt_totalall="<input type='text' value='$ptotalspg' id='txttotall[$idno]' name='txttotall[$idno]' class='inputmaskrp2' autocomplete='off' size='8px' Readonly $nstyle_text>";
                    $txt_totalall2="<input type='text' value='$ptotalspg' id='txttotall2[$idno]' name='txttotall2[$idno]' class='inputmaskrp2' autocomplete='off' size='8px' Readonly $nstyle_text>";
                    $txt_totalallbpjs="<input type='text' value='$ptotalspgbpjs' id='txttotallbpjs[$idno]' name='txttotallbpjs[$idno]' class='inputmaskrp2' autocomplete='off' size='8px' Readonly $nstyle_text>";
                    
                    $txt_gajiasli="<input type='text' value='$pasligaji' id='txtgasli[$idno]' name='txtgasli[$idno]' class='inputmaskrp2' autocomplete='off' size='8px' Readonly $nstyle_text>";
                    
                    
                    $txt_bridspg="<input type='text' value='$pidbrspg' id='txtbridspg[$idno]' name='txtbridspg[$idno]' class='' autocomplete='off' size='8px' Readonly>";
                    $txt_cabangid="<input type='text' value='$nincabang' id='txtidcabang[$idno]' name='txtidcabang[$idno]' class='' autocomplete='off' size='8px' Readonly>";
                    $txt_alokid="<input type='text' value='$pidalok' id='txtalokid[$idno]' name='txtalokid[$idno]' class='' autocomplete='off' size='8px' Readonly>";
                    $txt_areaid="<input type='text' value='$pidarea' id='txtareaid[$idno]' name='txtareaid[$idno]' class='' autocomplete='off' size='8px' Readonly>";
                    $txt_zonaid="<input type='text' value='$pidzona' id='txtzonaid[$idno]' name='txtzonaid[$idno]' class='' autocomplete='off' size='8px' Readonly>";
                    $txt_jabatid="<input type='text' value='$pidjbt' id='txtjabatid[$idno]' name='txtjabatid[$idno]' class='' autocomplete='off' size='8px' Readonly>";
                    
                    $txt_dataall="<span hidden>$txt_bridspg $txt_cabangid $txt_alokid $txt_areaid $txt_zonaid $txt_jabatid</span>";
                    
                    
                    

                    
                    $cekbox = "<input type=checkbox value='$idno' id='chkbox_br[$idno]' name='chkbox_br[]' onclick=\"HitungJumlahTotalCexBox()\">";
                    if ($bolehapv==false) $cekbox="";//sudah approve atasan 2 / apv2
                    if ((INT)$cket==4) $cekbox="";
                    if ($adacabangblmvalidate==true AND $cket=="1") {
                        $cekbox="";
                    }
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$cekbox $txt_dataall</td>";
                    echo "<td nowrap>$pnbpjs_kerja</td>";
                    echo "<td nowrap>$pnmjabatan</td>";
                    echo "<td nowrap>$txt_totalall</td>";
                    echo "<td nowrap>$txt_stdkerja</td>";
                    echo "<td nowrap>$pperiodeinc</td>";
                    echo "<td nowrap>$txt_incentif</td>";
                    echo "<td nowrap>$txt_incbooster</td>";
                    echo "<td nowrap>$txt_lebihkurang</td>";
                    echo "<td nowrap>$txt_hrkerja</td>";
                    echo "<td nowrap>$txt_sakit</td>";
                    echo "<td nowrap>$txt_izin</td>";
                    echo "<td nowrap>$txt_alpa</td>";
                    echo "<td nowrap>$txt_uc</td>";
                    echo "<td nowrap>$txt_gp</td>";
                    echo "<td nowrap>$txt_sewa</td>";
                    echo "<td nowrap>$txt_pulsa</td>";
                    echo "<td nowrap>$txt_bbm</td>";
                    echo "<td nowrap>$txt_parkir $txt_lain</td>";
                    echo "<td nowrap>$txt_tottujanggp $txt_tujang</td>";
                    echo "<td nowrap>$txt_mkn</td>";
                    echo "<td nowrap>$txt_totmkn</td>";
                    echo "<td nowrap>$txt_totpotongmkn</td>";
                    echo "<td nowrap>$txt_byrmkn</td>";
                    echo "<td nowrap>$txt_inpbpjskry</td>";
                    echo "<td nowrap>$txt_inpbpjssdm</td>";
                    echo "<td nowrap>$txt_totalall2</td>";
                    echo "<td nowrap>$txt_totalallbpjs</td>";
                    echo "<td nowrap>$txt_gajiasli</td>";
                    
                    echo "</tr>";
                    
                    
                    
                    $no++;
                    
                }
                ?>
            </tbody>
            
        </table>
        
        
        
        
        
        
    </div>
    
</form>


<script>
    $(document).ready(function() {
        //alert(etgl1);
        var dataTable = $('#dtabelspgpros').DataTable( {
            //"stateSave": true,
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "ordering": false,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                //{ "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [4, 5] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 400,
            "scrollX": true /*,
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            responsive: true */
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
        $(".inputmaskrp2").inputmask({ 'alias' : 'decimal', rightAlign: false, 'groupSeparator': '.','autoGroup': true });
    } );
    
    function TambahDataBPJSKerja(eidspg){
        $.ajax({
            type:"post",
            url:"module/md_m_spg_importdata/tambah_bpjskerja.php?module=viewisibpjskerja",
            data:"uidspg="+eidspg,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
    function SelAllCheckBox(nmbuton, data){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);

        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
        HitungJumlahTotalCexBox();
    }
    
    
    function HitungJumlahTotalCexBox() {
        var chk_arr1 =  document.getElementsByName('chkbox_br[]');
        var chklength1 = chk_arr1.length;
        var newchar = '';

        var nTotal_perspg="0";
        var nTotal_perspgbpjs="0";
        var nTotal_="0";
        
        for(k=0;k< chklength1;k++)
        {
            
            //if (chk_arr1[k].checked == true) {
            
                var kata = chk_arr1[k].value;
                var fields = kata.split('-');
                
                var anm_jml_inc="txtincentif["+fields[0]+"]";
                var ajml_inc=document.getElementById(anm_jml_inc).value;
                ajml_inc = ajml_inc.split(',').join(newchar);
                
                var anm_jml_incbot="txtincbot["+fields[0]+"]";
                var ajml_incbot=document.getElementById(anm_jml_incbot).value;
                ajml_incbot = ajml_incbot.split(',').join(newchar);
                
                var anm_jml_kl="txtlebihkurang["+fields[0]+"]";
                var ajml_kl=document.getElementById(anm_jml_kl).value;
                ajml_kl = ajml_kl.split(',').join(newchar);
                
                var anm_jml_tjgp="txttottunjanggp["+fields[0]+"]";
                var ajml_tjgp=document.getElementById(anm_jml_tjgp).value;
                ajml_tjgp = ajml_tjgp.split(',').join(newchar);
                
                var anm_jml_mkn="txttotmakan["+fields[0]+"]";
                var ajml_mkn=document.getElementById(anm_jml_mkn).value;
                ajml_mkn = ajml_mkn.split(',').join(newchar);
                
                //belum dijumlah
                var anm_jml_byrmkn="txtbyrmkn["+fields[0]+"]";
                var ajml_byrmkn=document.getElementById(anm_jml_byrmkn).value;
                ajml_byrmkn = ajml_byrmkn.split(',').join(newchar);
                
                var anm_jml_bpjskry="txtbpjskry["+fields[0]+"]";
                var ajml_bpjskry=document.getElementById(anm_jml_bpjskry).value;
                ajml_bpjskry = ajml_bpjskry.split(',').join(newchar);
                
                //total per spg
                nTotal_perspg =parseFloat(ajml_inc)+parseFloat(ajml_incbot)+parseFloat(ajml_kl)+parseFloat(ajml_tjgp)+parseFloat(ajml_mkn);
                nTotal_perspgbpjs =parseFloat(ajml_inc)+parseFloat(ajml_incbot)+parseFloat(ajml_kl)+parseFloat(ajml_tjgp)+parseFloat(ajml_mkn)-parseFloat(ajml_bpjskry);
                
                var anm_jml_total="txttotall["+fields[0]+"]";
                document.getElementById(anm_jml_total).value=nTotal_perspg;
                
                var anm_jml_total2="txttotall2["+fields[0]+"]";
                document.getElementById(anm_jml_total2).value=nTotal_perspg;
                
                var anm_jml_totalbpjs="txttotallbpjs["+fields[0]+"]";
                document.getElementById(anm_jml_totalbpjs).value=nTotal_perspgbpjs;
                
                if (chk_arr1[k].checked == true) {
                    nTotal_ =parseFloat(nTotal_)+parseFloat(nTotal_perspg);
                }
                
            //}
            
        }
        
        document.getElementById('e_jmlusulan').value=nTotal_;
        
    }
    
    
    function disp_confirm_proses(pText_,ket)  {
        
        var ijml =document.getElementById('e_jmlusulan').value;
        if(ijml==""){
            ijml="0";
        }
        if (ijml=="0") {
            alert("jumlah masih kosong...");
            return false;
        }
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/md_m_spg_proses/aksi_spgproses.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
    }
    
    
    function disp_confirm_set(ket, cekbr){
        
        var iperiode =  document.getElementById('e_periodepilih').value;
        var iperiodeins =  document.getElementById('e_periodepilih2').value;
        var icabang =  document.getElementById('e_cabangpilih').value;
        var istatus =  document.getElementById('e_status').value;
        
        if (ket=="simpan" || ket=="simpanpending") {
            var ijml =document.getElementById('e_jmlusulan').value;
            if(ijml==""){
                ijml="0";
            }
            if (ijml=="0") {
                alert("jumlah masih kosong...");
                return false;
            }
            
            var itglpengajuan =  document.getElementById('e_tglpengajuan').value;
            var itipests =  document.getElementById('cb_tipests').value;
        
        }else{
            var itglpengajuan =  "";
            var itipests =  "";
        }
        if (ket=="simpan") {
            var cmt = confirm('Apakah akan melakukan proses '+ket+' ...?');
        }else if (ket=="hapus") {
            var cmt = confirm('Apakah akan melakukan unproses ...?');
        }else{
            var cmt = confirm('Apakah akan melakukan proses ...?');
        }
        if (cmt == false) {
            return false;
        }
        
        var chk_arr =  document.getElementsByName(cekbr);
        var chklength = chk_arr.length;             
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
                
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
            
            //jml_inc = jml_inc.substring(0, lastIndex);
            
        }else{
            alert("Tidak ada data yang diproses...!!!");
            return false;
        }
        
        
        //alert(jml_inc); return false;
        
        var txt="";
        if (ket=="reject" || ket=="pending") {
            var textket = prompt("Masukan alasan "+ket+" : ", "");
            if (textket == null || textket == "") {
                txt = textket;
            } else {
                txt = textket;
            }
        }
        
        var jml_inc="";
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/md_m_spg_proses/viewdata.php?module="+ket,
            data:"unoidbr="+allnobr+"&utgl="+iperiode+"&ucabang="+icabang+"&utglpengajuan="+itglpengajuan+"&utipests="+itipests+"&utglinsentif="+iperiodeins+"&umlincbot="+jml_inc,
            success:function(data){
                $("#loading2").html("");
                if (istatus=="1") {
                    RefreshDataTabel('1')
                }else if (istatus=="2") {
                    RefreshDataTabel('2')
                }else if (istatus=="3") {
                    RefreshDataTabel('3')
                }
                alert(data);
            }
        });
        
    }
    
    
</script>


<style>
    .divnone {
        display: none;
    }
    #dtabelspgpros th {
        font-size: 13px;
    }
    #dtabelspgpros td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp04");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp05");
    
    mysqli_query($cnmy, "drop table $tmp01");
    mysqli_close($cnmy);
?>