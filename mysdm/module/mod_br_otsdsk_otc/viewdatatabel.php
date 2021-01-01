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

    $date1=$_POST['utgl1'];
    $mytgl1= date("Y-m-d", strtotime($date1));
    
    $date2=$_POST['utgl2'];
    $mytgl2= date("Y-m-d", strtotime($date2));
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRRETRLCLS01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DTBRRETRLCLS02_".$_SESSION['IDCARD']."_$now ";
    $tmp03 =" dbtemp.DTBRRETRLCLS03_".$_SESSION['IDCARD']."_$now ";
    $tmp04 =" dbtemp.DTBRRETRLCLS04_".$_SESSION['IDCARD']."_$now ";
    $tmp05 =" dbtemp.DTBRRETRLCLS05_".$_SESSION['IDCARD']."_$now ";
    $tmp06 =" dbtemp.DTBRRETRLCLS06_".$_SESSION['IDCARD']."_$now ";
    
    
    $query = "select idservice, tglservice, divisi, karyawanid, icabangid, areaid, icabangid_o, areaid_o, nopol, km, jumlah, keterangan from dbmaster.t_service_kendaraan WHERE stsnonaktif<>'Y' and divisi='OTC' and 
        DATE_FORMAT(tglservice,'%Y-%m-%d') BETWEEN '$mytgl1' AND '$mytgl2'";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.*, b.nama nama_karyawan, c.nama nama_cabang, CAST('' as CHAR(10)) as bridinput, tspd.nodivisi, "
            . " CAST('' as CHAR(10)) as brsudahinput, CAST(0 as DECIMAL(20,2)) as jumlahrp, CAST(NULL as DATE) as tglots "
            . " from $tmp01 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN MKT.icabang_o c on a.icabangid_o=c.icabangid_o "
            . " LEFT JOIN (select distinct g.bridinput, h.nodivisi FROM dbmaster.t_suratdana_br1 g JOIN "
            . " dbmaster.t_suratdana_br h on g.idinput=h.idinput WHERE h.stsnonaktif<>'Y') as tspd on a.idservice=tspd.bridinput";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "SELECT * FROM dbmaster.t_brrutin_outstanding_sk WHERE idservice IN "
            . "(SELECT distinct IFNULL(idservice,'') FROM $tmp02)";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp02 a JOIN $tmp03 b on a.idservice=b.idservice SET a.brsudahinput=b.idservice, "
            . " a.jumlahrp=b.jumlah, a.tglots=b.tanggal"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
?>

<div class='x_content'>


        <table id='datatableabc' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='10px'><input hidden type="checkbox" id="chkall[]" name="chkall[]" onclick="SelAllCheckBox('chkall[]', 'chk_idbr[]')" value='select'></th>
                    <th width='10px'>No</th>
                    <th width='10px'>Tanggal</th>
                    <th width='50px' nowrap>Yang Membuat</th>
                    <th width='20px'>No. Polisi</th>
                    <th align="center" nowrap>Jumlah</th>
                    <th align="center" nowrap>Outsanding Rp.</th>
                    <th align="center" nowrap>Tgl. Otsd</th>
                    <th nowrap></th>
                    <th align="center">No BR/Divisi</th>
                    <th align="center">Keterangan</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                
                $no=1;
                $query = "select * from $tmp02";
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pbrid = $row['idservice'];
                    $ptglinput =date("d-M-Y", strtotime($row['tglservice']));

                    $pnamakaryawan = $row['nama_karyawan'];
                    $paktivitas1 = $row['keterangan'];
                    $pdivisi = $row['divisi'];
                    $pnopolisi = $row['nopol'];
                    $pbridinput = $row['bridinput'];
                    $pnobrdivisi = $row['nodivisi'];

                    $pjumlah = $row['jumlah'];
                    $pkembalirp = $row['jumlahrp'];
                    $ptglkembali = $row['tglots'];
                    
                    $pjumlah=number_format($pjumlah,0,",",",");
                    if ($ptglkembali=="0000-00-00") $ptglkembali="";
                    
                    $ncheck_sudah="";
                    if (!empty($pbridinput)) $ncheck_sudah="checked";
                    $chkbox = "<input type='checkbox' id='chk_idbr[$pbrid]' name='chk_idbr[]' value='$pbrid' onclick=\"HitungJumlahTotalCexBox('$pbrid', 'cb_urut[$pbrid]', 'chk_jml1[$pbrid]', 'chk_adj[$pbrid]', 'txt_adj[$pbrid]', 'chk_transke[$pbrid]', 'txt_adj_ket[$pbrid]')\" $ncheck_sudah>";
                    $ptxtjumlah = "<input type='hidden' id='txt_jml[$pbrid]' name='txt_jml[$pbrid]' value='$pjumlah' size='7px' class='input-sm inputmaskrp2' Readonly>";
                    
                    $cn_div_hidden="";
                    if ((double)$pjumlah!=0 AND empty($ptglkembali)){
                        
                    }else{
                        $cn_div_hidden="hidden";
                    }
                    
                    $finjmlkembali="<span ><input type='text' size='10px' id='txtjmlkembali$no' name='txtjmlkembali$no' class='input-sm inputmaskrp2' autocomplete='off' value='$pkembalirp'></span>";
                    $fintglkembali="<input type='date' name='txttglkembali$no' id='txttglkembali$no' size='10px' value='$ptglkembali'>";
                    
                    $finidservice="<input type='hidden' name='txtidservice$no' id='txtidservice$no' size='10px' value='$pbrid'>";
                
                    $fsimpan="'txtidservice$no', 'txtjmlkembali$no', 'txttglkembali$no'";
                    $simpandata= "<span $cn_div_hidden><input type='button' class='btn btn-info btn-xs' id='s-submit' value='Save' onclick=\"SaveData('input', $fsimpan)\"></span>";
                    $hapusdata= "<input type='button' class='btn btn-danger btn-xs' id='s-submit' value='Hapus' onclick=\"SaveData('hapus', $fsimpan)\">";
                
                    echo "<tr>";//$chkbox
                    echo "<td nowrap> $finidservice<t/d>";
                    echo "<td nowrap>$no $ptxtjumlah</td>";
                    echo "<td nowrap>$ptglinput</td>";
                    echo "<td nowrap>$pnamakaryawan</td>";
                    echo "<td nowrap>$pnopolisi</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td nowrap align='right'>$finjmlkembali</td>";
                    echo "<td nowrap align='right'>$fintglkembali</td>";
                    echo "<td> $simpandata $hapusdata</td>";
                    echo "<td nowrap>$pnobrdivisi</td>";
                    echo "<td>$paktivitas1</td>";
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
    var table = $('#datatableabc').DataTable({
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
    
    
    function SaveData(eact, aidservice, atxtjumlah, atglkembali)  {
        var eidservice =document.getElementById(aidservice).value;
        var ejumlah =document.getElementById(atxtjumlah).value;
        var etgl =document.getElementById(atglkembali).value;
        
        if (eidservice=="" && ejumlah==""){
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
                    url:"module/mod_br_otsdsk_otc/aksi_otsd_sk.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"eidservice="+eidservice+"&ejumlah="+ejumlah+"&etgl="+etgl+"&uketbatal="+txt,
                    success:function(data){
                        if (data.length > 1) {
                            alert(data);
                        }
                        
                        if (eact=="hapus" && data.length <= 1) {
                            
                            document.getElementById(akembali).value="";
                            document.getElementById(ejumlah).value="";
                            document.getElementById(etgl).value="";
                            
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
    #datatableabc th {
        font-size: 12px;
    }
    #datatableabc td { 
        font-size: 11px;
    }
</style>