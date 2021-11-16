<?php
session_start();
//ini_set('display_errors', '0');
date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);
    

    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    $pidgroup=$_SESSION['GROUP'];
    
    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_ubahget_id.php";
    
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpapvbrrealmkt01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpapvbrrealmkt02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpapvbrrealmkt03_".$puserid."_$now ";
    
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    
    $ppilihsts = strtoupper($_POST['eket']);
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $pkaryawanid = $_POST['ukaryawan'];
    $pstsapv = $_POST['uketapv'];
    
    $_SESSION['MUAPVBRMKTSTS']=$ppilihsts;
    $_SESSION['MUAPVBRMKTBLN1']=$mytgl1;
    $_SESSION['MUAPVBRMKTBLN2']=$mytgl2;
    $_SESSION['MUAPVBRMKTAPVBY']=$pkaryawanid;
    
    
    $pbulan1= date("Y-m-01", strtotime($mytgl1));
    $pbulan2= date("Y-m-t", strtotime($mytgl2));
    
    
    $query = "select kodeid from hrd.br_kode where IFNULL(ks,'')='Y'";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    $pfilterkodeid="";
    if ($ketemu>0) {
        while ($r=  mysqli_fetch_array($tampil)) {
            $xccoaid=$r['kodeid'];
            $pfilterkodeid .= "'".$xccoaid."',";
        }
        if (!empty($pfilterkodeid)) {
            $pfilterkodeid="(".substr($pfilterkodeid, 0, -1).")";
        }else{
            $pfilterkodeid="('')";
        }
    }else{
        $pfilterkodeid="('')";
    }
    
    
    $tampil=mysqli_query($cnmy, "select jabatanId, nama from hrd.karyawan where karyawanid='$pkaryawanid'");
    $pr= mysqli_fetch_array($tampil);
    $pjabatanid=$pr['jabatanId'];
    $pnama_approve=$pr['nama'];
    if (empty($pjabatanid)) {
        $tampil=mysqli_query($cnmy, "select jabatanId from dbmaster.t_karyawan_posisi where karyawanid='$pkaryawanid'");
        $pr= mysqli_fetch_array($tampil);
        $pjabatanid=$pr['jabatanId'];
    }
    
    $tampil=mysqli_query($cnmy, "select nama as nama_jabatan from hrd.jabatan where jabatanId='$pjabatanid'");
    $pr= mysqli_fetch_array($tampil);
    $pnama_jabatan=$pr['nama_jabatan'];
    
    $papproveby="";
    if ($pjabatanid=="18" OR $pjabatanid=="10") {
        $papproveby="apvspv";
    }elseif ($pjabatanid=="08") {
        $papproveby="apvdm";
    }elseif ($pjabatanid=="20") {
        $papproveby="apvsm";
    }elseif ($pjabatanid=="04" OR $pjabatanid=="05" OR $pjabatanid=="36") {
        $papproveby="apvgsm";
    }elseif ($pjabatanid=="01") {
        $papproveby="apvcoo";
    }else{
        if ($pidgroup=="46") {
            $papproveby="apvcoo";
        }elseif ($pidgroup=="8") {
            $papproveby="apvgsm";
        }
    }
    
    
    if (empty($pjabatanid) OR empty($papproveby) OR empty($pkaryawanid)) {
        echo "Anda tidak berhak proses...";
        mysqli_close($cnmy); exit;
    }
    
    
    
    $query = "SELECT brid, karyawanid, mrid, icabangid, divprodid, "
            . " tgl, tgltrans, kode, coa4, dokterid, "
            . " jumlah, jumlah1, realisasi1, realisasi2, aktivitas1, aktivitas2, stsbr "
            . " FROM hrd.br0 where "
            . " tgltrans between '$pbulan1' and '$pbulan2' AND IFNULL(dokterid,'')<>'' AND ";
    if ($papproveby=="apvdm") {
        $query .=" ( karyawanid='$pkaryawanid' OR mrid='$pkaryawanid' ";
        $query .=" OR icabangid IN (select distinct IFNULL(icabangid,'') FROM sls.idm0 WHERE karyawanid='$pkaryawanid' AND IFNULL(aktif,'')<>'N') ";
        $query .=" ) ";
        
        $query .=" AND karyawanid IN (select distinct karyawanid FROM hrd.karyawan WHERE jabatanid IN ('15', '10', '18', '08'))";
    }elseif ($papproveby=="apvsm") {
        $query .=" ( karyawanid='$pkaryawanid' OR mrid='$pkaryawanid' ";
        $query .=" OR icabangid IN (select distinct IFNULL(icabangid,'') FROM sls.ism0 WHERE karyawanid='$pkaryawanid' AND IFNULL(aktif,'')<>'N') ";
        $query .=" ) ";
        
        $query .=" AND karyawanid IN (select distinct karyawanid FROM hrd.karyawan WHERE jabatanid IN ('15', '10', '18', '08', '20'))";
    }else{
        $query .=" karyawanid='$pkaryawanid' ";
    }
    
    if ($ppilihsts=="REJECT") {
        $query .=" AND (IFNULL(batal,'')='Y' OR IFNULL(retur,'')='Y') ";
    }else{
        
        $query .=" AND IFNULL(batal,'')<>'Y' AND IFNULL(retur,'')<>'Y' ";
        if ($ppilihsts=="ALLDATA") {
            
        }else{
            if ($ppilihsts=="APPROVE") {
                
            }elseif ($ppilihsts=="UNAPPROVE") {
                
            }else{
                $query .=" karyawanid='xxxxxxxx' ";
            }
        }
        
    }
    
    $query .=" AND ( IFNULL(stsbr,'')  IN ('KI', 'ki') OR kode IN $pfilterkodeid ) ";
    
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER TABLE $tmp01 ADD COLUMN nama_karyawan VARCHAR(200), ADD COLUMN nama_cabang VARCHAR(200), "
            . " ADD COLUMN nama_dokter VARCHAR(200), ADD COLUMN nama_mr VARCHAR(200)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanid SET a.nama_karyawan=b.nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN hrd.karyawan as b on a.mrid=b.karyawanid SET a.nama_mr=b.nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN hrd.dokter as b on a.dokterid=b.dokterid SET a.nama_dokter=b.nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN mkt.icabang as b on a.icabangid=b.icabangid SET a.nama_cabang=b.nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    echo "<div style='font-weight:bold; color:blue;'>";
    if ($ppilihsts=="APPROVE") {
        echo "DATA YANG BELUM DIAPPROVE";
    }elseif ($ppilihsts=="UNAPPROVE") {
        echo "DATA YANG SUDAH DIAPPROVE";
    }elseif ($ppilihsts=="REJECT") {
        echo "DATA REJECT";
    }
    echo "</div>";
    
?>

<!--<form method='POST' action='<?PHP //echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>-->
    
    <div class='x_content' style="overflow-x:auto; max-height:500px">
        
    
        <?PHP
        $pchkall = "<input type='checkbox' id='chkbtnbr' name='chkbtnbr' value='deselect' onClick=\"SelAllCheckBox('chkbtnbr', 'chkbox_br[]')\" checked/>";
        ?>
    
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead class="header" id="myHeader">
                <tr>
                    <th width='7px'>No</th>
                    <th width='5px'>&nbsp;</th>
                    <th width='50px'>User</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                
                $no=1;
                $query = "SELECT DISTINCT dokterid, nama_dokter FROM $tmp01 ORDER BY nama_dokter, dokterid";
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    
                    $ndoktid=$row['dokterid'];
                    $ndoktnm=$row['nama_dokter'];
                    
                    $filter_br="";
                    
                    if (!empty($ndoktid)) {
                        $query_ = "SELECT distinct brid FROM $tmp01 WHERE IFNULL(dokterid,'')='$ndoktid' ORDER BY brid";
                        $tampil_1=mysqli_query($cnmy, $query_);
                        while ($nrow= mysqli_fetch_array($tampil_1)) {
                            $nbrid=$nrow['brid'];

                            $filter_br .="".$nbrid.",";
                        }
                        if (!empty($filter_br)) $filter_br="".substr($filter_br, 0, -1)."";
                    }
                    
                    $pidnoget="";
                    $ndoktid_="";
                    if (!empty($ndoktnm)) {
                        $ndoktid_=(INT)$ndoktid;
                        $pidnoget=encodeString($ndoktid);
                    }else{
                        $ndoktid="NONE";
                        $ndoktnm="None";
                    }
                    
                    $pnamefield=$ndoktid;
                    $pnamebtnfld="btn".$ndoktid;
                    $pnamebtnfld_wa="btnlw".$ndoktid;
                    $pnamebtnfld_ld="btnld".$ndoktid;
                    $pnamebtnfld_apv="btnapv".$ndoktid;
                    
                    $pbtnshow = "<input type='button' id='$pnamebtnfld' name='$pnamebtnfld' class='btn btn-success btn-xs' value='detail' onClick=\"showhideRow('$pnamefield')\">";
                    
                    $pbtnlinkwa="";
                    $pbtnlengkapidata="";
                    $pbtnapprovedata="";
                    if (!empty($pidnoget)) {
                        $pbtnlinkwa = "<input type='button' id='$pnamebtnfld_wa' name='$pnamebtnfld_wa' class='btn btn-info btn-xs' value='Link WA' onClick=\"\">";
                        
                        $pbtnlengkapidata="<button type='button' id='$pnamebtnfld_ld' name='$pnamebtnfld_ld' class='btn btn-warning btn-xs' data-toggle='modal' "
                                . " data-target='#myModal' onClick=\"LengkapiDataUser('$pidnoget')\">Lengkapi Data</button>";
                        
                        $pbtnapprovedata="<button type='button' id='$pnamebtnfld_apv' name='$pnamebtnfld_apv' class='btn btn-dark btn-xs' data-toggle='modal' "
                                . " data-target='#myModal' onClick=\"ApproveDataUser('$pkaryawanid', '$pnama_approve', '$pjabatanid', '$pnama_jabatan', '$pidnoget', '$filter_br')\">Approve</button>";
                    }
                    
                    echo "<tr style='font-weight:bold;'>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pbtnshow $pbtnlinkwa $pbtnlengkapidata $pbtnapprovedata</td>";
                    echo "<td nowrap>$ndoktnm ($ndoktid_)</td>";
                    echo "</tr>";
                    
                    $no++;
                    
                    
                    echo "<tr id='$pnamefield' style='display:none;'>";
                    echo "<td colspan='3'>";
                        
                        $precno=1;
                        echo "<table id='mydatatable2' class='table table-striped table-bordered tbl2' width='100%' border='1px solid black'>";
                            echo "<tr>";
                                echo "<th>No</th>";
                                echo "<th>Cabang</th>";
                                echo "<th>Karyawan</th>";
                                echo "<th>MR</th>";
                                echo "<th>Divisi</th>";
                                echo "<th>Tgl</th>";
                                echo "<th>Tgl. Transfer</th>";
                                echo "<th>Jumlah</th>";
                                echo "<th>Jumlah Realisasi</th>";
                                echo "<th>Jumlah Transfer</th>";
                                echo "<th>Nama Realisasi</th>";
                                echo "<th>Keterangan</th>";
                            echo "</tr>";
                            
                            $ndoktid_pilih=$ndoktid;
                            if ($ndoktid=="NONE") $ndoktid_pilih="";
                            
                            $query = "SELECT * FROM $tmp01 WHERE IFNULL(dokterid,'')='$ndoktid_pilih' ORDER BY nama_dokter, dokterid, tgl, tgltrans";
                            $tampil2=mysqli_query($cnmy, $query);
                            while ($row2= mysqli_fetch_array($tampil2)) {
                                $nkryid=$row2['karyawanid'];
                                $nkrynm=$row2['nama_karyawan'];
                                $nmrid=$row2['mrid'];
                                $nmrnm=$row2['nama_mr'];
                                $ndivisi=$row2['divprodid'];
                                $ncabid=$row2['icabangid'];
                                $ncabnm=$row2['nama_cabang'];
                                $njumlah=$row2['jumlah'];
                                $njumlahreal=$row2['jumlah1'];
                                $nnamarealisasi=$row2['realisasi1'];
                                $nket1=$row2['aktivitas1'];
                                $nket2=$row2['aktivitas2'];
                                
                                $ntgl=$row2['tgl'];
                                $ntgltrans=$row2['tgltrans'];
                                
                                if ($ntgltrans=="0000-00-00") $ntgltrans="";
                                
                                $ntgl= date("d/m/Y", strtotime($ntgl));
                                if (!empty($ntgltrans)) $ntgltrans= date("d/m/Y", strtotime($ntgltrans));
                                
                                $njumlahtrans=$njumlah;
                                if ((INT)$njumlahreal<>0) $njumlahtrans=$njumlahreal;
                                
                                $njumlah=number_format($njumlah,0,",",",");
                                $njumlahreal=number_format($njumlahreal,0,",",",");
                                $njumlahtrans=number_format($njumlahtrans,0,",",",");
                                
                                $nnamadivisi=$ndivisi;
                                if ($ndivisi=="CAN") $nnamadivisi="CANARY";
                                elseif ($ndivisi=="PEACO") $nnamadivisi="PEACOCK";
                                elseif ($ndivisi=="PIGEO") $nnamadivisi="PIGEON";
                                
                                
                                echo "<tr>";
                                echo "<td nowrap>$precno</td>";
                                echo "<td nowrap>$ncabnm</td>";
                                echo "<td nowrap>$nkrynm</td>";
                                echo "<td nowrap>$nmrnm</td>";
                                echo "<td nowrap>$nnamadivisi</td>";
                                echo "<td nowrap>$ntgl</td>";
                                echo "<td nowrap>$ntgltrans</td>";
                                echo "<td nowrap align='right'>$njumlah</td>";
                                echo "<td nowrap align='right'>$njumlahreal</td>";
                                echo "<td nowrap align='right'>$njumlahtrans</td>";
                                echo "<td nowrap>$nnamarealisasi</td>";
                                echo "<td >$nket1</td>";
                                echo "</tr>";
                                
                                $precno++;
                            }
                        echo "</table>";
                        
                    echo "</td>";
                    echo "</tr>";
                        
                }
                
                ?>
            </tbody>
        </table>
                
    
    </div>
    
<!--</form>-->

        
<style>

    .divnone {
        display: none;
    }
    #mydatatable1, #mydatatable2, #mydatatable3, #mydatatable4, #mydatatable5, #mydatatable6, #mydatatable_ {
        color:#000;
        font-family: "Arial";
    }
    #mydatatable1 th, #mydatatable2 th, #mydatatable3 th, #mydatatable4 th, #mydatatable5 th, #mydatatable6 th {
        font-size: 12px;
    }
    #mydatatable1 td, #mydatatable2 td, #mydatatable3 td, #mydatatable4 td, #mydatatable5 td, #mydatatable6 td, #mydatatable_ td { 
        font-size: 14px;
    }


    th {
        background: white;
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index:1;
    }
    
    #mydatatable1 tr td {
        font-size: 12px;
    }
    
    #mydatatable2 tr td {
        font-size: 11px;
    }
</style>

<script>
    
        
    function showhideRow(rowId) {
        if (document.getElementById(rowId).style.display=="none") {
            document.getElementById(rowId).style.display = "";
            document.getElementById('btn'+rowId).value="  ---  ";
        }else{
            document.getElementById(rowId).style.display = "none";
            document.getElementById('btn'+rowId).value="detail";
        }
    }
    
    function LengkapiDataUser(edoktid) {
        $("#myModal").html("");
        $.ajax({
            type:"post",
            url:"module/manaj_user/mod_apvbrbymkt/lengkapidatausr.php?module=viewdatauser",
            data:"udoktid="+edoktid,
            success:function(data){
                $("#myModal").html(data);
            }
        });
        
    }
    
    function ApproveDataUser(ekryapv, ekryapvnm, ekryjbt, enmjbt, edoktid, eidbr) {
        $("#myModal").html("");
        if (ekryapv=="") {
            alert("Karyawan Approve Kosong");
            return false;
        }
        if (ekryjbt=="") {
            alert("Jabatan Karyawan Approve Kosong");
            return false;
        }
        
        $.ajax({
            type:"post",
            url:"module/manaj_user/mod_apvbrbymkt/approvebrrealbymkt.php?module=viewdatauserforapv",
            data:"udoktid="+edoktid+"&uidbr="+eidbr+"&ukryapv="+ekryapv+"&ukryapvnm="+ekryapvnm+"&ukryjbt="+ekryjbt+"&unmjbt="+enmjbt,
            success:function(data){
                $("#myModal").html(data);
            }
        });
        
    }
    
</script>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp03");
    
    mysqli_close($cnmy);
?>