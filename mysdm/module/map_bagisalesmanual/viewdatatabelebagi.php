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

    $pidesct=$_POST['ucstid'];
    
    
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
    
    $query = "SELECT nama FROM MKT.ecabang WHERE distid='$piddist' AND ecabangid='$pidecab'";
    $tampil=mysqli_query($cnms, $query);
    $row=mysqli_fetch_array($tampil);
    $pnamaecabang=$row['nama'];
    
    if (!empty($pidesct)) {
        $pecusid=$pidesct;
    }else{
        $query = "SELECT DISTINCT a.custid FROM MKT.$pnmtblsales as a WHERE a.cabangid='$pidecab' AND a.fakturid='$pnmfilter' AND LEFT(a.tgljual,7)='$pbulan' ";
    
        $tampil=mysqli_query($cnms, $query);
        $row=mysqli_fetch_array($tampil);
        $pecusid=$row['custid'];
    }

    $query = "SELECT nama, icabangid, areaid, icustid, nama_eth_sks FROM MKT.ecust WHERE distid='$piddist' AND cabangid='$pidecab' AND ecustid='$pecusid'";
    $tampil=mysqli_query($cnms, $query);
    $row=mysqli_fetch_array($tampil);
    $pnmecust=$row['nama'];
    $pnmethsks=$row['nama_eth_sks'];
    $icabangid_map=$row['icabangid'];
    $areaid_map=$row['areaid'];
    $icustid_map=$row['icustid'];

    if ( ($piddist=="0000000031" OR $piddist=="31") AND !empty($pnmethsks) ) {
        $pnmecust=$pnmethsks;
    }
    
    if (!empty($pnmecust)) $pnmecust = str_replace('"', ' ', $pnmecust);
    if (!empty($pnmecust)) $pnmecust = str_replace('*', ' ', $pnmecust);
    



    $query = "SELECT nama FROM MKT.icust WHERE icabangid='$icabangid_map' AND areaid='$areaid_map' AND icustid='$icustid_map'";
    $tampil=mysqli_query($cnms, $query);
    $row=mysqli_fetch_array($tampil);
    $pnmicustsdm=$row['nama'];
    if (!empty($pnmicustsdm)) $pnmicustsdm = str_replace('"', ' ', $pnmicustsdm);
    if (!empty($pnmicustsdm)) $pnmicustsdm = str_replace('*', ' ', $pnmicustsdm);
    
    
        if (!empty($pnmecust)) $pnmecust=strip_tags_content($pnmecust);
        if (!empty($pnmicustsdm)) $pnmicustsdm = preg_replace("/[\\n\\r]+/", "", $pnmicustsdm);
                            
                            
    $query = "SELECT icabang.nama as nmcab, iarea.nama as nmarea FROM MKT.icabang JOIN MKT.iarea ON icabang.icabangid=iarea.icabangid WHERE icabang.icabangid='$icabangid_map' AND iarea.areaid='$areaid_map'";
    $tampil=mysqli_query($cnms, $query);
    $row=mysqli_fetch_array($tampil);
    $pnmcabang=$row['nmcab'];
    $pnmarea=$row['nmarea'];
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpslsmapcust01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpslsmapcust02_".$puserid."_$now ";
    
    
    $query = "SELECT a.cabangid, a.brgid, a.custid, a.tgljual, a.harga, a.fakturid, "
            . " e.iprodid, e.nama as nmprod, SUM(a.qbeli) qbeli "
            . " FROM MKT.$pnmtblsales as a "
            . " JOIN MKT.eproduk as e ON a.brgid=e.eprodid  WHERE a.cabangid='$pidecab' "
            . " AND LEFT(tgljual,7)='$pbulan' AND a.fakturid='$pnmfilter' AND e.distid='$piddist' "
            . " ";
    if (!empty($pidesct)) {
        $query .= " AND a.custid='$pidesct' ";
    }
    $query .= " GROUP BY 1,2,3,4,5,6,7,8 ORDER BY nmprod";
    //echo "$query";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.nomsales, a.distid, ecabangid, a.ecustid, a.icabangid, b.areaid, a.icustid, c.nama as nmicust, "
            . " a.fakturid, a.tgl, b.iprodid, b.qty, b.src, d.nama as nama_cabang, e.nama as nama_area "
            . " from MKT.msales0 as a LEFT "
            . " JOIN MKT.msales1 as b on a.nomsales=b.nomsales "
            . " LEFT JOIN MKT.icust as c on a.icabangid=c.icabangid AND b.areaid=c.areaid AND a.icustid=c.icustid "
            . " LEFT JOIN MKT.icabang as d on a.icabangid=d.icabangid "
            . " LEFT JOIN MKT.iarea as e on a.icabangid=e.icabangid AND b.areaid=e.areaid WHERE "
            . " a.distid='$piddist' and a.ecabangid='$pidecab' and a.fakturid='$pnmfilter' AND left(a.tgl,7)='$pbulan'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "Alter table $tmp01 ADD COLUMN qtysdhsplt DECIMAL(20,2)"; 
    mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN (select fakturid, iprodid, sum(qty) as qtysp from $tmp02 GROUP BY 1,2) as b on a.iprodid=b.iprodid AND a.fakturid=b.fakturid SET a.qtysdhsplt=b.qtysp"; 
    mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //echo "$pnamadist, $pnmtblsales, $pnamaecabang, $pecusid - $pnmecust - $icabangid_map ($pnmcabang) - $areaid_map ($pnmarea), $icustid_map - $pnmicustsdm";
?>

    <div class='x_content'>
        <table>
            <?PHP
            if ($pecusid<>'') {
            ?>
            <tr><td nowrap>Faktur</td><td> : </td><td nowrap><?PHP echo "$pnmfilter"; ?></td></tr>
            <?PHP
            }
            ?>
            <tr><td nowrap>Nama Cust Distributor</td><td> : </td><td nowrap><?PHP echo "$pnamadist"; ?></td></tr>
            <tr><td nowrap>Kode Cust Distributor</td><td> : </td><td nowrap><?PHP echo "$pecusid"; ?></td></tr>
            <?PHP
            if ($icustid_map<>'') {
            ?>
                <tr><td nowrap colspan="3" style="font-weight:bold;">Sudah di map ke : </td></tr>
                <tr><td nowrap>- Nama Customer SDM</td><td> : </td><td nowrap><?PHP echo "$pnmicustsdm"; ?></td></tr>
                <tr><td nowrap>- Kode Customer SDM</td><td> : </td><td nowrap><?PHP echo "$icustid_map"; ?></td></tr>
                <tr><td nowrap>- Cabang SDM</td><td> : </td><td nowrap><?PHP echo "$pnmcabang"; ?></td></tr>
                <tr><td nowrap>- Area SDM</td><td> : </td><td nowrap><?PHP echo "$pnmarea"; ?></td></tr>
            <?PHP
            }
            ?>
        </table>
        *) <span style="color:red;">Setelah di klik tombol <b><u>Bagi Sales</u></b> Silakan scroll kepaling bawah...!!!</span>
        <hr/>
        <?PHP
        $queryf1 = "select * from $tmp02";
        $tampilf1= mysqli_query($cnms, $queryf1);
        $ketemuf1=mysqli_num_rows($tampilf1);
        if ((INT)$ketemuf1>0) {   
            $queryf = "select distinct tgljual from $tmp01";
            $tampilf= mysqli_query($cnms, $queryf);
            $rowf=mysqli_fetch_array($tampilf);
            $itglf=$rowf['tgljual'];
            $pbthapusbagi="<input type='button' value='Hapus Bagi Sesuai Faktur' class='btn btn-danger btn-xs' onClick=\"HapusDataSudahSpltFaktur('$piddist', '$pidecab', '$pbulan', '$pnmfilter', '$itglf', '$pecusid')\">";
            echo $pbthapusbagi;
        }
        ?>
        <table id='datatablecust' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='100px'>Nama Produk</th>
                    <th width='50px'>Qty. Faktur</th>
                    <th width='50px'>Qty. Splitted</th>
                    <th width='50px'>Qty. Available</th>
                    <th width='50px'></th>
                    <th width='50px'>Customer</th>
                    <th width='50px'>Cabang / Area</th>
                    <th width='50px'>Qty</th>
                    <th width='50px'></th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $query = "select * from $tmp01 order by nmprod";
                $tampil= mysqli_query($cnms, $query);
                while ($row=mysqli_fetch_array($tampil)) {
                    $pbrgid=$row['brgid'];
                    $pidprod=$row['iprodid'];
                    $pnamaprod=$row['nmprod'];
                    $pqty=$row['qbeli'];
                    $ptgljual=$row['tgljual'];
                    $pqtysplte=$row['qtysdhsplt'];
                    
                    /*
                    $query = "select sum(qty) as qtysp from $tmp02 WHERE iprodid='$pidprod'";
                    $tampil2= mysqli_query($cnms, $query);
                    $row2=mysqli_fetch_array($tampil2);
                    $pqtysplte=$row2['qtysp'];
                    */
                    
                    //$pqtysplte=-2;
                    
                    $psisa=(DOUBLE)$pqty-(DOUBLE)$pqtysplte;
                    
                    $pbtnmaping="";
                    $pbthapusbagi="";
                    if ((DOUBLE)$psisa==0) {
                    }else{
                        $pbtnmaping="<input type='button' value='Bagi Sales' class='btn btn-success btn-xs' onClick=\"TampilkanDataBagiSales('$piddist', '$pidecab', '$pbulan', '$pnmfilter', '$pbrgid', '$pidprod', '$ptgljual', '$pqty', '$pqtysplte', '$psisa', '$pnmtblsales', '$pecusid', '$pnmecust', '$icabangid_map', '$areaid_map', '$icustid_map', '$pnmicustsdm')\">";
                    }
                    if ((DOUBLE)$pqtysplte<>0) {
                        $pbthapusbagi="<input type='button' value='Hapus Produk' class='btn btn-danger btn-xs' onClick=\"HapusDataSudahSpltProd('$piddist', '$pidecab', '$pbulan', '$pnmfilter', '$pbrgid', '$pidprod', '$ptgljual', '$pecusid')\">";
                    }
                    
                    
                    $pqty=number_format($pqty,0,",",",");
                    $pqtysplte=number_format($pqtysplte,0,",",",");
                    $psisa=number_format($psisa,0,",",",");
                    
                    echo "<tr>";
                    echo "<td nowrap>$pnamaprod</td>";
                    echo "<td nowrap align='right'>$pqty</td>";
                    echo "<td nowrap align='right'>$pqtysplte</td>";
                    echo "<td nowrap align='right'>$psisa</td>";
                    echo "<td nowrap >$pbtnmaping &nbsp; $pbthapusbagi</td>";
                    
                    $ppilihdetail=false;
                    $query = "select * from $tmp02 WHERE iprodid='$pidprod'";
                    $tampil2= mysqli_query($cnms, $query);
                    $ketemu2=mysqli_num_rows($tampil2);
                    if ((INT)$ketemu2>0) {
                        while ($row2=mysqli_fetch_array($tampil2)) {
                            $pdkode=$row2['nomsales'];
                            $pdicustid=$row2['icustid'];
                            $pdicustnm=$row2['nmicust'];
                            $pdqtysplte=$row2['qty'];
                            
                            $pidcab_d=(INT)$row2['icabangid'];
                            $pnmcab_d=$row2['nama_cabang'];
                            $pidarea_d=(INT)$row2['areaid'];
                            $pnmarea_d=$row2['nama_area'];
                            
                            $pdqtysplte=number_format($pdqtysplte,0,",",",");
                            
                            $pbtnhapussplt="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"HapusDataSudahSplt('$pdkode', '$pnmfilter', '$pidprod')\">";
                            
                            if ($ppilihdetail==true) {
                                echo "<tr>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap ></td>";
                            }
                            
                            echo "<td nowrap >$pdicustnm ($pdicustid)</td>";
                            echo "<td nowrap >$pnmcab_d ($pidcab_d) / $pnmarea_d ($pidarea_d)</td>";
                            echo "<td nowrap align='right'>$pdqtysplte</td>";
                            echo "<td nowrap >$pbtnhapussplt</td>";
                            echo "</tr>";
                            $ppilihdetail=true;
                        }
                    }else{
                        
                        echo "<td nowrap ></td>";
                        echo "<td nowrap ></td>";
                        echo "<td nowrap ></td>";
                        echo "<td nowrap ></td>";
                        echo "</tr>";
                    }
                    
                }
                ?>
            </tbody>
        </table>
    </div>

<style>
    .divnone {
        display: none;
    }
    #datatablecust th {
        font-size: 13px;
    }
    #datatablecust td { 
        font-size: 11px;
    }
</style>

<script>
    function TampilkanDataBagiSales(idist, iecab, ibln, ifaktur, ibrg, iprod, itgljual, iqtyfaktur, iqtysplit, iqtysisa, inmsales, iecustid, inmecust, icabmap, iareamap, iicustmap, iicustnmmap) {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");
        
        if (inmsales=="") {
            alert("Nama Tabel Sales Tidak ada...");
            return false;
        }
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/map_bagisalesmanual/viewdatatabeleformbagi.php?module="+module+"&idmenu="+idmenu+"&act="+act,
            data:"udistid="+idist+"&ucabid="+iecab+"&ubln="+ibln+"&unamafilter="+ifaktur+"&ubrg="+ibrg+"&uproduk="+iprod+
                    "&utgljual="+itgljual+"&uqtyfaktur="+iqtyfaktur+"&uqtysplit="+iqtysplit+"&uqtysisa="+iqtysisa+"&unmsales="+inmsales+
                    "&uecustid="+iecustid+"&unmecust="+inmecust+
                    "&ucabmap="+icabmap+"&uareamap="+iareamap+"&uicustmap="+iicustmap+"&uicustnmmap="+iicustnmmap,
            success:function(data){
                $("#c-databagi").html(data);
                $("#loading2").html("");
                //window.scrollTo(0,document.body.scrollHeight);
                window.scrollTo(0,document.querySelector("#c-databagi").scrollHeight);
            }
        });
    }
    
    function HapusDataSudahSplt(skode, ifaktur, iproduk) {
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
            url:"module/map_bagisalesmanual/simpandatasplit.php?module="+module+"&act=hapusdatasplit",
            data:"ukode="+skode+"&uproduk="+iproduk+"&ufakturid="+ifaktur,
            success:function(data){
                var istatus=data.trim();
                if (istatus=="berhasil") {
                    disp_datamapingbyfaktur("2", ifaktur);
                }else{
                    alert(data);
                }
                
            }
        });
        
    }
    
    function HapusDataSudahSpltProd(idist, iecab, ibln, ifaktur, ibrg, iprod, itgljual, iecustid) {
        if (idist=="") {
            alert("Tidak ada data yang akan dihapus...");
            return false;
        }
        if (ifaktur=="") {
            alert("Tidak ada data faktur yang akan dihapus...");
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
            url:"module/map_bagisalesmanual/simpandatasplit.php?module="+module+"&act=hapusdatasplitprodfaktur",
            data:"udistid="+idist+"&ucabid="+iecab+"&ubln="+ibln+"&ufakturid="+ifaktur+"&ubrg="+ibrg+"&uproduk="+iprod+
                    "&utgljual="+itgljual+"&uecustid="+iecustid,
            success:function(data){
                var istatus=data.trim();
                if (istatus=="berhasil") {
                    disp_datamapingbyfaktur("2", ifaktur);
                }else{
                    alert(data);
                }
                
            }
        });
        
    }
    
    function HapusDataSudahSpltFaktur(idist, iecab, ibln, ifaktur, itgljual, iecustid) {
        if (idist=="") {
            alert("Tidak ada data yang akan dihapus...");
            return false;
        }
        if (ifaktur=="") {
            alert("Tidak ada data faktur yang akan dihapus...");
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
            url:"module/map_bagisalesmanual/simpandatasplit.php?module="+module+"&act=hapusdatasplitfaktur",
            data:"udistid="+idist+"&ucabid="+iecab+"&ubln="+ibln+"&ufakturid="+ifaktur+
                    "&utgljual="+itgljual+"&uecustid="+iecustid,
            success:function(data){
                var istatus=data.trim();
                if (istatus=="berhasil") {
                    disp_datamapingbyfaktur("2", ifaktur);
                }else{
                    alert(data);
                }
                
            }
        });
        
    }
</script>

<?PHP
function strip_tags_content($text) {
    return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
}

hapusdata:
    mysqli_query($cnms, "drop TEMPORARY table $tmp01");
    mysqli_query($cnms, "drop TEMPORARY table $tmp02");
    
    mysqli_close($cnms);
?>
