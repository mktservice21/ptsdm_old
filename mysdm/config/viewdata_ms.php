<?PHP
    session_start();
    include "../config/koneksimysqli.php";
?>
    <!-- Datatables -->
    <script src="../../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../../vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../../vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="../../vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="../../vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="../../vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script src="../../vendors/jszip/dist/jszip.min.js"></script>
    <script src="../../vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../../vendors/pdfmake/build/vfs_fonts.js"></script>

    <script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
            var table = dataTable = $('#mytable').dataTable({
                fixedHeader: false,
                "ordering": true,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": 10,
            });
        } );
    </script>


    <div class='modal-dialog modal-lg'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
                <h4 class='modal-title'>Pilih Kelompok</h4>
            </div>

            <div class='modal-body'>
                <?PHP
                if ($_GET['module']=="viewkaryawan"){
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='60px'>Kode</th><th>Nama</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    $no=1;
                    $tampil = mysqli_query($cnmy, "SELECT DISTINCT karyawanId, nama FROM hrd.karyawan order by nama, karyawanId");
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a data-dismiss='modal' href='#' onClick=\"getDataModalKaryawan('$_POST[udata1]', '$_POST[udata2]', '$r[karyawanId]', '$r[nama]')\">
                            $r[karyawanId]</a></td>";
                        echo "<td>$r[nama]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                }elseif ($_GET['module']=="viewkaryawanjbt"){
                    $kdjbt="";
                    if (!empty($_POST['ukdjbt'])) $kdjbt=" and jabatanId='$_POST[ukdjbt]' ";
                    if ($_POST['ukdjbt']==10 OR $_POST['ukdjbt']==18) $kdjbt=" and jabatanId in (18, 10) ";
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='60px'>Kode</th><th>Nama</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    $no=1;
                    $tampil = mysqli_query($cnmy, "SELECT DISTINCT karyawanId, nama FROM hrd.karyawan where 1=1 $kdjbt order by nama, karyawanId");
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a data-dismiss='modal' href='#' onClick=\"getDataModalKaryawan('$_POST[udata1]', '$_POST[udata2]', '$r[karyawanId]', '$r[nama]')\">
                            $r[karyawanId]</a></td>";
                        echo "<td>$r[nama]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                }elseif ($_GET['module']=="viewkaryawancabang"){
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='60px'>Kode</th><th>Nama</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    $no=1;
                    $icabangid = $_POST['uicabang']; 
                    if (($icabangid=='30') or ($icabangid=='31') or ($icabangid=='0000000032')) {
                            $query = "select karyawanId,nama,jabatanid,icabangid from hrd.karyawan where (karyawanId='0000000154' or karyawanId='0000000159') AND aktif = 'Y' order by nama"; 
                    } else {
                            $query = "select karyawanId,nama,jabatanid,icabangid from hrd.karyawan where icabangid='$icabangid' AND aktif = 'Y' order by nama"; 
                    }
                    $tampil = mysqli_query($cnmy, $query);
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a data-dismiss='modal' href='#' onClick=\"getDataModalKaryawan('$_POST[udata1]', '$_POST[udata2]', '$r[karyawanId]', '$r[nama]', '$icabangid')\">
                            $r[karyawanId]</a></td>";
                        echo "<td>$r[nama]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                }elseif ($_GET['module']=="viewkaryawandiv"){
                    $fildiv="";
                    if (empty($_POST['uedivprod'])){
                        
                    }else{
                        if ($_POST['uedivprod']=="HO")
                            $fildiv=" and ifnull(divisiId,'HO') in ('', 'HO') ";
                        else
                            $fildiv=" and divisiId='$_POST[uedivprod]' ";
                    }
                    
                    $query="SELECT DISTINCT karyawanId, nama FROM hrd.karyawan WHERE 1=1 $fildiv ";
                    $query .=" order by nama, karyawanId";
                    
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='60px'>Kode</th><th>Nama</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    $no=1;
                    $tampil = mysqli_query($cnmy, $query);
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a data-dismiss='modal' href='#' onClick=\"getDataModalKaryawan('$_POST[udata1]', '$_POST[udata2]', '$r[karyawanId]', '$r[nama]')\">
                            $r[karyawanId]</a></td>";
                        echo "<td>$r[nama]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                }elseif ($_GET['module']=="viewbgakun"){
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='60px'>Divisi</th><th width='60px'>Kode</th><th>Nama</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    $no=1;
                    $tampil = mysqli_query($cnmy, "SELECT DISTINCT divprodid, kodeid, nama FROM dbbudget.v_akun_budget order by divprodid, nama, kodeid");
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td>$r[divprodid]</td>";
                        echo "<td><a data-dismiss='modal' href='#' onClick=\"getDataModalBgAkun('$_POST[udata1]', '$_POST[udata2]', '$r[kodeid]', '$r[nama]')\">
                            $r[kodeid]</a></td>";
                        echo "<td>$r[nama]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                }elseif ($_GET['module']=="viewbgakundivprod"){
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='60px'>Divisi</th><th width='60px'>Kode</th><th>Nama</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    $no=1;
                    $tampil = mysqli_query($cnmy, "SELECT DISTINCT divprodid, kodeid, nama FROM dbbudget.v_akun_budget where divprodid='$_POST[udivprod]' order by divprodid, nama, kodeid");
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td>$r[divprodid]</td>";
                        echo "<td><a data-dismiss='modal' href='#' onClick=\"getDataModalBgAkun('$_POST[udata1]', '$_POST[udata2]', '$r[kodeid]', '$r[nama]')\">
                            $r[kodeid]</a></td>";
                        echo "<td>$r[nama]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                }elseif ($_GET['module']=="viewdatacabang"){
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='80px'>Kode</th><th>Nama</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    $no=1;
                    $tampil = mysqli_query($cnmy, "SELECT distinct iCabangId, nama from dbmaster.icabang where aktif='Y'");
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a data-dismiss='modal' href='#' onClick=\"getDataModalCabang('$_POST[udata1]', '$_POST[udata2]', '$r[iCabangId]', '$r[nama]')\">
                            $r[iCabangId]</a></td>";
                        echo "<td>$r[nama]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                }elseif ($_GET['module']=="viewdatacabangfmr"){
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='80px'>Kode</th><th>Nama</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    $no=1;
                    $karyawanId = $_POST['umr']; 
                    $query = "select karyawan.iCabangId, cabang.nama from hrd.karyawan as karyawan join dbmaster.icabang as cabang on "
                            . " karyawan.icabangid=cabang.icabangid where karyawanId='$karyawanId'"; 
                    $tampil = mysqli_query($cnmy, $query);
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a data-dismiss='modal' href='#' onClick=\"getDataModalCabang('$_POST[udata1]', '$_POST[udata2]', '$r[iCabangId]', '$r[nama]')\">
                            $r[iCabangId]</a></td>";
                        echo "<td>$r[nama]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                }elseif ($_GET['module']=="viewdatadokter"){
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='80px'>Kode</th><th>Nama</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    $no=1;
                    $tampil = mysqli_query($cnmy, "SELECT distinct dokterId, nama from dbmaster.dokter");
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a data-dismiss='modal' href='#' onClick=\"getDataModalDokter('$_POST[udata1]', '$_POST[udata2]', '$r[dokterId]', '$r[nama]')\">
                            $r[dokterId]</a></td>";
                        echo "<td>$r[nama]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                }elseif ($_GET['module']=="viewdatadoktermrcabang"){
                    
                    
                    $mr_id = $_POST['umr']; 
                    $icabangid = $_POST['ucab']; 

                    $query = "select iCabangId from hrd.karyawan where iCabangId='$icabangid'"; 
                    $result = mysqli_query($cnmy, $query); 
                    $record = mysqli_num_rows($result); 
                    if ($icabangid=="0000000001") {
                        $query = "select distinct (mr_dokt.dokterId),CONCAT(dokter.nama,' - ',dokter.dokterId) AS nama 
                                          from dbmaster.mr_dokt as mr_dokt 
                                          join dbmaster.dokter as dokter on mr_dokt.dokterId=dokter.dokterId
                                          where mr_dokt.aktif <> 'N' and dokter.nama<>''
                                          order by nama"; 
                    } else {
                        $query = "select dokter.dokterId, CONCAT(dokter.nama,' - ',dokter.dokterId) AS nama 
                                          FROM dbmaster.mr_dokt as mr_dokt 
                                          join hrd.karyawan as karyawan on mr_dokt.karyawanId=karyawan.karyawanId
                                          join dbmaster.dokter as dokter on mr_dokt.dokterId=dokter.dokterId
                                          where mr_dokt.aktif <> 'N' and karyawan.karyawanId='$mr_id' and dokter.nama <> ''
                                          order by dokter.nama";
                    }
                    
                    
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='80px'>Kode</th><th>Nama</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    $no=1;
                    $tampil = mysqli_query($cnmy, $query);
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a data-dismiss='modal' href='#' onClick=\"getDataModalDokter('$_POST[udata1]', '$_POST[udata2]', '$r[dokterId]', '$r[nama]')\">
                            $r[dokterId]</a></td>";
                        echo "<td>$r[nama]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                }elseif ($_GET['module']=="viewdatasubposting"){
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='80px'>Kode</th><th>Nama</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    $no=1;
                    $tampil = mysqli_query($cnmy, "SELECT distinct subpost, nmsubpost from dbmaster.brkd_otc where ifnull(subpost,'') <> ''");
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a data-dismiss='modal' href='#' "
                        . "onClick=\"getDataModalSubPosting('$_POST[uonklik]', '$_POST[udata1]', '$_POST[udata2]', '$r[subpost]', '$r[nmsubpost]')\">
                            $r[subpost]</a></td>";
                        echo "<td>$r[nmsubpost]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                }elseif ($_GET['module']=="viewdatacomboposting"){
                    $tampil = mysqli_query($cnmy, "SELECT distinct kodeid, nama from dbmaster.brkd_otc where ifnull(subpost,'') = '$_POST[kodesub]'");
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<option value='$r[kodeid]'>$r[kodeid] - $r[nama]</option>";
                    }
                }elseif ($_GET['module']=="viewdatadistributor"){
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='80px'>Kode</th><th>Nama</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    $no=1;
                    $tampil = mysqli_query($cnmy, "SELECT distinct Distid, nama, alamat1 from dbmaster.distrib0");
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a data-dismiss='modal' href='#' "
                        . "onClick=\"getDataModalDistributor('$_POST[udata1]', '$_POST[udata2]', '$r[Distid]', '$r[nama]')\">
                            $r[Distid]</a></td>";
                        echo "<td>$r[nama]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                }elseif ($_GET['module']=="viewdataareacabang"){
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='80px'>Kode</th><th>Nama</th><th>Akif</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    $no=1;
                    $tampil = mysqli_query($cnmy, "select areaId, Nama, aktif from dbmaster.iarea where iCabangId='$_POST[ucabang]'");
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a data-dismiss='modal' href='#' "
                        . "onClick=\"getDataModalArea('$_POST[udata1]', '$_POST[udata2]', '$r[areaId]', '$r[Nama]')\">
                            $r[areaId]</a></td>";
                        echo "<td>$r[Nama]</td>";
                        echo "<td>$r[aktif]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                }elseif ($_GET['module']=="viewakunlevel5"){
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='60px'>Level4</th><th width='60px'>COA</th><th>NAMA</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    
                    $level1="";
                    if (!empty($_POST['ulevel1'])) $level1=" and c1.COA1='$_POST[ulevel1]' ";
                    $level2="";
                    if (!empty($_POST['ulevel2'])) $level1=" and c2.COA2='$_POST[ulevel2]' ";
                    $level3="";
                    if (!empty($_POST['ulevel3'])) $level1=" and c3.COA3='$_POST[ulevel3]' ";
                    $level4="";
                    if (!empty($_POST['ulevel4'])) $level1=" and c4.COA4='$_POST[ulevel4]' ";
                    
                    $tampil = mysqli_query($cnmy, "SELECT c1.*, c2.*, c3.*, c4.*, c5.* FROM dbmaster.coa as c5 "
                            . "left join dbmaster.coa_level4 as c4 on c5.COA4=c4.COA4 "
                            . "left join dbmaster.coa_level3 as c3 on c4.COA3=c3.COA3 "
                            . "left join dbmaster.coa_level2 as c2 on c3.COA2=c2.COA2 "
                            . "left join dbmaster.coa_level1 as c1 on c2.COA1=c1.COA1 "
                            . " WHERE 1=1 $level1 $level2 $level3 $level4 "
                            . "order by c1.COA1, c2.COA2, c3.COA3, c4.COA4, c5.COA_KODE");
                
                    $no=1;
                    //$tampil = mysqli_query($cnmy, "SELECT COA4, COA_KODE, COA_NAMA FROM dbmaster.coa order by COA_KODE");
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td>$r[COA4]</td>";
                        echo "<td><a data-dismiss='modal' href='#' onClick=\"getDataModalBgAkun('$_POST[udata1]', '$_POST[udata2]', '$r[COA_KODE]', '$r[COA_NAMA]')\">
                            $r[COA_KODE]</a></td>";
                        echo "<td>$r[COA_NAMA]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                }elseif ($_GET['module']=="viewdatatokocab"){
                    
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='80px'>Kode</th><th>Nama</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    $no=1;
                    
                    $cabang = "";
                    //if (!empty($_POST['ucab'])) 
                        $cabang=" AND icabangid_o='$_POST[ucab]' ";
                    $area="";
                    if (isset($_POST['uarea'])) {
                        if (!empty($_POST['uarea']))
                            $area=" and areaid_o='$_POST[uarea]' ";
                    }

                    $query = "select icustid_o, nama from MKT.icust_o where 1=1 $cabang $area order by nama";
    
                    $tampil = mysqli_query($cnmy, $query);
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a data-dismiss='modal' href='#' "
                        . "onClick=\"getDataModalToko('$_POST[udata1]', '$_POST[udata2]', '$r[icustid_o]', '$r[nama]')\">
                            $r[icustid_o]</a></td>";
                        echo "<td>$r[nama]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                    
                }elseif ($_GET['module']=="viewdatakontak"){
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='80px'>Kode</th><th>Nama</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    $no=1;
                    
                    $cabang = "";
                    if (!empty($_POST['ucab'])) 
                        $cabang=" AND icabangid_o='$_POST[ucab]' ";

                    $query = "select idkontak, nama from dbmaster.t_kotak_realisasi where 1=1 $cabang order by nama";
    
                    $tampil = mysqli_query($cnmy, $query);
                    while ($r=mysqli_fetch_array($tampil)){
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a data-dismiss='modal' href='#' "
                        . "onClick=\"getDataModalKontak('$_POST[udata1]', '$_POST[udata2]', '$r[idkontak]', '$r[nama]')\">
                            $r[idkontak]</a></td>";
                        echo "<td>$r[nama]</td>";
                        echo "</tr>";
                        $no++;
                    }
                    echo "</tbody>";
                    echo "</table>";
                    
                }elseif ($_GET['module']=="viewdatabrperdivisi"){
                    
                    include "../config/koneksimysqli.php";
                    
                    $pnoslip=$_POST['unospd'];
                    $pnodivisi=$_POST['unodivisi'];
                    
                    $now=date("mdYhis");
                    $tmp01 =" dbtemp.DBRSLIPDIV01_".$_SESSION['IDCARD']."_$now ";
                    $tmp02 =" dbtemp.DBRSLIPDIV02_".$_SESSION['IDCARD']."_$now ";
                    $tmp03 =" dbtemp.DBRSLIPDIV03_".$_SESSION['IDCARD']."_$now ";
                    
                    $query = "select b.divisi, a.idinput, a.bridinput, b.nodivisi, b.nomor from dbmaster.t_suratdana_br1 a "
                            . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE b.nodivisi='$pnodivisi'";
                    $query ="CREATE TEMPORARY TABLE $tmp01 ($query)";
                    mysqli_query($cnmy, $query);
                    
                    $query = "select divisi from $tmp01 WHERE IFNULL(divisi,'')<>'' LIMIT 1";
                    $tampil = mysqli_query($cnmy, $query);
                    $z= mysqli_fetch_array($tampil);
                    $ndivisi=$z['divisi'];
                    
                    if ($ndivisi=="OTC") {
                        
                        $query = "select tglbr tgl, tgltrans, brOtcId brId, noslip, 
                            '' iddokter, '' nama_dokter, keterangan1 aktivitas1, 
                            keterangan2 aktivitas2, jumlah  from hrd.br_otc WHERE brOtcId IN 
                            (select DISTINCT IFNULL(a.bridinput,'') bridinput from $tmp01 a)";
                        
                        $query ="CREATE TEMPORARY TABLE $tmp03 ($query)";
                        mysqli_query($cnmy, $query);
                        
                    }else{
                    
                        $query = "select tgl, tgltrans, brId, noslip, dokterid, aktivitas1, aktivitas2, icabangid, "
                                . " karyawanid, karyawani2, kode, coa4, jumlah, jumlah1 from hrd.br0 WHERE 1=1 AND "
                                . " YEAR(tgl)>='2019'";
                        
                        if (!empty($pnodivisi)) {
                            $query .=" AND  brId IN (select DISTINCT IFNULL(a.bridinput,'') bridinput from $tmp01 a) ";
                        }
                        
                        $query ="CREATE TEMPORARY TABLE $tmp02 ($query)";
                        mysqli_query($cnmy, $query);

                        $query = "select a.*, b.nama nama_dokter from $tmp02 a LEFT JOIN hrd.dokter b on a.dokterid=b.dokterid";
                        $query ="CREATE TEMPORARY TABLE $tmp03 ($query)";
                        mysqli_query($cnmy, $query);
                        
                    }
                    

                    $query = "select brId, noslip, nama_dokter, aktivitas1, Format(jumlah,0) as jumlah From $tmp03 order by noslip, brId";
                    
                                                    
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='80px'>ID</th><th>NoSlip</th><th>Dokter</th><th>Jumlah</th><th>Keterangan</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    
                    $no=1;
                    $tampil = mysqli_query($cnmy, $query);
                    while ($r=mysqli_fetch_array($tampil)){
                        $pnobr=$r['brId'];
                        $pnoslip=$r['noslip'];
                        $pnmdokter=$r['nama_dokter'];
                        $pket=$r['aktivitas1'];
                        $pjumlah=$r['jumlah'];
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a data-dismiss='modal' href='#' "
                        . "onClick=\"getDataModalBRSlip('$_POST[udata1]', '$_POST[udata2]', '$pnobr', '$pnoslip')\">
                            $pnobr</a></td>";
                        echo "<td>$pnoslip</td>";
                        echo "<td>$pnmdokter</td>";
                        echo "<td>$pjumlah</td>";
                        echo "<td>$pket</td>";
                        echo "</tr>";
                        $no++;
                    }
                    
                    echo "</tbody>";
                    echo "</table>";
                    
                    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
                    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
                    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
                    mysqli_close($cnmy);
                    
                }elseif ($_GET['module']=="viewdataspddivisiperuserid"){
                    include "../config/koneksimysqli.php";
                    
                    $pidkry=$_POST['uidkry'];
                    $pbukaall=$_POST['ubuka'];

                    $ptgl = str_replace('/', '-', $_POST['utgl']);
                    
                    $tgl_pertama = date('Ym', strtotime('-2 month', strtotime($ptgl)));
                    $tgl_kedua= date("Ym", strtotime($ptgl));
                    
                    $query = "select idinput, DATE_FORMAT(tgl,'%d/%m/%Y') as tgl, nomor, nodivisi, jumlah from dbmaster.t_suratdana_br WHERE "
                            . " IFNULL(stsnonaktif,'')<>'Y' AND DATE_FORMAT(tgl,'%Y%m') >='$tgl_pertama' ";
                    if ($pbukaall=="1") {
                        
                    }else{
                        $query .=" AND userid='$pidkry' ";
                    }
                    $query .=" ORDER BY idinput desc";
                    
                    
                    
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='80px'>ID</th><th>Tgl</th><th>Nomor</th><th>No Divisi</th><th>Jumlah</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    
                    $no=1;
                    $tampil = mysqli_query($cnmy, $query);
                    while ($r=mysqli_fetch_array($tampil)){
                        $pidinput=$r['idinput'];
                        $ptgl=$r['tgl'];
                        $pnomor=$r['nomor'];
                        $pnodivisi=$r['nodivisi'];
                        $pjumlah=$r['jumlah'];
                        
                        $pjumlah=number_format($pjumlah,0,",",",");
                        
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a class='btn btn-dark btn-xs' data-dismiss='modal' href='#' "
                        . "onClick=\"getDataModalNoDivisi('$_POST[udata1]', '$_POST[udata2]', '$_POST[udata3]', '$pidinput', '$pnodivisi', '$pjumlah')\">
                            $pidinput</a></td>";
                        echo "<td>$ptgl</td>";
                        echo "<td>$pnomor</td>";
                        echo "<td>$pnodivisi</td>";
                        echo "<td align='right'>$pjumlah</td>";
                        echo "</tr>";
                        $no++;
                        
                    }
                    
                    echo "</tbody>";
                    echo "</table>";
                    
                    mysqli_close($cnmy);
                }elseif ($_GET['module']=="viewdataspddivisiperuserid2"){
                    include "../config/koneksimysqli.php";
                    
                    $pidkry=$_POST['uidkry'];
                    $pbukaall=$_POST['ubuka'];
					
                    $pnospdp="";
                    if (isset($_POST['upnospd'])) $pnospdp=$_POST['upnospd'];
                    $filterspd="";
                    if (!empty($pnospdp)) $filterspd=" AND nomor='$pnospdp' ";

                    $ptgl = str_replace('/', '-', $_POST['utgl']);
                    
                    $tgl_pertama = date('Ym', strtotime('-2 month', strtotime($ptgl)));
                    $tgl_kedua= date("Ym", strtotime($ptgl));
					
					$ptahunpil= date("Y", strtotime($ptgl));
                    
                    $query = "select idinput, DATE_FORMAT(tgl,'%d/%m/%Y') as tgl, nomor, nodivisi, jumlah from dbmaster.t_suratdana_br WHERE "
                            . " IFNULL(stsnonaktif,'')<>'Y' AND DATE_FORMAT(tgl,'%Y') >='$ptahunpil' $filterspd AND subkode NOT IN ('25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '37', '38')";
                    if ($pbukaall=="1") {
                        
                    }else{
                        $query .=" AND userid='$pidkry' ";
                    }
                    $query .=" ORDER BY idinput desc";
                    
                    
                    
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='80px'>ID</th><th>Tgl</th><th>Nomor</th><th>No Divisi</th><th>Jumlah</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    
                    $no=1;
                    $tampil = mysqli_query($cnmy, $query);
                    while ($r=mysqli_fetch_array($tampil)){
                        $pidinput=$r['idinput'];
                        $ptgl=$r['tgl'];
                        $pnomor=$r['nomor'];
                        $pnodivisi=$r['nodivisi'];
                        $pjumlah=$r['jumlah'];
                        
                        $pjumlah=number_format($pjumlah,0,",",",");
                        
                        echo "<tr scope='row'><td>$no</td>";
						
                        echo "<td><a class='' data-dismiss='modal' href='#' "
                        . "onClick=\"getDataModalNoDivisi('$_POST[udata1]', '$_POST[udata2]', '$_POST[udata3]', '$pidinput', '$pnodivisi', '$pjumlah')\">
                            $pidinput</a></td>";
							
                        echo "<td>$ptgl</td>";
                        echo "<td>$pnomor</td>";
						
						if (!empty($pnodivisi)) {
							
							echo "<td><a class='btn btn-dark btn-xs' data-dismiss='modal' href='#' "
							. "onClick=\"getDataModalNoDivisi('$_POST[udata1]', '$_POST[udata2]', '$_POST[udata3]', '$pidinput', '$pnodivisi', '$pjumlah')\">
								$pnodivisi</a></td>";
							
						}else{
							echo "<td>$pnodivisi</td>";
						}
						
                        echo "<td align='right'>$pjumlah</td>";
                        echo "</tr>";
                        $no++;
                        
                    }
                    
                    echo "</tbody>";
                    echo "</table>";
                    
                    mysqli_close($cnmy);
                }elseif ($_GET['module']=="viewdatagimicpenerima"){
                    include "../config/koneksimysqli.php";

                    
                    $query ="select * from dbmaster.t_barang_penerima WHERE IFNULL(AKTIF,'')<>'N' ORDER BY NAMA_PENERIMA";
                                        
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                    echo "<thead><tr><th width='10px'>No</th><th width='80px'>Nama</th><th>Alamat</th><th>Kota</th><th>Kd Pos</th><th>Hp</th></tr></thead>";
                    echo "<tbody class='gridview-error'>";
                    
                    $no=1;
                    $tampil = mysqli_query($cnmy, $query);
                    while ($r=mysqli_fetch_array($tampil)){
                        $pidpenerima=$r['IDPENERIMA'];
                        $pnmpenerima=$r['NAMA_PENERIMA'];
                        $palamat1=$r['ALAMAT1'];
                        $palamat2=$r['ALAMAT2'];
                        $pkota=$r['KOTA'];
                        $pprovinsi=$r['PROVINSI'];
                        $pkodepos=$r['KODEPOS'];
                        $php=$r['HP'];
                        
                        $putkkota="";
                        if (!empty($pkota)) {
                            $putkkota=$pkota;
                            if (!empty($pprovinsi)) $putkkota=$pkota.", ".$pprovinsi;
                        }
                        
                        echo "<tr scope='row'><td>$no</td>";
                        echo "<td><a class='btn btn-dark btn-xs' data-dismiss='modal' href='#' "
                        . "onClick=\"getDataModalPenerima('$_POST[udata1]', '$_POST[udata2]', '$_POST[udata3]', '$pidpenerima', '$pnmpenerima', '$palamat1')\">
                            $pnmpenerima</a></td>";
                        echo "<td>$palamat1 $palamat2</td>";
                        echo "<td>$putkkota</td>";
                        echo "<td>$pkodepos</td>";
                        echo "<td>$php</td>";
                        echo "</tr>";
                        $no++;
                        
                    }
                    
                    echo "</tbody>";
                    echo "</table>";
                    
                    mysqli_close($cnmy);
					
                }elseif ($_GET['module']=="view2"){
                }elseif ($_GET['module']=="view2"){
                }elseif ($_GET['module']=="view2"){
                    
                }else{
                    
                }
                ?>
            </div>

            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>