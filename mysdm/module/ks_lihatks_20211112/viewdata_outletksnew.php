<?php
    session_start();
    include "../../config/koneksimysqli_ms.php";

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    $pidcab=$_POST['uidcab'];
    $pidarea=$_POST['uidarea'];
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpotlksnewmap01_".$puserid."_$now ";
    
    $query = "SELECT a.approve as approvepraktek, a.id as idpraktek, a.outletId as idoutlet, b.nama as nama_outlet, b.alamat,  
        b.jenis, b.type, c.Nama as nama_type, b.dispensing, 
        d.iCustId as icustid, d.iCabangId as icabangid, e.nama as nama_cabang, d.areaId as areaid, f.Nama as nama_area, 
        a.iddokter, g.namalengkap as nama_dokter, g.spesialis, h.nama as nama_spesialis  
        FROM ms2.tempatpraktek as a 
        JOIN ms2.outlet_master as b on a.outletId=b.id 
        LEFT JOIN ms2.outlet_type as c on b.type=c.id 
        JOIN ms2.outlet_customer as d on a.outletId=d.outletId 
        LEFT JOIN mkt.icabang as e on d.iCabangId=e.iCabangId 
        LEFT JOIN mkt.iarea as f on d.iCabangId=f.iCabangId and d.areaId=f.areaId 
        JOIN ms2.masterdokter as g on a.iddokter=g.id 
        LEFT JOIN ms2.lookup as h on g.spesialis=h.id 
        WHERE d.icabangid='$pidcab' ";
    if (!empty($pidarea)) {
        $query .=" AND d.areaid='$pidarea' ";
    }
    
    $query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    
?>

    <table id='datatable' class='table table-striped table-bordered' width='100%'>
        <thead>
            <tr>
                <th width='5px'>No</th>
                <th width='50px'></th>
                <th width='20px'>Area</th>
                <th width='40px'>Outlet</th>
                <th width='15px'>Nama Type</th>
                <th width='10px'>Dispensing</th>
                <th width='50px'>Alamat</th>
                <th width='30px'>User</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $no=1;
            $query = "select * from $tmp01 order by nama_area, nama_outlet";
            $tampil= mysqli_query($cnms, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pnareaid=$row['areaid'];
                $pnareanm=$row['nama_area'];
                $pnotlid=$row['idoutlet'];
                $pnotlnm=$row['nama_outlet'];
                $pntypeotl=$row['nama_type'];
                $pndispensing=$row['dispensing'];
                $pnalamatotl=$row['alamat'];
                $pniddokt=$row['iddokter'];
                $pnnmdokt=$row['nama_dokter'];
                
                $pnamadokterid="";
                if (!empty($pnnmdokt)) $pnamadokterid="$pnnmdokt ($pniddokt)";
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap></td>";
                echo "<td nowrap>$pnareanm</td>";
                echo "<td nowrap>$pnotlnm ($pnotlid)</td>";
                echo "<td nowrap>$pntypeotl</td>";
                echo "<td nowrap>$pndispensing</td>";
                echo "<td nowrap>$pnalamatotl</td>";
                echo "<td nowrap>$pnamadokterid</td>";
                echo "</tr>";
                
                
                $no++;
            }
            ?>
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            var dataTable = $('#datatable').DataTable( {
                "stateSave": true,
                fixedHeader: true,
                "ordering": false,
                "processing": true,
                //"order": [[ 0, "asc" ], [ 1, "desc" ], [ 2, "desc" ], [ 3, "desc" ]],
                "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
                "displayLength": 10,
                "columnDefs": [
                    { "visible": false },
                    { "orderable": false, "targets": 0 },
                    { "orderable": false, "targets": 1 },
                    { "orderable": false, "targets": 2 },
                    { "orderable": false, "targets": 3 },
                    { "orderable": false, "targets": 4 },
                    { "orderable": false, "targets": 5 },
                    //{ className: "text-right", "targets": [10,12,13,14,15] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3,4,5,6,7,8] }//nowrap

                ],
                "language": {
                    "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
                }
            } );
            $('div.dataTables_filter input', dataTable.table().container()).focus();
        } );

    </script>

    
<?php
hapusdata:
    mysqli_close($cnms);
?>