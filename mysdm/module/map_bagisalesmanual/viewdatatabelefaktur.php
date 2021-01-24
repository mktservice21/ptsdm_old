<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    
    $piddist=$_POST['udistid'];
    $pidecab=$_POST['ucabid'];
    $pnmfilter=$_POST['unamafilter'];
    $pbln=$_POST['ubln'];
    $pbulan = date('Y-m', strtotime($pbln));
    
    
    $_SESSION['MAPCUSTBAGIDCAB']=$piddist;
    $_SESSION['MAPCUSTBAGIIDARE']=$pidecab;
    $_SESSION['MAPCUSTBAGIFILTE']=$pnmfilter;
    $_SESSION['MAPCUSTBAGIBULAN']=$pbln;
    
    include "../../config/koneksimysqli_ms.php";
    
    $query = "SELECT distid, nama, sls_data, initial FROM MKT.distrib0 WHERE distid='$piddist'";
    $tampil=mysqli_query($cnms, $query);
    $row=mysqli_fetch_array($tampil);
    $pnamadist=$row['nama'];
    $pnmtblsales=$row['sls_data'];
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpslsmapcustf01_".$puserid."_$now ";
    
    $query = "select distinct a.fakturid, a.tgljual, a.cabangid, a.custid, b.nama "
            . " from MKT.$pnmtblsales as a "
            . " LEFT JOIN MKT.ecust as b on '$piddist'=b.distid "
            . " and a.custid=b.ecustid and a.cabangid=b.cabangid where "
            . " a.cabangid='$pidecab' and left(a.tgljual,7)='$pbulan'";
    if (!empty($pnmfilter)) {
        $query .=" AND a.fakturid LIKE '%$pnmfilter%' ";
    }
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>

    <div class='x_content'>
        <table id='datatablecustfkt' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th Nowrap>Faktur Id</th>
                    <th Nowrap>Nama</th>
                    <th Nowrap>Tgl. Jual</th>
                    <th Nowrap class='divnone'></th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $query = "select * from $tmp01 order by nama, tgljual, fakturid";
                $tampil= mysqli_query($cnms, $query);
                while ($row=mysqli_fetch_array($tampil)) {
                    $pidfaktur=$row['fakturid'];
                    $pnamaecust=$row['nama'];
                    $pidecust=$row['custid'];
                    $ptgljual=$row['tgljual'];
                    
                    $pbtnfakturid="<input type='button' value='$pidfaktur' class='btn btn-warning btn-xs' onClick=\"disp_datamapingbyfaktur('1', '$pidfaktur')\">";
                    
                    echo "<tr>";
                    echo "<td nowrap>$pbtnfakturid</td>";
                    echo "<td nowrap>$pnamaecust ($pidecust)</td>";
                    echo "<td nowrap >$ptgljual</td>";
                    echo "<td nowrap class='divnone'>$pidfaktur</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

<style>
    .divnone {
        display: none;
    }
    #datatablecustfkt th {
        font-size: 13px;
    }
    #datatablecustfkt td { 
        font-size: 11px;
    }
</style>

<script>
    $(document).ready(function() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        
        
        var dataTable = $('#datatablecustfkt').DataTable( {
            "processing": true,
            //"serverSide": true,
            //"stateSave": true,
            //"order": [[ 2, "asc" ], [ 3, "asc" ], [ 4, "asc" ]],
            "lengthMenu": [[2, 5, 10, 50, 100, 10000000], [2, 5, 10, 50, 100, "All"]],
            "displayLength": 2,
            "columnDefs": [
                { "visible": false },
                //{ className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            //"scrollY": 490,
            "scrollX": true
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    function disp_datamapingbyfaktur(sKey, enamafilter) {
        var edistid=document.getElementById('cb_dist').value;
        var ecabid=document.getElementById('cb_ecabang').value;
        var ebln=document.getElementById('e_bulan').value;

        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");

        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/map_bagisalesmanual/viewdatatabelebagi.php?module="+module+"&idmenu="+idmenu+"&act="+act,
            data:"udistid="+edistid+"&ucabid="+ecabid+"&unamafilter="+enamafilter+"&ubln="+ebln,
            success:function(data){
                $("#c-datamaping").html(data);
                $("#loading").html("");
                //if (sKey=="2") {
                //    window.scrollTo(0,document.querySelector("#c-databagi").scrollHeight);
                //}else{
                    $("#c-databagi").html("");
                    window.scrollTo(0,document.querySelector("#c-datamaping").scrollHeight);
                //}
            }
        });
    }
</script>

<?PHP
hapusdata:
    mysqli_query($cnms, "drop TEMPORARY table $tmp01");
    
    mysqli_close($cnms);
?>
