<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>

<?php
    session_start();
    include "../../config/koneksimysqli.php";
    $cnit=$cnmy;
    $pidklaim=$_POST['uid'];
    $pidkar=$_POST['uidkry'];
    $piddokt=$_POST['uiddr'];
    $pblnpl=$_POST['ubln'];
    $paptid=$_POST['uaptid'];
    $pbln = date('Y', strtotime($pblnpl));
    $pplbulan = date('Y-m', strtotime($pblnpl));
    
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpinptksdrusr00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpinptksdrusr01_".$puserid."_$now ";
    
    
    //$query ="select iprodid as iprodid, nama as nama, hna as hna, aktif from MKT.iproduk where IFNULL(aktif,'') <> 'N' order by nama";
    $query ="select iprodid as iprodid, nama as nama, hna as hna, aktif from MKT.iprodukh where insentif='Y' and IFNULL(aktif,'') <> 'N' and tahun='$pbln' order by nama";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "select a.srid as srid, a.bulan as bulan, "
            . " a.dokterid as dokterid, "
            . " a.aptid as aptid, a.apttype as apttype, "
            . " a.iprodid as iprodid, "
            . " a.qty as qty, a.hna as hna, ifnull(a.qty,0)*ifnull(a.hna,0) as tvalue, a.cn_ks1 as cn_ks1, a.approved as approved "
            . " FROM hrd.ks1 as a WHERE a.dokterid='$piddokt' AND a.srid='$pidkar' AND a.bulan='$pplbulan' AND a.aptid='$paptid'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

    
    $query = "ALTER TABLE $tmp00 ADD COLUMN qty DECIMAL(20,2), ADD COLUMN tvalue DECIMAL(20,2)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }    
    
    $query = "UPDATE $tmp00 as a JOIN $tmp01 as b on a.iprodid=b.iprodid SET a.qty=b.qty, a.hna=b.hna, a.tvalue=b.tvalue WHERE IFNULL(b.qty,0)<>0";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }    
    
    $query = "DELETE FROM $tmp01 WHERE IFNULL(iprodid,'') IN (select distinct IFNULL(iprodid,'') FROM $tmp00)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "INSERT INTO $tmp00 (iprodid, nama, hna, aktif, qty, tvalue) "
            . " SELECT DISTINCT a.iprodid, b.nama, a.hna, b.aktif, a.qty, a.tvalue FROM $tmp01 as a JOIN MKT.iproduk as b on a.iprodid=b.iprodid";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
?>

<div class='tbldata'>
    <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
        <thead>
            <tr>
                <th width='2%px' class='divnone'></th>
                <th width='5%px'>No</th>
                <th width='30%' >Produk</th>
                <th width='5%' align="right">Qty</th>
                <th width='5%' align="right">Hna</th>
                <th width='5%' align="right">Jumlah</th>
            </tr>
        </thead>
        <tbody class='inputdatauc'>
            <?PHP
            $no=1;
            $query = "select * from $tmp00 order by nama";
            $tampil=mysqli_query($cnit, $query);
            while ($nrow= mysqli_fetch_array($tampil)){
                $pkodeidbr=$nrow['iprodid'];
                $pnmproduk=$nrow['nama'];
                $phna=$nrow['hna'];
                $pqty=$nrow['qty'];
                $pjumlah=$nrow['tvalue'];
                
                $pfldqty="<input type='text' size='10px' id='e_txtqty[$pkodeidbr]' name='e_txtqty[$pkodeidbr]' onblur=\"HitungJumlahRp('e_txtqty[$pkodeidbr]', 'e_txthna[$pkodeidbr]', 'e_txtjml[$pkodeidbr]')\" class='input-sm inputmaskrp2' autocomplete='off' value='$pqty'>";
                $pfldhna="<input type='text' size='10px' id='e_txthna[$pkodeidbr]' name='e_txthna[$pkodeidbr]' onblur=\"HitungJumlahRp('e_txtqty[$pkodeidbr]', 'e_txthna[$pkodeidbr]', 'e_txtjml[$pkodeidbr]')\" class='input-sm inputmaskrp2' autocomplete='off' value='$phna' Readonly>";
                $pfldjml="<input type='text' size='10px' id='e_txtjml[$pkodeidbr]' name='e_txtjml[$pkodeidbr]' onblur='HitungTotalJumlahRp()' class='input-sm inputmaskrp2' autocomplete='off' value='$pjumlah' Readonly>";
                
                
                
                $chkbox = "<input type='checkbox' id='chk_kodeid[$pkodeidbr]' name='chk_kodeid[]' value='$pkodeidbr' checked>";
                
                echo "<tr>";
                echo "<td nowrap class='divnone'>$chkbox</td>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$pnmproduk</td>";
                echo "<td nowrap>$pfldqty</td>";
                echo "<td nowrap>$pfldhna</td>";
                echo "<td nowrap>$pfldjml</td>";
                echo "</tr>";
                
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>


<?PHP
mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp00");
mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
?>
    
    

<script>
    
    function HitungJumlahRp(iqty, ihna, ijml) {
        var aqty=document.getElementById(iqty).value;
        var ahna=document.getElementById(ihna).value;
        var newchar = '';
        
        if (aqty=="") aqty="0";
        aqty = aqty.split(',').join(newchar);
        
        if (ahna=="") ahna="0";
        ahna = ahna.split(',').join(newchar);
        
        var nTotal_="0";
        nTotal_ =parseFloat(aqty)*parseFloat(ahna);
        
        document.getElementById(ijml).value=nTotal_;
        
        HitungTotalJumlahRp();
    }
    
    function HitungTotalJumlahRp() {
        var chk_arr1 =  document.getElementsByName('chk_kodeid[]');
        var chklength1 = chk_arr1.length;
        var newchar = '';

        var nTotal_="0";
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                
                var kata = chk_arr1[k].value;
                var fields = kata.split('-');    
                var anm_jml="e_txtjml["+fields[0]+"]";
                var ajml=document.getElementById(anm_jml).value;
                if (ajml=="") ajml="0";
                ajml = ajml.split(',').join(newchar);

                nTotal_ =parseFloat(nTotal_)+parseFloat(ajml);
                
                
            }
        }
        
        document.getElementById('e_total').value=nTotal_;
        document.getElementById('e_total2').value=nTotal_;
    }
</script>