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
    
    $pkaryawanid_pl=$_POST['ukry'];
    $pdate1=$_POST['uperiode1'];
    $pdate2=$_POST['uperiode2'];
    $ptgl1= date("Y-m-d", strtotime($pdate1));
    $ptgl2= date("Y-m-d", strtotime($pdate2));
    
    
    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_ubahget_id.php";
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpdtabsinp01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpdtabsinp02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpdtabsinp03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmpdtabsinp04_".$puserid."_$now ";
    
    $query = "select a.idabsen, a.tglinput, a.kode_absen, c.nama_absen, a.karyawanid, b.nama as nama_karyawan, "
            . " a.tanggal, a.jam, a.l_latitude, a.l_longitude, a.l_status, a.l_radius, a.id_status, "
            . " a.keterangan from hrd.t_absen as a "
            . " JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId "
            . " LEFT JOIN hrd.t_absen_kode as c on a.kode_absen=c.kode_absen WHERE tanggal BETWEEN '$ptgl1' AND '$ptgl2' ";
    if (!empty($pkaryawanid_pl)) $query .=" AND a.karyawanid='$pkaryawanid_pl' ";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
?>


<form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='hrd_isidtabsen' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='70px'></th>
                    <th width='40px'>Nama Karyawan</th>
                    <th width='40px'>Tanggal</th>
                    <th width='80px'>Jam</th>
                    <th width='30px'>Status</th>
                    <th width='30px'>Lokasi</th>
                    <th width='30px'>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                
                $query = "SELECT * FROM $tmp01 ";
                $query .=" ORDER BY tanggal DESC, nama_karyawan, karyawanid, jam, kode_absen";
                $no=1;
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pidabsen=$row['idabsen'];
                    $nnmkry=$row['nama_karyawan'];
                    $ntgl=$row['tanggal'];
                    $njam=$row['jam'];
                    $nnmabsen=$row['nama_absen'];
                    $nstatus=$row['l_status'];
                    $nket=$row['keterangan'];
                    
                    
                    $ntgl= date("d-m-Y", strtotime($ntgl));
                    
                    $pidnoget=encodeString($pidabsen);
                    $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidnoget'>Edit</a>";
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pedit</td>";
                    echo "<td nowrap>$nnmkry</td>";
                    echo "<td nowrap>$ntgl</td>";
                    echo "<td nowrap>$njam</td>";
                    echo "<td nowrap>$nnmabsen</td>";
                    echo "<td nowrap>$nstatus</td>";
                    echo "<td >$nket</td>";
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
        var dataTable = $('#hrd_isidtabsen').DataTable( {
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
                //{ className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1,2,3,4,5,6] }//nowrap

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
                
                document.getElementById("d-form2").action = "module/hrd/hrd_isidataabsen/aksi_lokasi.php?module="+module+"&act=hapus&idmenu="+idmenu+"&kethapus="+txt+"&ket="+ket+"&id="+noid;
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
    #hrd_isidtabsen th {
        font-size: 13px;
    }
    #hrd_isidtabsen td { 
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