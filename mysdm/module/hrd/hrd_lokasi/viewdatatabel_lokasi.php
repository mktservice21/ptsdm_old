<?php
    date_default_timezone_set('Asia/Jakarta'); 
    session_start(); 
    
    //ini_set('display_errors', '0');
    //ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    $pidcard=$_SESSION['IDCARD'];
    $pidgroup=$_SESSION['GROUP'];
    $pidjabatan=$_SESSION['JABATANID'];
    
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_ubahget_id.php";
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmplkshrddt01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmplkshrddt02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmplkshrddt03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmplkshrddt04_".$puserid."_$now ";
    
    $query = "select karyawanid, aktif, id_status, a_latitude, a_longitude, a_radius from hrd.karyawan_absen WHERE IFNULL(aktif,'')='Y'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER TABLE $tmp01 ADD COLUMN itabel VARCHAR(15) DEFAULT '', ADD COLUMN id INT(4) DEFAULT '0', ADD COLUMN nama_karyawan VARCHAR(200), ADD COLUMN e_hilang VARCHAR(1) DEFAULT 'N'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 SET itabel='LOKASIWFH'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "INSERT INTO $tmp01 (itabel, karyawanid, aktif, id_status, a_latitude, a_longitude, a_radius) select DISTINCT "
            . " 'LOKASISDMEX' as itabel, a.karyawanid, 'Y' as aktif, a.id_status, b.sdm_latitude, b.sdm_longitude, a.sdm_radius "
            . " from hrd.sdm_lokasi_radius_ex as a, hrd.sdm_lokasi as b WHERE a.id_status=b.id_status";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "INSERT INTO $tmp01 (itabel, id, aktif, id_status, a_latitude, a_longitude, a_radius) select "
            . " 'SDMLOKASI' as itabel, id, 'Y' as aktif, id_status, sdm_latitude, sdm_longitude, sdm_radius from hrd.sdm_lokasi";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId SET a.nama_karyawan=b.nama";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp01 as a JOIN hrd.sdm_lokasi_radius_ex as b on a.karyawanid=b.karyawanId SET a.e_hilang='Y'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
?>


<form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='datatablertnotcho' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='70px'></th>
                    <th width='40px'>Nama</th>
                    <th width='40px'>&nbsp;</th>
                    <th width='80px'>Aktif</th>
                    <th width='30px'>Latitude</th>
                    <th width='30px'>Longitude</th>
                    <th width='30px'>Radius</th>
                    <th width='30px'>Maps</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $query = "SELECT * FROM $tmp01 ";
                $query .=" ORDER BY id DESC, nama_karyawan, karyawanid";
                $no=1;
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pidsdm=$row['id'];
                    $pkaryawanid=$row['karyawanid'];
                    $pnmkaryawan=$row['nama_karyawan'];
                    $paktif=$row['aktif'];
                    $pidsts=$row['id_status'];
                    $plat=$row['a_latitude'];
                    $plong=$row['a_longitude'];
                    $pradius=$row['a_radius'];
                    $pststabel=$row['itabel'];
                    $philang=$row['e_hilang'];
                    
                    $pedit="";
                    
                    $lokasistatus="";
                    if ($pststabel=="SDMLOKASI" AND $pidsts=="HO1") {
                        $pnmkaryawan="<b>LOKASI SDM (HO JKT)</b>";
                        $lokasistatus="<b>SDM HO JKT</b>";
                        
                        $pststat=encodeString('sdmholok');//HO / CAB
                        $pidnoget=encodeString($pidsdm);
                        $pids_=encodeString($pidsts);
                        $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdatalsdm&idmenu=$pidmenu&nmun=$pidmenu&id=$pidnoget&s=$pids_&n=$pststat'>Edit Radius</a>";
                        
                    }elseif ($pststabel=="SDMLOKASI") {
                        $pnmkaryawan="<b>LOKASI SDM (CABANG)</b>";
                        $lokasistatus="<b>$pidsts</b>";
                        
                        $pststat=encodeString('sdmcablok');//HO / CAB
                        $pidnoget=encodeString($pidsdm);
                        $pids_=encodeString($pidsts);
                        $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdatalsdm&idmenu=$pidmenu&nmun=$pidmenu&id=$pidnoget&s=$pids_&n=$pststat'>Edit Radius</a>";
                        
                    }
                    
                    if ($pststabel=="LOKASISDMEX") {
                        $lokasistatus ="(exception lokasi sdm)";
                        
                            $pidkry_=encodeString($pkaryawanid);
                            $pidsts_=encodeString($pidsts);
                            $pidakt_=encodeString($paktif);
                            $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdataexpsdmkry&idmenu=$pidmenu&nmun=$pidmenu&id=$pidkry_&s=$pidakt_&n=$pidsts_'>Edit Radius</a>";
                    }elseif ($pststabel=="LOKASIWFH") {
                        $lokasistatus ="(lokasi wfh)";
                        if ($philang=="Y") {
                        }else{
                            $pidkry_=encodeString($pkaryawanid);
                            $pidsts_=encodeString($pidsts);
                            $pidakt_=encodeString($paktif);
                            $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdatawfh&idmenu=$pidmenu&nmun=$pidmenu&id=$pidkry_&s=$pidakt_&n=$pidsts_'>Edit Radius</a>";
                        }
                    }
                    
                    $plihatpeta="<button type='button' class='tombol-simpan btn-xs btn-dark' id='ibuttontampil' onclick=\"ShowIframeMapsPerson('$plat', '$plong')\">Preview Maps</button>";
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pedit</td>";
                    echo "<td nowrap>$pnmkaryawan</td>";
                    echo "<td nowrap>$lokasistatus</td>";
                    echo "<td nowrap>$paktif</td>";
                    echo "<td nowrap>$plat</td>";
                    echo "<td nowrap>$plong</td>";
                    echo "<td nowrap>$pradius</td>";
                    echo "<td nowrap>$plihatpeta</td>";
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
        var dataTable = $('#datatablertnotcho').DataTable( {
            //"stateSave": true,
            //"order": [[ 2, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 1 },
                { "orderable": false, "targets": 2 },
                { "orderable": false, "targets": 3 },
                { "orderable": false, "targets": 4 },
                { "orderable": false, "targets": 5 },
                { "orderable": false, "targets": 6 },
                { "orderable": false, "targets": 7 },
                //{ className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1,2,3,4,5,6,7] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            //"scrollY": 460,
            "scrollX": true
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    

    function ProsesDataHapus(ket, noid){
        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
            if (r==true) {

                var txt;
                if (ket=="reject" || ket=="hapus" || ket=="pending") {
                    var textket = prompt("Masukan alasan "+ket+" : ", "");
                    if (textket == null || textket == "") {
                        txt = textket;
                    } else {
                        txt = textket;
                    }
                }


                //document.write("You pressed OK!")
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                
                document.getElementById("d-form2").action = "module/hrd/hrd_lokasi/aksi_lokasi.php?module="+module+"&act=hapus&idmenu="+idmenu+"&kethapus="+txt+"&ket="+ket+"&id="+noid;
                document.getElementById("d-form2").submit();
                return 1;
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
    #datatablertnotcho th {
        font-size: 13px;
    }
    #datatablertnotcho td { 
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
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp04");
    
    mysqli_close($cnmy);
?>