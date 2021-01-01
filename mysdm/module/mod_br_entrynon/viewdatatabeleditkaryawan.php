<?php
    date_default_timezone_set('Asia/Jakarta'); 
    session_start(); 
    
    //ini_set('display_errors', '0');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }

    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $_SESSION['FINNONTIPE']=$_POST['utipeproses'];
    $_SESSION['FINNONTGLTIPE']=$_POST['utgltipe'];
    $_SESSION['FINNONPERENTY1']=$_POST['uperiode1'];
    $_SESSION['FINNONPERENTY2']=$_POST['uperiode2'];
    $_SESSION['FINNONDIV']=$_POST['udivisi'];
    $_SESSION['FINNONUSPL']=$_POST['uidkarpilih'];
    
    $psescardidid=$_SESSION['IDCARD'];
    $pgroupid=$_SESSION['GROUP'];
    
    $ptgltipe=$_POST['utgltipe'];
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-d", strtotime($date1));
    $tgl2= date("Y-m-d", strtotime($date2));
    $pdivisi=$_POST['udivisi'];
    $uidcard=$_POST['uidc'];
    $upilidcrd=$_POST['uidkarpilih'];
    $pnuseriid=(INT)$upilidcrd;
    
    if (empty($upilidcrd)) {
        $upilidcrd=$psescardidid;
        $_SESSION['FINNONUSPL']=$psescardidid;
    }
    if (empty($pnuseriid)) $pnuseriid=$_SESSION['USERID'];
    
    
    include "../../config/koneksimysqli.php";
    
    $query = "select kodeid from hrd.br_kode where br = '' and br<>'N'";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    $filkode="";
    if ($ketemu>0) {
        while ($r=  mysqli_fetch_array($tampil)) {
            $xccoaid=$r['kodeid'];
            $filkode .= "'".$xccoaid."',";
        }
        if (!empty($filkode)) {
            $filkode="(".substr($filkode, 0, -1).")";
        }else{
            $filkode="('')";
        }
    }else{
        $filkode="('')";
    }
    $filterkodeid=" AND kode IN $filkode ";
    
    $filternondssdcc=" AND ( (br = '' and br<>'N') OR user1=$pnuseriid ) ";
	
	
    $filtipetglpil=" AND Date_format(MODIFDATE, '%Y-%m-%d') ";
    if ($ptgltipe=="2") $filtipetglpil=" AND Date_format(tgltrans, '%Y-%m-%d') ";
    if ($ptgltipe=="3") $filtipetglpil=" AND Date_format(tgltrm, '%Y-%m-%d') ";
    if ($ptgltipe=="4") $filtipetglpil=" AND Date_format(tgl, '%Y-%m-%d') ";
    if ($ptgltipe=="5") $filtipetglpil=" AND Date_format(tglrpsby, '%Y-%m-%d') ";
    
    $filtipetglpil=$filtipetglpil." BETWEEN '$tgl1' AND '$tgl2' ";
    
    
    $filterdivprod="";
    if (!empty($pdivisi)) {
        $filterdivprod = " AND divprodid = '$pdivisi' ";
    }
    
    
    
    //echo "$ptgltipe, $date1 - $date2, $tgl1 - $tgl2, $pdivisi, $upilidcrd, $filcoapilih";
    //echo "<br/>&nbsp;<br/>&nbsp;$filtipetglpil<br/>$filternondssdcc<br/>$filteruntukcoa";
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpbrtrkedtkr01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpbrtrkedtkr02_".$puserid."_$now ";
    
    
    $query = "SELECT TRIM(LEADING '0' FROM user1) as user1, brid as brid, tgl, tgltrans, tgltrm, tglrpsby, tglunrtr, coa4, icabangid, idcabang, "
            . " ccyid, jumlah, jumlah1, realisasi1, cn, noslip, "
            . " aktivitas1, aktivitas2, "
            . " kode, dokterid, dokter, karyawanid, karyawani2, karyawani3, karyawani4, mrid, lampiran, via, ca, sby, "
            . " divprodid, pajak, nama_pengusaha, noseri, tgl_fp, dpp, ppn, ppn_rp, pph_jns, pph, pph_rp, pembulatan, jasa_rp, materai_rp, jenis_dpp,"
            . " noseri_pph, tgl_fp_pph, dpp_pph, batal, alasan_b, lain2 FROM hrd.br0 WHERE 1=1 AND 
			 (user1='$pnuseriid' OR user1='$upilidcrd') $filtipetglpil $filterkodeid $filterdivprod ";//$filteruntukcoa
    $query.=" and brId not in (select distinct ifnull(brId,'') from hrd.br0_reject) ";
	$query.=" and karyawanid='0000000148' ";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query ="Select a.*, b.nama as nama_kry, c.nama as nama_cabang FROM "
            . " $tmp01 as a LEFT JOIN hrd.karyawan as b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN MKT.icabang as c on a.icabangid=c.icabangid";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER TABLE $tmp02 ADD COLUMN brid_g varchar(10), ADD COLUMN kry_g varchar(10), ADD COLUMN nama_kry_g varchar(100), ADD COLUMN cab_g varchar(10), ADD COLUMN nama_cab_g varchar(100)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN hrd.br0_ganti_karyawan as b on a.brid=b.brid SET "
            . " a.kry_g=b.karyawanid, a.cab_g=b.icabangid";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN hrd.karyawan as b on a.kry_g=b.karyawanid SET a.nama_kry_g=b.nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN MKT.icabang as b on a.cab_g=b.icabangid SET a.nama_cab_g=b.nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    $pkrypilihnm=""; 
    $pcbpilihnm="";
?>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />


<form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' 
      id='form_dataext01' name='form_data01' data-parsley-validate class='form-horizontal form-label-left'>

    <div class='x_content'>
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>
                
                <div class='col-sm-3'>
                    Yang Membuat (Karyawan)
                   <div class="form-group">
                        <select class='form-control input-sm' id="e_kryid" name="e_kryid" onchange="showCabangKry()">
                            <?PHP
                            $query = "select distinct b.karyawanId as karyawanid, b.nama as nama from hrd.karyawan b where 1=1 ";//b.jabatanid NOT IN ('08', '10', '15', '18', '20') 
                            //$query .=" AND (b.karyawanId IN ('0000001043', '0000000148', '0000000566') OR b.karyawanid='$fkaryawan')";
                            $query .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  "
                                    . " and LEFT(b.nama,7) NOT IN ('NN DM - ')  "
                                    . " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') "
                                    . " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR') ";
                            $query .=" ORDER BY b.nama ";
                            $tampil = mysqli_query($cnmy, $query);
                            echo "<option value=''>--Pilih--</option>";
                            while ($ir=  mysqli_fetch_array($tampil)) {
                                $iridkar=$ir['karyawanid'];
                                $irnmkar=$ir['nama'];
                                echo "<option value='$iridkar'>$irnmkar ($iridkar)</option>";
                            }
                            ?>
                        </select>
                   </div>
               </div>
                
                <div class='col-sm-3'>
                    Cabang
                   <div class="form-group">
                        <select class='form-control input-sm' id="cb_cabangid" name="cb_cabangid" onchange="showNamaCabangPL()">
                            <?PHP
                            echo "<option value=''>--Pilih--</option>";
                            ?>
                        </select>
                   </div>
               </div>
                
                <div hidden class='col-sm-3'>
                    Nama
                   <div class="form-group">
                        <input type='text' id='e_nmkaryawanpl' name='e_nmkaryawanpl' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pkrypilihnm; ?>' Readonly>
                        <input type='text' id='e_nmcabangpl' name='e_nmcabangpl' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pcbpilihnm; ?>' Readonly>
                   </div>
               </div>

                <div class='col-sm-3'>
                    <small>&nbsp;</small>
                   <div class="form-group">
                       <input type='button' class='btn btn-warning btn-sm' id="s-submit" value="Save" onclick='disp_confirkaryawan("Simpan ?")'>
                   </div>
               </div>
                
            </div>
        </div>
        <br/>
        
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='5%px'>No</th>
                    <th width='2%px'>
                        <input type="checkbox" id="chkbtnbr" value="select" 
                        onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
                    <th width='5%' >ID</th>
                    <th width='20%'>Karyawan</th>
                    <th width='30%'>Aktivitas</th>
                    <th width='5%' align="right">Jumlah</th>
					<th width='30%'>Realisasi</th>
                    <th width='30%'>Cabang</th>
                    <th width='30%'>Tgl BR</th>
                    <th width='30%'>Karyawan Lama</th>
                    <th width='30%'>Cabang Lama</th>
                </tr>
            </thead>
            <tbody class='inputdatauc'>
                <?PHP
                $no=1;
                $query = "select * from $tmp02 order by IFNULL(aktivitas1,''), tgl";
                $tampil=mysqli_query($cnmy, $query);
                while ($nrow= mysqli_fetch_array($tampil)){
                    $pidkode=$nrow['brid'];
                    $ptgl=$nrow['tgl'];
                    $pidkry=$nrow['karyawanid'];
                    $pnmkry=$nrow['nama_kry'];
                    $pidcab=$nrow['icabangid'];
                    $pnmcab=$nrow['nama_cabang'];
                    
                    $pidkry_g=$nrow['kry_g'];
                    $pnmkry_g=$nrow['nama_kry_g'];
                    
                    $pidcab_g=$nrow['cab_g'];
                    $pnmcab_g=$nrow['nama_cab_g'];
                    
                    $pnmrealisasi=$nrow['realisasi1'];
                    
                    $pkaryawanlama="";
                    if (!empty($pidkry_g)) $pkaryawanlama=$pnmkry_g." (".$pidkry_g.")";
                    $pcabanglama="";
                    if (!empty($pidcab_g)) $pcabanglama=$pnmcab_g." (".$pidcab_g.")";
                    
                    $paktivitas1=$nrow['aktivitas1'];
                    $paktivitas2=$nrow['aktivitas2'];
                    $pjumlah=$nrow['jumlah'];
                    
                    $pjumlah=number_format($pjumlah,0,",",",");
                    
                    $ceklisnya = "<input type='checkbox' value='$pidkode' name='chkbox_br[]' id='chkbox_br[$pidkode]' class='cekbr'>";
                    $ptxtkaryawan = "<input type='hidden' value='$pidkry' name='txt_kryid[$pidkode]' id='txt_kryid[$pidkode]' Readonly>";
                    $ptxtcab = "<input type='hidden' value='$pidcab' name='txt_cabid[$pidkode]' id='txt_cabid[$pidkode]' Readonly>";
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$ceklisnya $ptxtkaryawan $ptxtcab</td>";
                    echo "<td nowrap>$pidkode</td>";
                    echo "<td nowrap>$pnmkry ($pidkry)</td>";
                    echo "<td nowrap>$paktivitas1</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td nowrap>$pnmrealisasi</td>";
                    echo "<td nowrap>$pnmcab ($pidcab)</td>";
                    echo "<td nowrap>$ptgl</td>";
                    echo "<td nowrap>$pkaryawanlama</td>";
                    echo "<td nowrap>$pcabanglama</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                ?>
            </tbody>
        </table>



    </div>
                            
</form>

<script>
    function disp_confirkaryawan(pText_)  {
        var ecab =document.getElementById('cb_cabangid').value;
        var ebuat =document.getElementById('e_kryid').value;
        
        var iidnmkaryawan = document.getElementById('e_nmkaryawanpl').value;
        var iidnmcabang = document.getElementById('e_nmcabangpl').value;
        
        if (ebuat=="") {
            alert("karyawan yang membuat masih kosong...!!!");
            return false;
        }
        
        if (ecab=="") {
            alert("cabang masih kosong...!!!");
            return false;
        }
        
        var chk_arr1 =  document.getElementsByName('chkbox_br[]');
        var chklength1 = chk_arr1.length;
        var iadadata=false;

        
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                iadadata=true;
                break;
            }
        }
        
        if (iadadata==false) {
            alert("tidak ada BR yang dipilih...!!!");
            return false;
        }
        
        
        
        pText_ = "Karyawan : "+iidnmkaryawan+"\n\
Cabang : "+iidnmcabang+"\n\
-------------------------------------------------\n\
Apakah Akan melakukan simpan...???";
        
        var ket="updatekryidcab";
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                            
                ////document.write("You pressed OK!")
                document.getElementById("form_dataext01").action = "module/mod_br_entrynon/aksi_gantikaryawanbr.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("form_dataext01").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
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
    }
    
    
    
    function showCabangKry() {
        var icar = document.getElementById('e_kryid').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrynon/viewdata.php?module=viewdatacabangkaryawan",
            data:"umr="+icar,
            success:function(data){
                $("#cb_cabangid").html(data);
                showNamaKaryawanPL();
                showNamaCabangPL();
            }
        });
    }
    
    function showNamaKaryawanPL() {
        var icar = document.getElementById('e_kryid').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrynon/viewdata.php?module=viewdatanamakaryawan",
            data:"ucar="+icar,
            success:function(data){
                document.getElementById('e_nmkaryawanpl').value=data;
            }
        });
    }
    
    function showNamaCabangPL() {
        var iidcab = document.getElementById('cb_cabangid').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrynon/viewdata.php?module=viewdatanamacabang",
            data:"uidcab="+iidcab,
            success:function(data){
                document.getElementById('e_nmcabangpl').value=data;
            }
        });
    }
    
    
</script>

<style>
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
    .divnone {
        display: none;
    }
</style>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_close($cnmy);
?>