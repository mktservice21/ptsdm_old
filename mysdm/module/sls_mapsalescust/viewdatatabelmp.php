<?PHP
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
    
    $_SESSION['SLSMPCUSTTGL01']=$_POST['uperiode1'];
    $_SESSION['SLSMPCUSTTGL02']=$_POST['uperiode2'];
    
    
    $psescardidid=$_SESSION['IDCARD'];
    $pgroupid=$_SESSION['GROUP'];
    $pidjbtpl=$_SESSION['JABATANID'];
    

    
    include "../../config/koneksimysqli_ms.php";
    
    
    
    $picabidfil="";
    if ($pidjbtpl=="38" || (DOUBLE)$pidjbtpl==38) {
        $pcabangid="";
        $query = "select distinct karyawanid as karyawanid, icabangid as icabangid from hrd.rsm_auth where karyawanid='$psescardidid'";
        $tampil= mysqli_query($cnms, $query);
        while ($nro= mysqli_fetch_array($tampil)) {
            $pncab=$nro['icabangid'];
            $picabidfil .="'".$pncab."',";
        }
        
        if (!empty($picabidfil)) {
            $picabidfil="(".substr($picabidfil, 0, -1).")";
        }else{
            $picabidfil="('nnzznnnn')";
        }
    }
    
    
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $tgl2= date("Y-m-t", strtotime($date2));
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPSLSMAPCUSTAR01_".$puserid."_$now ";
    
    $query = "SELECT
        DISTINCT 
        s.icabangid as icabangid,
        c.nama as namacabang,
        s.areaid as areaid,
        ar.nama as namaarea,
        s.fakturid as fakturid,
        s.icustid as icustid, 
        ic.nama AS namacustomer
        FROM
        sls.mr_sales2 s
        LEFT JOIN sls.icust ic
        ON s.icustid = ic.iCustId
        LEFT JOIN sls.iproduk ip
        ON s.iprodid = ip.iprodid
        LEFT JOIN sls.icabang c
        ON s.icabangid = c.icabangid
        LEFT JOIN sls.iarea ar
        ON s.icabangid = ar.icabangid
        AND s.areaid = ar.areaid
        WHERE s.tgljual BETWEEN '$tgl1'
        AND '$tgl2'
        AND s.icabangid NOT IN (30,31) ";
    
    if (!empty($picabidfil)) {
        $query .=" AND s.icabangid IN $picabidfil";
    }
    $query .=" HAVING IFNULL(namacustomer,'')=''";
    $query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
?>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' 
      id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    
    
    <div class='x_content'>
        
        <table id='datatablempslscust' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='5px'>No</th>
                    <th width='50px'></th>
                    <th width='50px'>Cabang</th>
                    <th width='20px'>Area</th>
                    <th width='20px'>Faktur ID</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp01 ORDER BY namacabang, namaarea, namacustomer";
                $tampil=mysqli_query($cnms, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $picabangid=$row['icabangid'];
                    $picabangnm=$row['namacabang'];
                    $paraid=$row['areaid'];
                    $pareanm=$row['namaarea'];
                    $pidcust=$row['icustid'];
                    $pnmcust=$row['namacustomer'];
                    
                    $pfakturid=$row['fakturid'];
                    
                    $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&ixn=$pfakturid&cbb=$picabangid&iax=$paraid&ics=$pidcust&yd1=$tgl1&yd2=$tgl2'>Edit</a>";
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pedit</td>";
                    echo "<td nowrap>$picabangnm</td>";
                    echo "<td nowrap>$pareanm</td>";
                    echo "<td nowrap>$pfakturid</td>";
                    //echo "<td nowrap>$pidcust</td>";
                    //echo "<td nowrap>$pnmcust</td>";
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
        var dataTable = $('#datatablempslscust').DataTable( {
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
                //{ "orderable": false, "targets": 2 },
                //{ "orderable": false, "targets": 3 },
                //{ className: "text-right", "targets": [10,12,13,14,15] },//right
                { className: "text-nowrap", "targets": [0, 1, 2,3] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            }
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    function ProsesData(ket, noid){

        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
            if (r==true) {

                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/pch_purchaseorder/aksi_purchaseorder.php?module="+module+"&idmenu="+idmenu+"&act="+ket+"&kethapus="+"&ket="+ket+"&id="+noid;
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
    #datatablempslscust th {
        font-size: 13px;
    }
    #datatablempslscust td { 
        font-size: 11px;
    }
</style>


<?PHP
hapusdata:
    mysqli_query($cnms, "drop TEMPORARY table $tmp01");
    
    mysqli_close($cnms);
?>