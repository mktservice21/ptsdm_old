<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","500M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $ptglini = date("Y-m-d");
    
    $puserid="";
    $pidcard="";
    $pidgroup="";
    $pidsesion="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
    if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];
    if (isset($_SESSION['GROUP'])) $pidgroup=$_SESSION['GROUP'];
    if (isset($_SESSION['IDSESI'])) $pidsesion=$_SESSION['IDSESI'];


    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
        
        
    include "../../config/koneksimysqli_ms.php";
    include "../../config/fungsi_sql.php";
    $cnit=$cnms;
    $pidcabang=$_POST['uicab'];
    $pidarea=$_POST['uiarea'];
    
    $piddaricabang=$_POST['uicabdari'];
    $piddariarea=$_POST['uiareadari'];
    
    $pnmcabang=getfieldcnnew("select nama as lcfields from MKT.icabang where icabangid='$pidcabang'");
    $pnmarea=getfieldcnnew("select nama as lcfields from MKT.iarea where icabangid='$pidcabang' AND areaid='$pidarea'");
    
    $pnmdaricabang=getfieldcnnew("select nama as lcfields from MKT.icabang where icabangid='$piddaricabang'");
    $pnmadarirea=getfieldcnnew("select nama as lcfields from MKT.iarea where icabangid='$piddaricabang' AND areaid='$piddariarea'");
    
    $_SESSION['PNDCSTNWIDCAB']=$pidcabang;
    $_SESSION['PNDCSTNWIDARA']=$pidarea;
    $_SESSION['PNDCSTOLIDCAB']=$piddaricabang;
    $_SESSION['PNDCSTOLIDARA']=$piddariarea;
    
    
    echo "<input type='hidden' name='txt_idcab_view' id='txt_idcab_view' value='$pidcabang'>";
    echo "<input type='hidden' name='txt_nmcab_view' id='txt_nmcab_view' value='$pnmcabang'>";
    echo "<input type='hidden' name='txt_idarea_view' id='txt_idarea_view' value='$pidarea'>";
    echo "<input type='hidden' name='txt_nmarea_view' id='txt_nmarea_view' value='$pnmarea'>";
    
    echo "<input type='hidden' name='txt_idcab_view_old' id='txt_idcab_view_old' value='$piddaricabang'>";
    echo "<input type='hidden' name='txt_nmcab_view_old' id='txt_nmcab_view_old' value='$pnmdaricabang'>";
    echo "<input type='hidden' name='txt_idarea_view_old' id='txt_idarea_view_old' value='$piddariarea'>";
    echo "<input type='hidden' name='txt_nmarea_view_old' id='txt_nmarea_view_old' value='$pnmadarirea'>";
    
    // echo "$pidcabang - $pidarea dan $piddaricabang - $piddariarea";
    
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmppindahcustcabar00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmppindahcustcabar01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmppindahcustcabar02_".$puserid."_$now ";
    
    
    $query = "select distinct icabangid from dbmaster.tmp_pindah_cust WHERE icabangid='$piddaricabang' AND areaid='$piddariarea' AND ifnull(selesai,'')='Y'";
    $tampil= mysqli_query($cnit, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        ?>
        <div class="page-title">
            <h1 style="font-size:15px; font-weight: bold;">
                <?PHP echo "Sudah pernah pindah data..."; ?>
            </h1>
        </div>
        <?PHP
        goto hapusdata;
    }
    
    $query = "select icabangid, icustid, areaid, nama, alamat1, alamat2, kodepos, contact, "
            . " telp, fax, ikotaid, kota, isektorid, aktif, dispen, user1,oldflag, scode, grp, grp_spp, "
            . " o_icabangid, o_areaid, o_icustid, pertgl, batch_id, icabangid_hist, iareaid_hist, icustid_hist, "
            . " istatus, idisc, sys_now from MKT.icust where icabangid='$piddaricabang' and areaid='$piddariarea'";
    //$query .= " AND icustid='0000006983'";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select * from $tmp00";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

    $query = "DELETE FROM $tmp01";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER TABLE $tmp01 ADD COLUMN icabangid_new VARCHAR(10), ADD COLUMN nmcabang_new VARCHAR(100), "
            . " ADD COLUMN areaid_new VARCHAR(10), ADD COLUMN areanm_new VARCHAR(100), ADD COLUMN icustid_new INT(10) ZEROFILL NOT NULL AUTO_INCREMENT PRIMARY KEY";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //e cabang e custid
    $query = "select a.distid as distid, a.cabangid as cabangid, a.ecustid as ecustid, a.icabangid as icabangid, "
            . " a.areaid as areaid, a.icustid as icustid, a.nama as nama "
            . " from MKT.ecust as a "
            . " JOIN $tmp00 as b on a.icabangid=b.icabangid AND a.areaid=b.areaid AND a.icustid=b.icustid "
            . " where a.icabangid='$piddaricabang' and a.areaid='$piddariarea'";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER TABLE $tmp02 ADD COLUMN icabangid_new VARCHAR(10), ADD COLUMN nmcabang_new VARCHAR(100), "
            . " ADD COLUMN areaid_new VARCHAR(10), ADD COLUMN areanm_new VARCHAR(100), ADD COLUMN icustid_new VARCHAR(10) NOT NULL";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET icabangid_new='$pidcabang', areaid_new='$pidarea', nmcabang_new='$pnmcabang', areanm_new='$pnmarea'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    //-- e cabang
    
    //cari nourut terakhir dari icust sesuai cabang
    $pnourut=0;
    $query = "select max(icustid) as icustid from MKT.icust where icabangid='$pidcabang'";
    $tampil= mysqli_query($cnit, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $irow=mysqli_fetch_array($tampil);
        $pnourut=$irow['icustid'];
        if (empty($pnourut)) $pnourut=0;
    }
    $pnourut++;
    
    $pnourut=str_pad($pnourut, 10, "0", STR_PAD_LEFT);
    //END cari nourut terakhir
    
    
    //membuat nomor urut
    $query = "ALTER TABLE $tmp01 AUTO_INCREMENT = $pnourut";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "INSERT INTO $tmp01 (icabangid, icustid, areaid, nama, alamat1, alamat2, kodepos, contact, "
            . " telp, fax, ikotaid, kota, isektorid, aktif, dispen, user1,oldflag, scode, grp, grp_spp, "
            . " o_icabangid, o_areaid, o_icustid, pertgl, batch_id, icabangid_hist, iareaid_hist, icustid_hist, "
            . " istatus, idisc, sys_now, icabangid_new, areaid_new, nmcabang_new, areanm_new)"
            . " SELECT icabangid, icustid, areaid, nama, alamat1, alamat2, kodepos, contact, "
            . " telp, fax, ikotaid, kota, isektorid, aktif, dispen, user1,oldflag, scode, grp, grp_spp, "
            . " o_icabangid, o_areaid, o_icustid, pertgl, batch_id, icabangid_hist, iareaid_hist, icustid_hist, "
            . " istatus, idisc, sys_now, '$pidcabang' as icabangid_new, '$pidarea' as areaid_new, '$pnmcabang' as nmcabang_new, '$pnmarea' as areanm_new FROM $tmp00";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    //END membuat nomor urut
    
    //update temporary ecust icustid dengan icustid new
    $query = "UPDATE $tmp02 as a JOIN $tmp01 as b on a.icabangid=b.icabangid AND a.areaid=b.areaid AND a.icustid=b.icustid "
            . " SET a.icustid_new=LPAD(ifnull(b.icustid_new,0), 10, '0')";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "DELETE FROM dbmaster.tmp_pindah_cust WHERE tglinput<'$ptglini' AND IFNULL(selesai,'')<>'Y'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "DELETE FROM dbmaster.tmp_pindah_cust WHERE idsesi='$pidsesion' AND userid='$pidcard' AND IFNULL(selesai,'')<>'Y'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "ALTER TABLE dbmaster.tmp_pindah_cust AUTO_INCREMENT = 1";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //masuk dbmaster.temporary
    $query = "INSERT INTO dbmaster.tmp_pindah_cust (icabangid, icustid, areaid, nama, alamat1, alamat2, kodepos, contact, "
            . " telp, fax, ikotaid, kota, isektorid, aktif, dispen, user1,oldflag, scode, grp, grp_spp, "
            . " o_icabangid, o_areaid, o_icustid, pertgl, batch_id, icabangid_hist, iareaid_hist, icustid_hist, "
            . " istatus, idisc, sys_now, icabangid_new, areaid_new, icustid_new, userid, idsesi)"
            . " SELECT icabangid, icustid, areaid, nama, alamat1, alamat2, kodepos, contact, "
            . " telp, fax, ikotaid, kota, isektorid, aktif, dispen, user1,oldflag, scode, grp, grp_spp, "
            . " o_icabangid, o_areaid, o_icustid, pertgl, batch_id, icabangid as icabangid_hist, areaid as iareaid_hist, icustid as icustid_hist, "
            . " istatus, idisc, sys_now, icabangid_new, areaid_new, LPAD(ifnull(icustid_new,0), 10, '0') as icustid_new, '$pidcard' as userid, '$pidsesion' as idsesi FROM $tmp01";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "DELETE FROM dbmaster.tmp_pindah_ecust WHERE tglinput<'$ptglini' AND IFNULL(selesai,'')<>'Y'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "DELETE FROM dbmaster.tmp_pindah_ecust WHERE idsesi='$pidsesion' AND userid='$pidcard' AND IFNULL(selesai,'')<>'Y'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "ALTER TABLE dbmaster.tmp_pindah_ecust AUTO_INCREMENT = 1";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "INSERT INTO dbmaster.tmp_pindah_ecust (distid, cabangid, ecustid, icabangid, areaid, icustid, nama, "
            . " icabangid_new, areaid_new, icustid_new, userid, idsesi)"
            . " SELECT distid, cabangid, ecustid, icabangid, areaid, icustid, nama, "
            . " icabangid_new, areaid_new, icustid_new, '$pidcard' as userid, '$pidsesion' as idsessi FROM $tmp02";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
?>

<div class="page-title">
    <h1 style="font-size:15px; font-weight: bold;">
        <?PHP echo "Data Customer (iCust)"; ?>
    </h1>
</div>
<div class="clearfix"></div>


    
    <div class='x_content'>
        <table id='datatblcust' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='10px'>ID Cust (New)</th>
                    <th width='30px'>Nama Cust</th>
                    <th width='30px'>Alamat</th>
                    <th width='10px'>Cabang (New)</th>
                    <th width='30px'>Area (New)</th>
                    <th width='10px'>Cabang (Old)</th>
                    <th width='10px'>Area (Old)</th>
                    <th width='10px'>ID Cust (Old)</th>
                </tr> 
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp01 order by icustid_new";
                $tampil=mysqli_query($cnit, $query);
                while ($row=mysqli_fetch_array($tampil)){
                    $pidcabnew=(INT)$row['icabangid_new'];
                    $pnmcabnew=$row['nmcabang_new'];
                    $pidareanew=(INT)$row['areaid_new'];
                    $pnmareanew=$row['areanm_new'];
                    $pidcustnew=$row['icustid_new'];
                    $pnmcust=$row['nama'];
                    $palamat=$row['alamat1'];
                    
                    $pidcabold=$row['icabangid'];
                    $pidareaold=$row['areaid'];
                    $pidcustold=$row['icustid'];
                    echo "<tr>";
                    echo "<td nworap>$no</td>";
                    echo "<td nworap>$pidcustnew</td>";
                    echo "<td nworap>$pnmcust</td>";
                    echo "<td nworap>$palamat</td>";
                    echo "<td nworap>$pnmcabnew ($pidcabnew)</td>";
                    echo "<td nworap>$pnmareanew ($pidareanew)</td>";
                    echo "<td nworap>$pnmdaricabang ($pidcabold)</td>";
                    echo "<td nworap>$pnmadarirea ($pidareaold)</td>";
                    echo "<td nworap>$pidcustold</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                ?>                
            </tbody>
        </table>
    </div>
    <div class="clearfix"></div>

    <div class="page-title">
        <h1 style="font-size:15px; font-weight: bold;">
            <?PHP echo "Data Customer (eCust)"; ?>
        </h1>
    </div>
    <div class="clearfix"></div>


    <div class='x_content'>
        <table id='datatblcust2' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='10px'>Cabang Id</th>
                    <th width='10px'>eCustId</th>
                    <th width='30px'>Nama</th>
                    <th width='30px'>CabangId (New)</th>
                    <th width='30px'>AreaId (New)</th>
                    <th width='30px'>iCustId (New)</th>
                    <th width='30px'>CabangId (Old)</th>
                    <th width='30px'>AreaId (Old)</th>
                    <th width='30px'>iCustId (Old)</th>
                </tr> 
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp02 order by icustid_new";
                $tampil2=mysqli_query($cnit, $query);
                while ($row2=mysqli_fetch_array($tampil2)){
                    
                    $peccabid=$row2['cabangid'];
                    $pecustid=$row2['ecustid'];
                    $pnmcust=$row2['nama'];
                    $picustid=$row2['icustid_new'];
                    
                    $picustidold=$row2['icustid'];
                    //$pidcabang, $pidarea, $piddaricabang, $piddariarea
                    echo "<tr>";
                    echo "<td nworap>$no</td>";
                    echo "<td nworap>$peccabid</td>";
                    echo "<td nworap>$pecustid</td>";
                    echo "<td nworap>$pnmcust</td>";
                    echo "<td nworap>$pidcabang</td>";
                    echo "<td nworap>$pidarea</td>";
                    echo "<td nworap>$picustid</td>";
                    echo "<td nworap>$piddaricabang</td>";
                    echo "<td nworap>$piddariarea</td>";
                    echo "<td nworap>$picustidold</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                ?>                
            </tbody>
        </table>
    </div>
    <div class="clearfix"></div>
    
    <div class='col-md-12 col-sm-12 col-xs-12'>

        <div class="well" style="overflow: auto; margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;">
            <button type='button' class='btn btn-danger btn-sm' onclick='ProsesPindahDataCabCust()'>Proses Pindah Data</button>
        </div>


    </div>
    
<style>
    .divnone {
        display: none;
    }
    #datatblcust th, #datatblcust2 th {
        font-size: 13px;
    }
    #datatblcust td, #datatblcust2 td { 
        font-size: 11px;
    }
</style>

<script>
    $(document).ready(function() {
        var aksi = "module/mst_pindahcabareacust/aksi_pindahcabareacust.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        
        
        var dataTable = $('#datatblcust').DataTable( {
            //"stateSave": true,
            //"order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                //{ "orderable": false, "targets": 0 },
                //{ "orderable": false, "targets": 1 },
                //{ className: "text-right", "targets": [0,5,6,7,8,9] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3,4,5,6,7,8] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollX": true,
        } );
        
        var dataTable = $('#datatblcust2').DataTable( {
            //"stateSave": true,
            //"order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                //{ "orderable": false, "targets": 0 },
                //{ "orderable": false, "targets": 1 },
                //{ className: "text-right", "targets": [0,5,6,7,8,9] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3,4,5] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollX": true,
        } );
    } );
    
    
    
    function ProsesPindahDataCabCust() {
        
        var icab=document.getElementById('txt_idcab_view').value;
        var inmcab=document.getElementById('txt_nmcab_view').value;
        var idarea=document.getElementById('txt_idarea_view').value;
        var nmarea=document.getElementById('txt_nmarea_view').value;
        
        var icab_old=document.getElementById('txt_idcab_view_old').value;
        var inmcab_old=document.getElementById('txt_nmcab_view_old').value;
        var idarea_old=document.getElementById('txt_idarea_view_old').value;
        var nmarea_old=document.getElementById('txt_nmarea_view_old').value;
        
        var ket="prosespindah";
        var pText_="Apakah akan melakukan pindah data \n\
dari cabang : "+inmcab_old+" ("+icab_old+") Area : "+nmarea_old+" ("+idarea_old+")\n\
ke cabang : "+inmcab+" ("+icab+") Area : "+nmarea+" ("+idarea+") ...???\n\
Jika sudah yakin, klik OK/Yes...!!!";
        
        
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("form_data01").action = "module/mst_pindahcabareacust/aksi_pindahcabareacust.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("form_data01").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    
</script>


<?PHP
hapusdata:
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
    mysqli_close($cnit);
?>