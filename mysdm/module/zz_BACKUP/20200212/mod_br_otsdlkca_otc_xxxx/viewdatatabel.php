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
    $tmp05 =" dbtemp.DTBRRETRLCLS05_".$_SESSION['IDCARD']."_$now ";
    $tmp06 =" dbtemp.DTBRRETRLCLS06_".$_SESSION['IDCARD']."_$now ";
    
    
    $query = "select a.idrutin, a.divisi, a.karyawanid, b.nama, a.nama_karyawan, a.icabangid, a.areaid, a.jumlah,
        a.atasan1, a.atasan2, a.atasan3, a.atasan4  
        from dbmaster.t_brrutin0 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanId WHERE 
        IFNULL(a.stsnonaktif,'')<>'Y' AND a.divisi='OTC' AND a.kode=2 
        AND DATE_FORMAT(a.bulan,'%Y-%m')='$periode1'";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "select a.idca, a.divisi, a.karyawanid, b.nama, a.nama_karyawan, a.icabangid, a.areaid, a.jumlah,
        a.atasan1, a.atasan2, a.atasan3, a.atasan4, batal, alasan_batal 
        from dbmaster.t_ca0 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanId WHERE 
        IFNULL(a.stsnonaktif,'')<>'Y' AND a.divisi='OTC'  
        AND DATE_FORMAT(a.periode,'%Y-%m')='$periode1'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    $query = "update $tmp01 set nama=nama_karyawan WHERE karyawanid='0000002200'"; 
    mysqli_query($cnit, $query);
    
    $query = "update $tmp01 a JOIN dbmaster.t_karyawan_kontrak b ON a.nama_karyawan=b.nama AND"
            . " a.atasan1=b.atasan1 AND a.atasan2=b.atasan2 AND a.atasan3=b.atasan3 AND a.atasan4=b.atasan4 "
            . " SET a.karyawanid=b.id WHERE a.karyawanid='0000002200'";
    mysqli_query($cnit, $query);
    
    $query = "update $tmp02 set nama=nama_karyawan WHERE karyawanid='0000002200'"; 
    mysqli_query($cnit, $query);
    
    $query = "update $tmp02 a JOIN dbmaster.t_karyawan_kontrak b ON a.nama_karyawan=b.nama AND"
            . " a.atasan1=b.atasan1 AND a.atasan2=b.atasan2 AND a.atasan3=b.atasan3 AND a.atasan4=b.atasan4 "
            . " SET a.karyawanid=b.id WHERE a.karyawanid='0000002200'";
    mysqli_query($cnit, $query);
    
    
    mysqli_query($cnmy, "CREATE TEMPORARY TABLE $tmp05 SELECT * FROM $tmp01");
    $query = "INSERT INTO $tmp01 (karyawanid, nama, icabangid, areaid, divisi)"
            . "select distinct karyawanid, nama, icabangid, areaid, divisi FROM $tmp02 WHERE karyawanid not in "
            . "(select distinct IFNULL(karyawanid,'') FROM $tmp05)";
    mysqli_query($cnit, $query);
    
    
    //cari pengembalian hanya status ots=1
    $query = "select bulan, karyawanid, tgl_kembali, keterangan, sum(kembali_rp) kembali_rp from dbmaster.t_brrutin_outstanding WHERE 
        DATE_FORMAT(bulan,'%Y-%m')='$periode1' AND IFNULL(ots_status,'')='1' AND divisi = 'OTC' group by 1,2,3,4";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    //cari kelebihan status ots <> 1
    $query = "select ots_status, karyawanid, sum(kembali_rp) kembali_rp from dbmaster.t_brrutin_outstanding WHERE 
        DATE_FORMAT(bulan,'%Y-%m')='$periode1' AND IFNULL(ots_status,'')<>'1' AND divisi = 'OTC' group by 1,2";
    $query = "create TEMPORARY table $tmp06 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    $query = "select b.idca, a.divisi, a.karyawanid, a.nama, a.icabangid, a.areaid, 
        c.tgl_kembali, c.keterangan, c.kembali_rp, d.ots_status, 
        sum(a.jumlah) saldo, sum(b.jumlah) ca1, CAST(0 as DECIMAL(20,2)) as selisih, b.batal, b.alasan_batal 
        from $tmp01 a LEFT JOIN $tmp02 b on a.karyawanid=b.karyawanid 
        LEFT JOIN $tmp03 c on a.karyawanid=c.karyawanid 
        LEFT JOIN $tmp06 d on a.karyawanid=d.karyawanid             
        GROUP BY  1,2,3,4,5,6,7,8,9,10";
    
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp04 SET selisih=IFNULL(IFNULL(ca1,0)-IFNULL(saldo,0),0)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "DELETE FROM $tmp04 WHERE IFNULL(selisih,0)<=0";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
?>

<div class='x_content'>


    <table id='datatablepostdeth' class='table table-striped table-bordered' width="100%">
        <thead>
            <tr>
                <th width='4px'>No</th>
                <th width='200px'>Karyawan</th>
                <th width='100px'>Saldo</th>
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
            $query = "select * from $tmp04 order by nama, karyawanid";
            $tampil = mysqli_query($cnmy, $query);
            while( $row=mysqli_fetch_array($tampil) ) {
                $pidca=$row['idca'];
                $pidkry=$row['karyawanid'];
                $pnmkry=$row['nama'];
                $pdivisi=$row['divisi'];
                $psaldo=$row['saldo'];
                $pca=$row['ca1'];
                $pselisih=$row['selisih'];
                
                $pbatal=$row['batal'];
                $palasanbatal=$row['alasan_batal'];
                
                $ptglkembali=$row['tgl_kembali'];
                if ($ptglkembali=="0000-00-00") $ptglkembali="";
                $pketkembali=$row['keterangan'];
                $potsstatus=$row['ots_status'];
                $pkembalirp=$row['kembali_rp'];
                
                $cn_isi_kembali="";
                $cn_div_hidden="";
                if (!empty($row['kembali_rp']) AND !empty($ptglkembali)) {
                    //$cn_div_hidden="hidden";
                    //$cn_isi_kembali=number_format($pkembalirp,0,",",",");
                }
                
                //if ((double)$psaldo<>0) $cn_div_hidden="hidden";
                //if ($pbatal=="Y") { $cn_div_hidden="hidden"; }
                
                $finchksama="<span $cn_div_hidden><input type='checkbox' id='txtchksama$no' name='txtchksama$no' onClick=\"SamakanSelisih('txtchksama$no', 'txtjmlselisih$no', 'txtjmlkembali$no', 'tglskrang$no', 'txttglkembali$no')\" value='select'></span>";
                
                $finidca="<input type='hidden' id='txtidca$no' name='txtidca$no' class='input-sm' value='$pidca'>";
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
                
                $fsimpan="'txtbln$no', 'txtidkry$no', 'txtiddiv$no', 'txtjmlsaldo$no', 'txtjmlca$no', 'txtjmlselisih$no', 'txtjmlkembali$no', 'txttglkembali$no', 'cbsts$no', 'txtket$no', 'txtnmkry$no', 'txtidca$no'";
                $simpandata= "<span $cn_div_hidden><input type='button' class='btn btn-info btn-xs' id='s-submit' value='Save' onclick=\"SaveData('input', $fsimpan)\"></span>";
                $hapusdata= "<input type='button' class='btn btn-danger btn-xs' id='s-submit' value='Hapus' onclick=\"SaveData('hapus', $fsimpan)\">";
                        
                
                $btnbatal="<span $cn_div_hidden><input type='button' class='btn btn-warning btn-xs' value='CA Batal' onClick=\"SaveData('batal', $fsimpan)\"></span>";
                
                if ($pbatal=="Y") $btnbatal="CA BATAL";
                
                $psaldo=number_format($psaldo,0,",",",");
                $pca=number_format($pca,0,",",",");
                $pselisih=number_format($pselisih,0,",",",");
                
                
                echo "<tr>";
                echo "<td>$no $fintglskrang $finbln $finidkry $finnmkry $finiddivisi $finjmlsaldo $finjmlca $finjmlselisih $finidca</td>";
                echo "<td nowrap>$pnmkry</td>";
                echo "<td align='right'>$psaldo</td>";
                echo "<td align='right'>$pca</td>";
                echo "<td align='right'>$pselisih</td>";
                echo "<td align='right'><table><tr><td>$finchksama</td><td>$finjmlkembali $cn_isi_kembali</td></tr></table></td>";
                echo "<td>$fintglkembali</td>";
                //echo "<td>$finsts</td>";
                echo "<td><table><tr><td>$finsts</td><td>$finket</td></tr></table></td>";
                echo "<td nowrap>$simpandata $hapusdata $btnbatal</td>";
                echo "</tr>";
                
                $no++;
            }
        ?>
        </tbody>
    </table>
    
</div>
            

<?PHP

hapusdata:
    mysqli_query($cnit, "drop TEMPORARY table $tmp01");
    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
    mysqli_query($cnit, "drop TEMPORARY table $tmp03");
    mysqli_query($cnit, "drop TEMPORARY table $tmp04");
    mysqli_query($cnit, "drop TEMPORARY table $tmp05");
    mysqli_query($cnit, "drop TEMPORARY table $tmp06");
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
    
    
    function SaveData(eact, abln, akry, adiv, asaldo, aca, aselisih, akembali, atgl, asts, aket, anamakaryawan, aidca)  {
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
        var eidca =document.getElementById(aidca).value;
        
        if (ebln=="" && ekry==""){
            alert("tidak ada data yang disimpan...");
            return 0;
        }
        
        //alert(eselisih);
        var pText_="Simpan";
        if (eact=="hapus") var pText_="Hapus";
        if (eact=="batal") {
            var pText_="Jumlah kembali akan otomatis 0.\n\
                    Apakah CA akan dibatal...?";
        }
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                
                var txt;
                if (eact=="batal") {
                    var textket = prompt("Masukan alasan "+eact+" : ", "");
                    if (textket == null || textket == "") {
                        txt = textket;
                    } else {
                        txt = textket;
                    }
                }
                
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                $.ajax({
                    type:"post",
                    url:"module/mod_br_otsdlkca_otc/aksi_otsd_lkca.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"ubln="+ebln+"&ukry="+ekry+"&udiv="+ediv+"&usaldo="+esaldo+"&uca="+eca+"&uselisih="+eselisih+"&ukembali="+ekembali+"&utgl="+etgl+"&usts="+ests+"&uketerangan="+eket+"&unamakaryawan="+enamakaryawan+"&uidca="+eidca+"&uketbatal="+txt,
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