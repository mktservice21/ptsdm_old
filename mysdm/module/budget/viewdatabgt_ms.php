<?PHP
date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);
    
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];
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
                <h4 class='modal-title'>Pilih Data</h4>
            </div>

            <div class='modal-body'>
            <?PHP
            if ($pmodule=="viewdataspddivisiperuserid2") {
                include "../../config/koneksimysqli.php";

                $pidkry=$_POST['uidkry'];
                $pbukaall=$_POST['ubuka'];

                $pnospdp="";
                if (isset($_POST['upnospd'])) $pnospdp=$_POST['upnospd'];
                $filterspd="";
                if (!empty($pnospdp)) $filterspd=" AND a.nomor='$pnospdp' ";

                $ptgl = str_replace('/', '-', $_POST['utgl']);

                $tgl_pertama = date('Ym', strtotime('-2 month', strtotime($ptgl)));
                $tgl_kedua= date("Ym", strtotime($ptgl));

                $ptahunpil= date("Y", strtotime($ptgl));

                $query = "select a.idinput, DATE_FORMAT(a.tgl,'%d/%m/%Y') as tgl, a.nomor, a.nodivisi, kodeinput, c.nama_pengajuan, a.jumlah "
                        . " from dbmaster.t_suratdana_br as a "
                        . " LEFT JOIN (select distinct idinput, kodeinput from dbmaster.t_suratdana_br1) as b on a.idinput=b.idinput "
                        . " LEFT JOIN dbmaster.t_kode_spd_pengajuan as c on a.jenis_rpt=c.jenis_rpt and a.subkode=c.subkode WHERE "
                        . " IFNULL(a.stsnonaktif,'')<>'Y' AND DATE_FORMAT(a.tgl,'%Y') >='$ptahunpil' $filterspd "
                        . " AND a.subkode NOT IN ('25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '37', '38')";
                if ($pbukaall=="1") {

                }else{
                    $query .=" AND userid='$pidkry' ";
                }
                $query .=" ORDER BY idinput desc";



                echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                
                echo "<thead>";
                    echo "<tr>";
                        echo "<th width='10px'>No</th>";
                        echo "<th width='80px'>ID</th>";
                        echo "<th>Tgl</th>";
                        echo "<th>Jenis</th>";
                        echo "<th>Nomor</th>";
                        echo "<th>No Divisi</th>";
                        echo "<th>Jumlah</th>";
                    echo "</tr>";
                echo "</thead>";
                echo "<tbody class='gridview-error'>";

                $no=1;
                $tampil = mysqli_query($cnmy, $query);
                while ($r=mysqli_fetch_array($tampil)){
                    $pidinput=$r['idinput'];
                    $ptgl=$r['tgl'];
                    $pnomor=$r['nomor'];
                    $pnodivisi=$r['nodivisi'];
                    $pnmpengajuan=$r['nama_pengajuan'];
                    $pjumlah=$r['jumlah'];
                    $pkodeinput=$r['kodeinput'];

                    $pjumlah=number_format($pjumlah,0,",",",");

                    echo "<tr scope='row'><td>$no</td>";

                    echo "<td><a class='' data-dismiss='modal' href='#' "
                    . "onClick=\"getDataModalNoDivisi('$_POST[udata1]', '$_POST[udata2]', '$_POST[udata3]', '$_POST[udata4]', '$pidinput', '$pnodivisi', '$pjumlah', '$pkodeinput')\">
                        $pidinput</a></td>";

                    echo "<td>$ptgl</td>";
                    echo "<td>$pnmpengajuan</td>";
                    echo "<td>$pnomor</td>";

                    if (!empty($pnodivisi)) {

                        echo "<td><a class='btn btn-dark btn-xs' data-dismiss='modal' href='#' "
                        . "onClick=\"getDataModalNoDivisi('$_POST[udata1]', '$_POST[udata2]', '$_POST[udata3]', '$_POST[udata4]', '$pidinput', '$pnodivisi', '$pjumlah', '$pkodeinput')\">
                                $pnodivisi</a></td>";

                    }else{
                        echo "<td>$pnodivisi $pkodeinput</td>";
                    }

                    echo "<td align='right'>$pjumlah</td>";
                    echo "</tr>";
                    $no++;

                }

                echo "</tbody>";
                echo "</table>";

                mysqli_close($cnmy);
            }elseif ($pmodule=="viewdatabrinput") {
                include "../../config/koneksimysqli.php";

                $pidinput=$_POST['uidinput'];
                $pkodeinput=$_POST['ukodeinput'];
                
                
                $query = "select * from dbmaster.t_kode_spd_tabel WHERE kodeinput='$pkodeinput'";
                $tampil = mysqli_query($cnmy, $query);
                $nrow= mysqli_fetch_array($tampil);
                $nmtable=$nrow['tabel_link'];
                
                if ($nmtable=="hrd.br0") {
                    
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                        echo "<thead>";
                            echo "<tr>";
                                echo "<th width='10px'>No</th>";
                                echo "<th width='80px'>ID</th>";
                                echo "<th width='50px'>Noslip</th>";
                                echo "<th width='60px'>Dokter</th>";
                                echo "<th width='60px'>Realisasi</th>";
                                echo "<th width='50px'>Jumlah</th>";
                                echo "<th width='100px'>Keterangan</th>";
                            echo "</tr>";
                        echo "</thead>";
                        
                        echo "<tbody class='gridview-error'>";
                            $no=1;
                            $query = "select a.brId as brid, a.noslip, a.dokterId as dokterid, b.nama as nama_dokter, a.realisasi1, "
                                    . " a.jumlah, a.aktivitas1 as keterangan "
                                    . " from hrd.br0 as a LEFT JOIN hrd.dokter as b on a.dokterId=b.dokterId JOIN "
                                    . " dbmaster.t_suratdana_br1 as c on a.brId=c.bridinput WHERE c.idinput='$pidinput'";
                            $query .=" Order by a.brId";
                            $tampil = mysqli_query($cnmy, $query);
                            while ($nr=mysqli_fetch_array($tampil)){
                                $nbrid=$nr['brid'];
                                $nnoslip=$nr['noslip'];
                                $ndokterid=$nr['dokterid'];
                                $ndokternm=$nr['nama_dokter'];
                                $nrealisasi=$nr['realisasi1'];
                                $njumlah=$nr['jumlah'];
                                $nketerangan=$nr['keterangan'];
                                
                                $njumlah=number_format($njumlah,0,",",",");
                                
                                $nnmrealisasi=$nrealisasi;
                                $nnmdokter=$ndokternm;
                                $nnmket=$nketerangan;
                                
                                if (!empty($nnmrealisasi)) $nnmrealisasi=strip_tags_content($nnmrealisasi);
                                if (!empty($nnmrealisasi)) $nnmrealisasi = preg_replace("/[\\n\\r]+/", "", $nnmrealisasi);
                                
                                if (!empty($nnmdokter)) $nnmdokter=strip_tags_content($nnmdokter);
                                if (!empty($nnmdokter)) $nnmdokter = preg_replace("/[\\n\\r]+/", "", $nnmdokter);
                                
                                if (!empty($nnmket)) $nnmket=strip_tags_content($nnmket);
                                if (!empty($nnmket)) $nnmket = preg_replace("/[\\n\\r]+/", "", $nnmket);
                            
                            
                                $nlink="<a class='btn btn-dark btn-xs' data-dismiss='modal' href='#' "
                                        . " onClick=\"getDataModalBrInput('$_POST[udata1]', '$_POST[udata2]', '$_POST[udata3]', '$_POST[udata4]', '$_POST[udata5]', '$nbrid', '$nnoslip', '$nnmrealisasi', '$nnmdokter', '$nnmket')\">"
                                        . " $nbrid</a>";
                                
                                echo "<tr>";
                                echo "<td nowrap>$no</td>";
                                echo "<td nowrap>$nlink</td>";
                                echo "<td nowrap>$nnoslip</td>";
                                echo "<td nowrap>$ndokternm</td>";
                                echo "<td nowrap>$nrealisasi</td>";
                                echo "<td nowrap>$njumlah</td>";
                                echo "<td >$nketerangan</td>";
                                echo "</tr>";
                                
                                $no++;
                                
                            }
                            
                            
                        echo "</tbody>";
                    echo "</table>";
                    
                    
                }elseif ($nmtable=="hrd.br_otc") {
                    
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                        echo "<thead>";
                            echo "<tr>";
                                echo "<th width='10px'>No</th>";
                                echo "<th width='80px'>ID</th>";
                                echo "<th width='50px'>Noslip</th>";
                                echo "<th width='60px'>Dokter</th>";
                                echo "<th width='60px'>Realisasi</th>";
                                echo "<th width='50px'>Jumlah</th>";
                                echo "<th width='100px'>Keterangan</th>";
                            echo "</tr>";
                        echo "</thead>";
                        
                        echo "<tbody class='gridview-error'>";
                            $no=1;
                            $query = "select a.brOtcId as brid, a.noslip, '' as dokterid, '' as nama_dokter, a.real1 as realisasi1, "
                                    . " a.jumlah, a.keterangan1 as keterangan from hrd.br_otc as a JOIN "
                                    . " dbmaster.t_suratdana_br1 as c on a.brOtcId=c.bridinput WHERE c.idinput='$pidinput'";
                            $query .=" Order by a.brOtcId";
                            $tampil = mysqli_query($cnmy, $query);
                            while ($nr=mysqli_fetch_array($tampil)){
                                $nbrid=$nr['brid'];
                                $nnoslip=$nr['noslip'];
                                $ndokterid=$nr['dokterid'];
                                $ndokternm=$nr['nama_dokter'];
                                $nrealisasi=$nr['realisasi1'];
                                $njumlah=$nr['jumlah'];
                                $nketerangan=$nr['keterangan'];
                                
                                $njumlah=number_format($njumlah,0,",",",");
                                
                                $nnmrealisasi=$nrealisasi;
                                $nnmdokter=$ndokternm;
                                $nnmket=$nketerangan;
                                
                                if (!empty($nnmrealisasi)) $nnmrealisasi=strip_tags_content($nnmrealisasi);
                                if (!empty($nnmrealisasi)) $nnmrealisasi = preg_replace("/[\\n\\r]+/", "", $nnmrealisasi);
                                
                                if (!empty($nnmdokter)) $nnmdokter=strip_tags_content($nnmdokter);
                                if (!empty($nnmdokter)) $nnmdokter = preg_replace("/[\\n\\r]+/", "", $nnmdokter);
                                
                                if (!empty($nnmket)) $nnmket=strip_tags_content($nnmket);
                                if (!empty($nnmket)) $nnmket = preg_replace("/[\\n\\r]+/", "", $nnmket);
                            
                            
                                $nlink="<a class='btn btn-dark btn-xs' data-dismiss='modal' href='#' "
                                        . " onClick=\"getDataModalBrInput('$_POST[udata1]', '$_POST[udata2]', '$_POST[udata3]', '$_POST[udata4]', '$_POST[udata5]', '$nbrid', '$nnoslip', '$nnmrealisasi', '$nnmdokter', '$nnmket')\">"
                                        . " $nbrid</a>";
                                
                                echo "<tr>";
                                echo "<td nowrap>$no</td>";
                                echo "<td nowrap>$nlink</td>";
                                echo "<td nowrap>$nnoslip</td>";
                                echo "<td nowrap>$ndokternm</td>";
                                echo "<td nowrap>$nrealisasi</td>";
                                echo "<td nowrap>$njumlah</td>";
                                echo "<td >$nketerangan</td>";
                                echo "</tr>";
                                
                                $no++;
                                
                            }
                            
                            
                        echo "</tbody>";
                    echo "</table>";
                    
                }elseif ($nmtable=="hrd.klaim") {
                    
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                        echo "<thead>";
                            echo "<tr>";
                                echo "<th width='10px'>No</th>";
                                echo "<th width='80px'>ID</th>";
                                echo "<th width='50px'>Noslip</th>";
                                echo "<th width='60px'>Dokter</th>";
                                echo "<th width='60px'>Realisasi</th>";
                                echo "<th width='50px'>Jumlah</th>";
                                echo "<th width='100px'>Keterangan</th>";
                            echo "</tr>";
                        echo "</thead>";
                        
                        echo "<tbody class='gridview-error'>";
                            $no=1;
                            $query = "select a.klaimId as brid, a.noslip, a.distid as dokterid, b.nama as nama_dokter, a.realisasi1, "
                                    . " a.jumlah, a.aktivitas1 as keterangan "
                                    . " from hrd.klaim as a LEFT JOIN mkt.distrib0 as b on a.distid=b.Distid JOIN "
                                    . " dbmaster.t_suratdana_br1 as c on a.klaimId=c.bridinput WHERE c.idinput='$pidinput'";
                            $query .=" Order by a.klaimId";
                            $tampil = mysqli_query($cnmy, $query);
                            while ($nr=mysqli_fetch_array($tampil)){
                                $nbrid=$nr['brid'];
                                $nnoslip=$nr['noslip'];
                                $ndokterid=$nr['dokterid'];
                                $ndokternm=$nr['nama_dokter'];
                                $nrealisasi=$nr['realisasi1'];
                                $njumlah=$nr['jumlah'];
                                $nketerangan=$nr['keterangan'];
                                
                                $njumlah=number_format($njumlah,0,",",",");
                                
                                $nnmrealisasi=$nrealisasi;
                                $nnmdokter=$ndokternm;
                                $nnmket=$nketerangan;
                                
                                if (!empty($nnmrealisasi)) $nnmrealisasi=strip_tags_content($nnmrealisasi);
                                if (!empty($nnmrealisasi)) $nnmrealisasi = preg_replace("/[\\n\\r]+/", "", $nnmrealisasi);
                                
                                if (!empty($nnmdokter)) $nnmdokter=strip_tags_content($nnmdokter);
                                if (!empty($nnmdokter)) $nnmdokter = preg_replace("/[\\n\\r]+/", "", $nnmdokter);
                                
                                if (!empty($nnmket)) $nnmket=strip_tags_content($nnmket);
                                if (!empty($nnmket)) $nnmket = preg_replace("/[\\n\\r]+/", "", $nnmket);
                            
                            
                                $nlink="<a class='btn btn-dark btn-xs' data-dismiss='modal' href='#' "
                                        . " onClick=\"getDataModalBrInput('$_POST[udata1]', '$_POST[udata2]', '$_POST[udata3]', '$_POST[udata4]', '$_POST[udata5]', '$nbrid', '$nnoslip', '$nnmrealisasi', '$nnmdokter', '$nnmket')\">"
                                        . " $nbrid</a>";
                                
                                echo "<tr>";
                                echo "<td nowrap>$no</td>";
                                echo "<td nowrap>$nlink</td>";
                                echo "<td nowrap>$nnoslip</td>";
                                echo "<td nowrap>$ndokternm</td>";
                                echo "<td nowrap>$nrealisasi</td>";
                                echo "<td nowrap>$njumlah</td>";
                                echo "<td >$nketerangan</td>";
                                echo "</tr>";
                                
                                $no++;
                                
                            }
                            
                            
                        echo "</tbody>";
                    echo "</table>";
                    
                }elseif ($nmtable=="dbmaster.t_brrutin0") {
                    
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                        echo "<thead>";
                            echo "<tr>";
                                echo "<th width='10px'>No</th>";
                                echo "<th width='80px'>ID</th>";
                                echo "<th width='50px'>Jumlah</th>";
                                echo "<th width='100px'>Keterangan</th>";
                            echo "</tr>";
                        echo "</thead>";
                        
                        echo "<tbody class='gridview-error'>";
                            $no=1;
                            $query = "select a.idrutin as brid, "
                                    . " a.jumlah, a.keterangan as keterangan from dbmaster.t_brrutin0 as a JOIN "
                                    . " dbmaster.t_suratdana_br1 as c on a.idrutin=c.bridinput WHERE c.idinput='$pidinput'";
                            $query .=" Order by a.idrutin";
                            $tampil = mysqli_query($cnmy, $query);
                            while ($nr=mysqli_fetch_array($tampil)){
                                $nbrid=$nr['brid'];
                                $njumlah=$nr['jumlah'];
                                $nketerangan=$nr['keterangan'];
                                $nnoslip="";
                                $njumlah=number_format($njumlah,0,",",",");
                                
                                $nnmrealisasi="";
                                $nnmdokter="";
                                $nnmket=$nketerangan;
                                
                                if (!empty($nnmrealisasi)) $nnmrealisasi=strip_tags_content($nnmrealisasi);
                                if (!empty($nnmrealisasi)) $nnmrealisasi = preg_replace("/[\\n\\r]+/", "", $nnmrealisasi);
                                
                                if (!empty($nnmdokter)) $nnmdokter=strip_tags_content($nnmdokter);
                                if (!empty($nnmdokter)) $nnmdokter = preg_replace("/[\\n\\r]+/", "", $nnmdokter);
                                
                                if (!empty($nnmket)) $nnmket=strip_tags_content($nnmket);
                                if (!empty($nnmket)) $nnmket = preg_replace("/[\\n\\r]+/", "", $nnmket);
                            
                            
                                $nlink="<a class='btn btn-dark btn-xs' data-dismiss='modal' href='#' "
                                        . " onClick=\"getDataModalBrInput('$_POST[udata1]', '$_POST[udata2]', '$_POST[udata3]', '$_POST[udata4]', '$_POST[udata5]', '$nbrid', '$nnoslip', '$nnmrealisasi', '$nnmdokter', '$nnmket')\">"
                                        . " $nbrid</a>";
                                
                                echo "<tr>";
                                echo "<td nowrap>$no</td>";
                                echo "<td nowrap>$nlink</td>";
                                echo "<td nowrap>$njumlah</td>";
                                echo "<td >$nketerangan</td>";
                                echo "</tr>";
                                
                                $no++;
                                
                            }
                            
                            
                        echo "</tbody>";
                    echo "</table>";
                    
                }elseif ($nmtable=="hrd.kas") {
                    
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                        echo "<thead>";
                            echo "<tr>";
                                echo "<th width='10px'>No</th>";
                                echo "<th width='80px'>ID</th>";
                                echo "<th width='50px'>Jumlah</th>";
                                echo "<th width='100px'>Keterangan</th>";
                            echo "</tr>";
                        echo "</thead>";
                        
                        echo "<tbody class='gridview-error'>";
                            $no=1;
                            $query = "select a.kasId as brid, a.nama as nama_dokter, "
                                    . " a.jumlah, a.aktivitas1 as keterangan from hrd.kas as a JOIN "
                                    . " dbmaster.t_suratdana_br1 as c on a.kasId=c.bridinput WHERE c.idinput='$pidinput'";
                            $query .=" Order by a.kasId";
                            $tampil = mysqli_query($cnmy, $query);
                            while ($nr=mysqli_fetch_array($tampil)){
                                $nbrid=$nr['brid'];
                                $njumlah=$nr['jumlah'];
                                $nketerangan=$nr['keterangan'];
                                $ndokternm=$nr['nama_dokter'];
                                $nnoslip="";
                                $njumlah=number_format($njumlah,0,",",",");
                                
                                $nnmrealisasi="";
                                $nnmdokter=$ndokternm;
                                $nnmket=$nketerangan;
                                
                                if (!empty($nnmrealisasi)) $nnmrealisasi=strip_tags_content($nnmrealisasi);
                                if (!empty($nnmrealisasi)) $nnmrealisasi = preg_replace("/[\\n\\r]+/", "", $nnmrealisasi);
                                
                                if (!empty($nnmdokter)) $nnmdokter=strip_tags_content($nnmdokter);
                                if (!empty($nnmdokter)) $nnmdokter = preg_replace("/[\\n\\r]+/", "", $nnmdokter);
                                
                                if (!empty($nnmket)) $nnmket=strip_tags_content($nnmket);
                                if (!empty($nnmket)) $nnmket = preg_replace("/[\\n\\r]+/", "", $nnmket);
                            
                            
                                $nlink="<a class='btn btn-dark btn-xs' data-dismiss='modal' href='#' "
                                        . " onClick=\"getDataModalBrInput('$_POST[udata1]', '$_POST[udata2]', '$_POST[udata3]', '$_POST[udata4]', '$_POST[udata5]', '$nbrid', '$nnoslip', '$nnmrealisasi', '$nnmdokter', '$nnmket')\">"
                                        . " $nbrid</a>";
                                
                                echo "<tr>";
                                echo "<td nowrap>$no</td>";
                                echo "<td nowrap>$nlink</td>";
                                echo "<td nowrap>$njumlah</td>";
                                echo "<td >$nketerangan</td>";
                                echo "</tr>";
                                
                                $no++;
                                
                            }
                            
                            
                        echo "</tbody>";
                    echo "</table>";
                    
                }elseif ($nmtable=="dbmaster.t_spg_gaji_br0") {
                }elseif ($nmtable=="dbmaster.t_kendaraan") {
                }elseif ($nmtable=="dbmaster.t_sewa") {
                }elseif ($nmtable=="dbmaster.t_kasbon") {
                }elseif ($nmtable=="dbmaster.t_kaskecilcabang") {
                    
                    echo "<table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>";
                        echo "<thead>";
                            echo "<tr>";
                                echo "<th width='10px'>No</th>";
                                echo "<th width='80px'>ID</th>";
                                echo "<th width='50px'>Jumlah</th>";
                                echo "<th width='100px'>Keterangan</th>";
                            echo "</tr>";
                        echo "</thead>";
                        
                        echo "<tbody class='gridview-error'>";
                            $no=1;
                            $query = "select a.idkascab as brid, "
                                    . " a.jumlah, a.keterangan as keterangan from dbmaster.t_kaskecilcabang as a JOIN "
                                    . " dbmaster.t_suratdana_br1 as c on a.idkascab=c.bridinput WHERE c.idinput='$pidinput'";
                            $query .=" Order by a.idkascab";
                            $tampil = mysqli_query($cnmy, $query);
                            while ($nr=mysqli_fetch_array($tampil)){
                                $nbrid=$nr['brid'];
                                $njumlah=$nr['jumlah'];
                                $nketerangan=$nr['keterangan'];
                                $nnoslip="";
                                $njumlah=number_format($njumlah,0,",",",");
                                
                                $nnmrealisasi="";
                                $nnmdokter="";
                                $nnmket=$nketerangan;
                                
                                if (!empty($nnmrealisasi)) $nnmrealisasi=strip_tags_content($nnmrealisasi);
                                if (!empty($nnmrealisasi)) $nnmrealisasi = preg_replace("/[\\n\\r]+/", "", $nnmrealisasi);
                                
                                if (!empty($nnmdokter)) $nnmdokter=strip_tags_content($nnmdokter);
                                if (!empty($nnmdokter)) $nnmdokter = preg_replace("/[\\n\\r]+/", "", $nnmdokter);
                                
                                if (!empty($nnmket)) $nnmket=strip_tags_content($nnmket);
                                if (!empty($nnmket)) $nnmket = preg_replace("/[\\n\\r]+/", "", $nnmket);
                            
                            
                                $nlink="<a class='btn btn-dark btn-xs' data-dismiss='modal' href='#' "
                                        . " onClick=\"getDataModalBrInput('$_POST[udata1]', '$_POST[udata2]', '$_POST[udata3]', '$_POST[udata4]', '$_POST[udata5]', '$nbrid', '$nnoslip', '$nnmrealisasi', '$nnmdokter', '$nnmket')\">"
                                        . " $nbrid</a>";
                                
                                echo "<tr>";
                                echo "<td nowrap>$no</td>";
                                echo "<td nowrap>$nlink</td>";
                                echo "<td nowrap>$njumlah</td>";
                                echo "<td >$nketerangan</td>";
                                echo "</tr>";
                                
                                $no++;
                                
                            }
                            
                            
                        echo "</tbody>";
                    echo "</table>";
                    
                }
                
                
                mysqli_close($cnmy);
                
            }elseif ($pmodule=="xxx") {
            }elseif ($pmodule=="xxx") {
                
            }
            ?>

            </div>

            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
    
<?PHP
function strip_tags_content($text) {
    return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
}
?>