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
    
    $pidcard=$_SESSION['USERID'];
    
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
    $tmp02 =" dbtemp.tmpslsmapcustf02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpslsmapcustf03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmpslsmapcustf04_".$puserid."_$now ";
    
    $query = "select distinct a.fakturid, a.tgljual, a.cabangid, a.custid, b.nama, b.nama_eth_sks "
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
    
    $query = "SELECT DISTINCT '$piddist' as distid, a.cabangid, a.fakturid, e.iprodid, a.custid, LEFT(a.tgljual,7) as bulan "
            . " FROM MKT.$pnmtblsales as a "
            . " JOIN MKT.eproduk as e ON a.brgid=e.eprodid  WHERE LEFT(tgljual,7)='$pbulan' AND e.distid='$piddist'";
    //echo "$query";
    $query = "create TEMPORARY table $tmp02($query)"; 
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.nomsales, a.distid, a.ecabangid, a.fakturid, a.iprodid, a.ecustid, LEFT(a.tgl,7) as bulan, a.tgl, a.qty, a.user1, c.nama as nama_user, b.nama as nama_produk "
            . " from mkt.msales_new as a LEFT JOIN mkt.iproduk as b on a.iprodid=b.iprodid "
            . " LEFT JOIN hrd.karyawan as c on LPAD(a.user1,10,'0')=c.karyawanId "
            . " WHERE LEFT(tgl,7)='$pbulan' AND distid='$piddist' AND (a.user1='$puserid' OR a.user1='$pidcard')";
    $query = "create TEMPORARY table $tmp03($query)"; 
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select * from $tmp03 where concat(distId, eCabangId, fakturId, iProdId) NOT IN "
            . " (select IFNULL(concat(IFNULL(distId,''), IFNULL(CabangId,''), IFNULL(fakturId,''), IFNULL(iProdId,'')),'') FROM $tmp02)";
    $query = "create TEMPORARY table $tmp04($query)"; 
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
?>

    <div class='x_content'>
        <?PHP
        
        $query = "SELECT * from $tmp04";
        $tampilf= mysqli_query($cnms, $query);
        $ketemuf=mysqli_num_rows($tampilf);
        if ((INT)$ketemuf>0) {
            echo "<br/><b><u>Ada Mapping yang tidak sesuai data sales</u></b><br/>";
            echo "<table  id='datatablecustfkt2' class='table table-striped table-bordered' width='100%'>";
                echo "<thead>";
                echo "<tr>";
                    echo "<th></th>";
                    echo "<th>ID</th><th>Faktur</th><th>Produk</th><th>QTY</th><th>User</th>";
                echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while ($frow= mysqli_fetch_array($tampilf)) {
                $f_id=$frow['nomsales'];
                $f_faktur=$frow['fakturid'];
                $f_idprod=$frow['iprodid'];
                $f_nmprod=$frow['nama_produk'];
                $f_qty=$frow['qty'];
                $f_userid=$frow['user1'];
                $f_usernm=$frow['nama_user'];
                $f_bln=$frow['bulan'];
                $f_tgl=$frow['tgl'];
                
                $pbtnhapussalah="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"HapusDataSalahMapping('$piddist', '$f_tgl', '$f_id', '$f_faktur', '$f_idprod', '$f_userid')\">";
                
                echo "<tr>";
                echo "<td nowrap>$pbtnhapussalah</td>";
                echo "<td nowrap>$f_id</td>";
                echo "<td nowrap>$f_faktur</td>";
                echo "<td nowrap>$f_nmprod</td>";
                echo "<td nowrap align='right'>$f_qty</td>";
                echo "<td nowrap>$f_usernm</td>";
                echo "</tr>";
                
            }
            echo "</tbody>";
            echo "</table>";
            
            echo "<br/><br/>";
        }
        
        ?>
        
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
                    $pnmethsks=$row['nama_eth_sks'];
                    $pidecust=$row['custid'];
                    $ptgljual=$row['tgljual'];

                    if ( ($piddist=="0000000031" OR $piddist=="31") AND !empty($pnmethsks) ) {
                        $pnamaecust=$pnmethsks;
                    }
                    
                    //if (!empty($pnamaecust)) $pnamaecust = str_replace('"', ' ', $pnamaecust);
                    //if (!empty($pnamaecust)) $pnamaecust = str_replace('*', ' ', $pnamaecust);
                    
                    $pbtnfakturid="<input type='button' value='$pidfaktur' class='btn btn-warning btn-xs' onClick=\"disp_datamapingbyfaktur('1', '$pidfaktur', '$pidecust')\">";
                    
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
    
    function disp_datamapingbyfaktur(sKey, enamafilter, ecstid) {
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
            data:"udistid="+edistid+"&ucabid="+ecabid+"&unamafilter="+enamafilter+"&ubln="+ebln+"&ucstid="+ecstid,
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
    
    
    function HapusDataSalahMapping(sdisid, stgl, skode, ifaktur, iproduk, idusr) {
        if (skode=="") {
            alert("Tidak ada data yang akan dihapus...");
            return false;
        }
        
        var cmt = confirm('Apakah akan hapus data... ???');
        if (cmt == false) {
            return false;
        }
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");
        
        $.ajax({
            type:"post",
            url:"module/map_bagisalesmanual/aksi_bagisalesmanual.php?module="+module+"&act=hapusdatasalahmapinguser",
            data:"ukode="+skode+"&uproduk="+iproduk+"&ufakturid="+ifaktur+"&uidusr="+idusr+"&udistid="+sdisid+"&utgljual="+stgl,
            success:function(data){
                var istatus=data.trim();
                if (istatus=="berhasil") {
                    disp_viewdata();
                }else{
                    alert(data);
                }
                
            }
        });
    }
</script>

<?PHP
hapusdata:
    mysqli_query($cnms, "drop TEMPORARY table $tmp01");
    mysqli_query($cnms, "drop TEMPORARY table $tmp02");
    mysqli_query($cnms, "drop TEMPORARY table $tmp03");
    mysqli_query($cnms, "drop TEMPORARY table $tmp04");
    
    mysqli_close($cnms);
?>
