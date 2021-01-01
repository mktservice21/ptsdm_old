<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>

<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }
</style>

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<?php
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    include "../../config/library.php";
    $cnit=$cnmy;
    $date1=$_POST['utgl'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $bulan= date("Ym", strtotime($date1));
    
    $_SESSION['CLSLKCA']=date("F Y", strtotime($date1));
    
    
    $tglnow = date("Y-m-d");
    $tgl01 = $_POST['utgl'];
    $periode1 = date("Y-m", strtotime($tgl01));
    $per1 = date("F Y", strtotime($tgl01));
    
    $stsreport = $_POST['usts'];
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRRETRLCLS01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DTBRRETRLCLS02_".$_SESSION['IDCARD']."_$now ";
    $tmp03 =" dbtemp.DTBRRETRLCLS03_".$_SESSION['IDCARD']."_$now ";
    $tmp04 =" dbtemp.DTBRRETRLCLS04_".$_SESSION['IDCARD']."_$now ";
    
    $query = "select DISTINCT a.bulan, a.karyawanid, b.nama, a.divisi, a.saldo, a.ca1, IFNULL(a.ca1,0)-IFNULL(a.saldo,0) as selisih 
        from dbmaster.t_brrutin_ca_close a JOIN hrd.karyawan b on a.karyawanid=b.karyawanId 
        WHERE DATE_FORMAT(a.bulan,'%Y-%m')='$periode1' AND IFNULL(IFNULL(a.ca1,0)-IFNULL(a.saldo,0),0) > 0
        ORDER BY b.nama";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    //cari pengembalian hanya status ots=1
    $query = "select bulan, karyawanid, tgl_kembali, keterangan, sum(kembali_rp) kembali_rp from dbmaster.t_brrutin_outstanding WHERE 
        DATE_FORMAT(bulan,'%Y-%m')='$periode1' AND IFNULL(ots_status,'')='1' AND divisi <> 'OTC' group by 1,2,3,4";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    //cari kelebihan status ots <> 1
    $query = "select ots_status, karyawanid, sum(kembali_rp) kembali_rp from dbmaster.t_brrutin_outstanding WHERE 
        DATE_FORMAT(bulan,'%Y-%m')='$periode1' AND IFNULL(ots_status,'')<>'1' AND divisi <> 'OTC' group by 1,2";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "SELECT a.*, c.ots_status, b.tgl_kembali, b.keterangan, b.kembali_rp from $tmp01 a LEFT JOIN $tmp02 b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN $tmp04 c on a.karyawanid=c.karyawanid";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
?>

<div class='x_content'>


    <table id='datatablepostdeth' class='table table-striped table-bordered' width="100%">
        <thead>
            <tr>
                <th width='4px'>No</th>
                <th width='200px'>Karyawan</th>
                <th width='100px'>Divisi</th>
                <th width='100px'>LK</th>
                <th width='100px'>CA</th>
                <th width='100px'>Selisih</th>
                <th width='100px'>Jumlah Kembali</th>
                <th width='100px'>Tgl. Kembali</th>
                <th width='100px'>Keterangan</th>
                <th width='100px'></th>
            </tr>
        </thead>
        <tbody>
        <?PHP
            $no=1;
            $query = "select * from $tmp03 order by nama, karyawanid";
            $tampil = mysqli_query($cnmy, $query);
            while( $row=mysqli_fetch_array($tampil) ) {
                $pidkry=$row['karyawanid'];
                $pnmkry=$row['nama'];
                $pdivisi=$row['divisi'];
                $psaldo=$row['saldo'];
                $pca=$row['ca1'];
                $pselisih=$row['selisih'];
                
                $ptglkembali=$row['tgl_kembali'];
                if ($ptglkembali=="0000-00-00") $ptglkembali="";
                $pketkembali=$row['keterangan'];
                $potsstatus=$row['ots_status'];
                $pkembalirp=$row['kembali_rp'];
                
                $cn_isi_kembali="";
                $cn_div_hidden="";
                if (!empty($row['kembali_rp']) AND !empty($ptglkembali)) {
                    //$cn_div_hidden="hidden";
                    $cn_isi_kembali=number_format($pkembalirp,0,",",",");
                }
                
                $finchksama="<span $cn_div_hidden><input type='checkbox' id='txtchksama$no' name='txtchksama$no' onClick=\"SamakanSelisih('txtchksama$no', 'txtjmlselisih$no', 'txtjmlkembali$no', 'tglskrang$no', 'txttglkembali$no')\" value='select'></span>";
                
                $fintglskrang="<input type='hidden' id='tglskrang$no' name='tglskrang$no' class='input-sm' value='$tglnow'>";
                $finnmkry="<input type='hidden' id='txtnmkry$no' name='txtnmkry$no' class='input-sm' value='$pnmkry'>";
                $finidkry="<input type='hidden' id='txtidkry$no' name='txtidkry$no' class='input-sm' value='$pidkry'>";
                $finiddivisi="<input type='hidden' id='txtiddiv$no' name='txtiddiv$no' class='input-sm' value='$pdivisi'>";
                $finbln="<input type='hidden' id='txtbln$no' name='txtbln$no' class='input-sm' value='$tgl01'>";
                $finjmlselisih="<input type='hidden' size='10px' id='txtjmlselisih$no' name='txtjmlselisih$no' class='input-sm inputmaskrp2' autocomplete='off' value='$pselisih'>";
                $finjmlca="<input type='hidden' size='10px' id='txtjmlca$no' name='txtjmlca$no' class='input-sm inputmaskrp2' autocomplete='off' value='$pca'>";
                $finjmlsaldo="<input type='hidden' size='10px' id='txtjmlsaldo$no' name='txtjmlsaldo$no' class='input-sm inputmaskrp2' autocomplete='off' value='$psaldo'>";
                
                
                $finjmlkembali="<span $cn_div_hidden><input type='text' size='10px' id='txtjmlkembali$no' name='txtjmlkembali$no' class='input-sm inputmaskrp2' autocomplete='off' value='$pkembalirp'></span>";
                $fintglkembali="<input type='date' name='txttglkembali$no' id='txttglkembali$no' size='10px' value='$ptglkembali'>";
                $finket="<input type='text' size='10px' id='txtket$no' name='txtket$no' class='input-sm' autocomplete='off' value='$pketkembali'>";
                
                /*
                $finsts="<div><select id='cbsts$no' name='cbsts$no' class='input-sm' >"
                        . "<option value='1' selected>Outstanding</option>"
                        . "<option value='2'>Adjustment</option>"
                        . "</select></div>";
                */
                $sel3="selected";
                $sel4="";
                if ($potsstatus=="4"){
                    $sel3="";
                    $sel4="selected";    
                }
                $finsts="<div><select id='cbsts$no' name='cbsts$no' class='input-sm' >"
                        . "<option value='3' $sel3>Pembulatan</option>"
                        . "<option value='4' $sel4>Hutang Piutang</option>"
                        . "</select></div>";
                
                $fsimpan="'txtbln$no', 'txtidkry$no', 'txtiddiv$no', 'txtjmlsaldo$no', 'txtjmlca$no', 'txtjmlselisih$no', 'txtjmlkembali$no', 'txttglkembali$no', 'cbsts$no', 'txtket$no', 'txtnmkry$no'";
                $simpandata= "<span $cn_div_hidden><input type='button' class='btn btn-info btn-xs' id='s-submit' value='Save' onclick=\"SaveData('input', $fsimpan)\"></span>";
                $hapusdata= "<input type='button' class='btn btn-danger btn-xs' id='s-submit' value='Hapus' onclick=\"SaveData('hapus', $fsimpan)\">";
                        
                
                $psaldo=number_format($psaldo,0,",",",");
                $pca=number_format($pca,0,",",",");
                $pselisih=number_format($pselisih,0,",",",");
                
                
                echo "<tr>";
                echo "<td>$no $fintglskrang $finbln $finidkry $finnmkry $finiddivisi $finjmlsaldo $finjmlca $finjmlselisih</td>";
                echo "<td nowrap>$pnmkry</td>";
                echo "<td nowrap>$pdivisi</td>";
                echo "<td align='right'>$psaldo</td>";
                echo "<td align='right'>$pca</td>";
                echo "<td align='right'>$pselisih</td>";
                echo "<td align='right'><table><tr><td>$finchksama</td><td>$finjmlkembali $cn_isi_kembali</td></tr></table></td>";
                echo "<td>$fintglkembali</td>";
                //echo "<td>$finsts</td>";
                echo "<td><table><tr><td>$finsts</td><td>$finket</td></tr></table></td>";
                echo "<td nowrap>$simpandata $hapusdata</td>";
                echo "</tr>";
                
                $no++;
            }
        ?>
        </tbody>
    </table>
    
</div>
            

<?PHP
    goto hapusdata;
    
    include ("../../module/mod_br_closing_lkca/seleksi_data_lk_ca.php");


    
    mysqli_query($cnit, "alter table $tmp01 add column nselisih DECIMAL(20,2)");
    mysqli_query($cnit, "UPDATE $tmp01 SET nselisih=IFNULL(ca1,0)-IFNULL(saldo,0)");
    
    mysqli_query($cnit, "DELETE FROM $tmp01 WHERE IFNULL(nselisih,0)<=0");
    /*
    $query ="select * from $tmp01";
    $query = "create table $tmp10 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
     * 
     */
?>

<form method='POST' action='<?PHP echo "?module=$_POST[module]&act=input&idmenu=$_POST[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_POST['module']; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_POST['idmenu']; ?>' Readonly>
    <input type='hidden' id='u_tgl1' name='u_tgl1' value='<?PHP echo $tgl1; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    
    <div class='x_content'>
        
        <?PHP
            $chkall = "<input type='checkbox' id='pilihsemua' name='pilihsemua' value='select' onClick=\"SelAllCheckBox('pilihsemua', 'chkpilih[]')\"/>";
            
        ?>
        
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                <th width="30px" align="center" nowrap><?PHP echo $chkall; ?></th>
                <th align="center" nowrap>NAMA</th>
                <th align="center" nowrap>DIVISI</th>
                <th align="center" nowrap>No LK</th>
                <th align="center" nowrap>Credit</th>
                <th align="center" nowrap>Saldo REAL</th>
                <th align="center" nowrap>CA <?PHP echo $per1; ?></th>
                <th align="center" nowrap>Selisih</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    
                    $gtotjumlah=0;
                    $gtotca1=0;
                    $gtotselisih=0;
                    $gtotca2=0;
                    $gtottrans=0;
                    $nourutpilih="";
                    
                    $query = "select distinct divisi, karyawanid, nama_karyawan, saldo, ca1, ca2 from $tmp01 order by divisi, nama_karyawan, karyawanid";
                    $result = mysqli_query($cnit, $query);
                    $records = mysqli_num_rows($result);
                    
                    if ($records) {
                        $reco = 1;
                        while ($row = mysqli_fetch_array($result)) {
                            $pdivisi=$row['divisi'];
                            $pkaryawanid=$row['karyawanid'];
                            $pnmkaryawan=$row['nama_karyawan'];
                            $prprutin=number_format($row['saldo'],0,",",",");
                            $pca1=number_format($row['ca1'],0,",",",");
                            $pca2=number_format($row['ca2'],0,",",",");
                            
                            $pselisih=$row['ca1']-$row['saldo'];
                            
                            $pjmltrans=$row['ca2']-$pselisih;
                            
                            if ($pselisih>0 AND $row['ca2']==0) $pjmltrans=0;
                            elseif ($pselisih>0 AND $row['ca2']>0) $pjmltrans=$row['ca2'];
                            elseif ($pselisih==0 AND $row['ca2']>0) $pjmltrans=$row['ca2'];
                            
                            $gtotca1=$gtotca1+$row['ca1'];
                            $gtotca2=$gtotca2+$row['ca2'];
                            $gtotselisih=$gtotselisih+$pselisih;
                            $gtottrans=$gtottrans+$pjmltrans;
                            
                            $query = "select * from $tmp01 where karyawanid='$pkaryawanid' order by idrutin";
                            $result2 = mysqli_query($cnit, $query);
                                    
                            
                            $belum=false;    
                            while ($row2 = mysqli_fetch_array($result2)) {
                                
                                
                                $pnolk=$row2['idrutin'];
                                $pidca1=$row2['idca1'];
                                $pidca2=$row2['idca2'];
                                $pketerangan=$row2['keterangan'];
                                $pjumlah=number_format($row2['credit'],0,",",",");
                                $gtotjumlah=$gtotjumlah+$row2['credit'];
                                
                                
                                
                                $chkck="";
                                $pnourutnya=$row2['nourut'];
                                $ceklisnya = "<input type='checkbox' value='$pnourutnya' name='chkpilih[]' id='chkpilih[]' class='cekbr' $chkck>";
                                
                                if ($belum==true) {
                                    echo "<tr>";
                                    echo "<td></td>";
                                    echo "<td></td>";
                                    echo "<td></td>";
                                    
                                    echo "<td nowrap>$pnolk</td>";
                                    echo "<td nowrap align='right'>$pjumlah</td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "</tr>";
                                    
                                }else{
                                    $pselisih=number_format($pselisih,0,",",",");
                                    $pjmltrans=number_format($pjmltrans,0,",",",");
                                    echo "<tr>";
                                    echo "<td>$ceklisnya</td>";
                                    echo "<td>$pnmkaryawan</td>";
                                    echo "<td>$pdivisi</td>";
                                    echo "<td nowrap>$pnolk</td>";
                                    echo "<td nowrap align='right'>$pjumlah</td>";
                                    echo "<td nowrap align='right'>$prprutin</td>";
                                    echo "<td nowrap align='right'>$pca1</td>";
                                    echo "<td nowrap align='right'>$pselisih</td>";
                                    echo "</tr>";
                                    
                                }
                                
                                $belum=true;
                                $no++;
                                $reco++;
                            }
                            
                            
                                
                        }
                        
                        
                        $gtotjumlah=number_format($gtotjumlah,0,",",",");
                        $gtotca1=number_format($gtotca1,0,",",",");
                        $gtotselisih=number_format($gtotselisih,0,",",",");
                        $gtotca2=number_format($gtotca2,0,",",",");
                        $gtottrans=number_format($gtottrans,0,",",",");
                        
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";

                        echo "<td nowrap></td>";
                        echo "<td nowrap align='right'><b>$gtotjumlah</b></td>";
                        echo "<td nowrap align='right'><b>$gtotjumlah</b></td>";
                        echo "<td nowrap align='right'><b>$gtotca1</b></td>";
                        echo "<td nowrap align='right'><b>$gtotselisih</b></td>";
                        echo "</tr>";
                        
                        if (!empty($nourutpilih)) $nourutpilih="(".substr($nourutpilih, 0, -1).")";
                        
                        if ($stsreport=="B") {
                            $gtotjumlah=0;
                            $gtotca1=0;
                            $gtotselisih=0;
                            $gtotca2=0;
                            $gtottrans=0;
                        }
                        
                    }
                    
                ?>
            </tbody>
        </table>

    </div>
    

    <div class='col-md-12 col-sm-12 col-xs-12'>

        <div id="div_jumlah">
            <div class='x_panel'>
                <div class='x_content'>
                    <div class='col-md-12 col-sm-12 col-xs-12'>


                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                            <div class='col-xs-3'>
                                 <div id='loading2'></div>
                                 
                                 <?PHP if ($stsreport=="B") { ?>
                                    <button type='button' class='btn btn-danger btn-xs' onclick='HitungTotalJumlah()'>Hitung Jumlah</button> <span class='required'></span>
                                 <?PHP } ?>
                                <input type='hidden' id='e_per1' name='e_per1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $per1; ?>' Readonly>
                                <input type='hidden' id='e_per2' name='e_per2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $per2; ?>' Readonly>
                                <input type='hidden' id='e_periode' name='e_periode' class='form-control col-md-7 col-xs-12' value='<?PHP echo $tgl1; ?>' Readonly>
                                <input type='hidden' id='e_nourut' name='e_nourut' class='form-control col-md-7 col-xs-12' value='<?PHP echo $nourutpilih; ?>' Readonly>
                                <input type='hidden' id='e_sudah' name='e_sudah' class='form-control col-md-7 col-xs-12' value='<?PHP echo $sudahclosing; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Saldo Real <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_saldo' name='e_saldo' class='form-control col-md-7 col-xs-12' value='<?PHP echo $gtotjumlah; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>CA <?PHP echo $per1; ?><span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_ca1' name='e_ca1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $gtotca1; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Selisih <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_selisih' name='e_selisih' class='form-control col-md-7 col-xs-12' value='<?PHP echo $gtotselisih; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>CA <?PHP echo $per2; ?><span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_ca2' name='e_ca2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $gtotca2; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah Trsf. <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_jmltrsf' name='e_jmltrsf' class='form-control col-md-7 col-xs-12' value='<?PHP echo $gtottrans; ?>' Readonly>
                            </div>
                        </div>
    
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Trsf. <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <div class='input-group date' id='tgl1'>
                                    <input type='text' id='e_periode01' name='e_periode01' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $ptgltrans; ?>' data-inputmask="'mask': '99/99/9999'">
                                    <span class="input-group-addon">
                                       <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Bukti <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_nobukti' name='e_nobukti' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnobukti; ?>'>
                            </div>
                        </div>
                        

                        <?PHP if (!empty($nourutpilih)) { ?>
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                            <div class='col-xs-9'>
                                <div class="checkbox">
                                    <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo "simpan"; ?>")'>Simpan</button>
                                    <button type='button' class='btn btn-danger' id="btnhapus" name="btnhapus" onclick='disp_confirm("Hapus ?", "<?PHP echo "hapus"; ?>")'>Hapus</button>
                                </div>
                            </div>
                        </div>
                        <?PHP } ?>
                        
                    </div>
                </div>
            </div>
            
        </div>

    </div>

</form>
    

<?PHP
    mysqli_query($cnit, "DELETE FROM dbmaster.tmp_lk_closing WHERE idsession='$_SESSION[IDSESI]'");
    
        $query = "INSERT INTO dbmaster.tmp_lk_closing (nourut, tglinput, bulan, idrutin, idca1, idca2, userid, idsession, jumlah, totalrutin, ca1, ca2, karyawanid, divisi, sts) "
                . " select nourut, CURRENT_DATE() tglinput, '$tgl1' bulan, idrutin, idca1, idca2, '$_SESSION[IDCARD]' userid, "
                . " '$_SESSION[IDSESI]', credit, saldo, ca1, ca2, karyawanid, divisi, sts FROM $tmp01";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    mysqli_query($cnit, "drop TEMPORARY table $tmp01");
    mysqli_query($cnit, "drop TEMPORARY table $tmp08");
?>

<style>
    .divnone {
        display: none;
    }
    #datatablespggj th {
        font-size: 12px;
    }
    #datatablespggj td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }

    .form-group, .input-group, .control-label {
        margin-bottom:2px;
    }
    .control-label {
        font-size:11px;
    }
    #datatable input[type=text], #tabelnobr input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:11px;
        height: 25px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }

    table.datatable, table.tabelnobr {
        color: #000;
        font-family: Helvetica, Arial, sans-serif;
        width: 100%;
        border-collapse:
        collapse; border-spacing: 0;
        font-size: 11px;
        border: 0px solid #000;
    }

    table.datatable td, table.tabelnobr td {
        border: 1px solid #000; /* No more visible border */
        height: 10px;
        transition: all 0.1s;  /* Simple transition for hover effect */
    }

    table.datatable th, table.tabelnobr th {
        background: #DFDFDF;  /* Darken header a bit */
        font-weight: bold;
    }

    table.datatable td, table.tabelnobr td {
        background: #FAFAFA;
    }

    /* Cells in even rows (2,4,6...) are one color */
    tr:nth-child(even) td { background: #F1F1F1; }

    /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
    tr:nth-child(odd) td { background: #FEFEFE; }

    tr td:hover.biasa { background: #666; color: #FFF; }
    tr td:hover.left { background: #ccccff; color: #000; }

    tr td.center1, td.center2 { text-align: center; }

    tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
    tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
    /* Hover cell effect! */
    tr td {
        padding: -10px;
    }

</style>

<script>
    
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
    }    
    
    function HitungTotalJumlah() {
        var chk_arr =  document.getElementsByName('chkpilih[]');
        var chklength = chk_arr.length;             
        var allnobr="";
        
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                var kata = chk_arr[k].value;
                var fields = kata.split('-');
                //allnobr =allnobr + "'"+fields[0]+"',";
                allnobr =allnobr + fields[0]+",";
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }else{
            alert("Tidak ada data yang dipilih...!!!");
            return false;
        }
        var eper1 =document.getElementById('e_per1').value;
        var eper2 =document.getElementById('e_per2').value;
        var eperiode =document.getElementById('e_periode').value;
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_closing_lkca/viewdata.php?module=hitungtotal",
            data:"uurut="+allnobr+"&uper1="+eper1+"&uper2="+eper2+"&uperiode="+eperiode,
            success:function(data){
                $("#loading2").html("");
                $("#div_jumlah").html(data);
            }
        });
        
    }
</script>

<script>
    
    function disp_confirm(pText_, eact)  {
        
        var enourut =document.getElementById('e_nourut').value;
        var etgltrans =document.getElementById('e_periode01').value;
        var enobukti =document.getElementById('e_nobukti').value;
        var esudah =document.getElementById('e_sudah').value;

        if (enourut=="") {
            alert("Tidak ada data yang dipilih...");
            return false;    
        }
            
        if (eact=="") {
            var chk_arr =  document.getElementsByName('chkpilih[]');
            var chklength = chk_arr.length;             
            var allnobr="";

            for(k=0;k< chklength;k++)
            {
                if (chk_arr[k].checked == true) {
                    var kata = chk_arr[k].value;
                    var fields = kata.split('-');
                    //allnobr =allnobr + "'"+fields[0]+"',";
                    allnobr =allnobr + fields[0]+",";
                }
            }
            if (allnobr.length > 0) {
                var lastIndex = allnobr.lastIndexOf(",");
                allnobr = "("+allnobr.substring(0, lastIndex)+")";
            }else{
                alert("Tidak ada data yang dipilih...!!!");
                return false;
            }
            
            var eact="input";
            var pText_="Simpan";
            if (eact=="hapus") { var pText_="Hapus"; var eact="hapus"; }

        }
        
        //alert(eact); return false;
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
                $.ajax({
                    type:"post",
                    url:"module/mod_br_closing_lkca/aksi_closing_lkca.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"unourut="+enourut+"&utgltrans="+etgltrans+"&unobukti="+enobukti+"&usudahpernah="+esudah+"&uact="+eact,
                    success:function(data){
                        $("#loading2").html("");
                        alert(data);
                        HideHitungSave();
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    function HideHitungSave() {
        document.getElementById("btnhitung").style.visibility = "hidden";
        document.getElementById("btnsave").style.visibility = "hidden";
    }
</script>

<script type="text/javascript">
    $(function() {
        $('#e_periode01').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            /*
            minDate: '0',
            maxDate: '+2Y',
            */
            onSelect: function(dateStr) {
            } 
        });
    });
</script>


<script>
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("myBtn").style.display = "block";
      } else {
        document.getElementById("myBtn").style.display = "none";
      }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    }
</script>


<?PHP
hapusdata:
    mysqli_query($cnit, "drop TEMPORARY table $tmp01");
    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
    mysqli_query($cnit, "drop TEMPORARY table $tmp03");
    mysqli_query($cnit, "drop TEMPORARY table $tmp04");
?>

<script>

$(document).ready(function() {
    var table = $('#datatablepostdeth').DataTable({
        fixedHeader: true,
        "ordering": true,
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "displayLength": -1,
        "order": [[ 0, "asc" ]],
        bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
        "bPaginate": true
    } );

} );

    function SamakanSelisih(nmbuton, eselisih, utextkembali, utglskr, upilihtgl){
        var jmlselisih =document.getElementById(eselisih).value;
        var tglsekarang =document.getElementById(utglskr).value;
        
        var button = document.getElementById(nmbuton);
        if(button.value == 'select'){
            document.getElementById(utextkembali).value=jmlselisih;
            document.getElementById(upilihtgl).value=tglsekarang;
            button.value = 'deselect'
        }else{
            document.getElementById(utextkembali).value="0";
            document.getElementById(upilihtgl).value="";
            button.value = 'select';
        }
    }
    
    
    function SaveData(eact, abln, akry, adiv, asaldo, aca, aselisih, akembali, atgl, asts, aket, anamakaryawan)  {
        var ebln =document.getElementById(abln).value;
        var ekry =document.getElementById(akry).value;
        var ediv =document.getElementById(adiv).value;
        var esaldo =document.getElementById(asaldo).value;
        var eca =document.getElementById(aca).value;
        var eselisih =document.getElementById(aselisih).value;
        var ekembali =document.getElementById(akembali).value;
        var etgl =document.getElementById(atgl).value;
        var ests =document.getElementById(asts).value;
        var eket =document.getElementById(aket).value;
        var enamakaryawan =document.getElementById(anamakaryawan).value;
        
        if (ebln=="" && ekry==""){
            alert("tidak ada data yang disimpan...");
            return 0;
        }
        
        //alert(eselisih);
        var pText_="Simpan";
        if (eact=="hapus") var pText_="Hapus";
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                $.ajax({
                    type:"post",
                    url:"module/mod_br_otsdlkca_eth/aksi_otsd_lkca.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"ubln="+ebln+"&ukry="+ekry+"&udiv="+ediv+"&usaldo="+esaldo+"&uca="+eca+"&uselisih="+eselisih+"&ukembali="+ekembali+"&utgl="+etgl+"&usts="+ests+"&uketerangan="+eket+"&unamakaryawan="+enamakaryawan,
                    success:function(data){
                        if (data.length > 1) {
                            alert(data);
                        }
                        
                        if (eact=="hapus" && data.length <= 1) {
                            
                            document.getElementById(akembali).value="";
                            document.getElementById(aket).value="";
                            document.getElementById(atgl).value="";
                            
                        }
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>

<style>
    .divnone {
        display: none;
    }
    #datatablepostdeth th {
        font-size: 12px;
    }
    #datatablepostdeth td { 
        font-size: 11px;
    }
</style>