<?php
    session_start();
    include "../../config/koneksimysqli.php";
    
    $pidklaim=$_POST['uid'];
    $pthn=$_POST['uthn'];
    $psemester=$_POST['usem'];
    
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpinptdplchc00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpinptdplchc01_".$puserid."_$now ";
    
    
    $query ="SELECT c.GRP_FKIDEN as grpprod, d.GRP_NAMESS as grpname, b.iprodid AS iprodid, b.nama AS nama_produk, b.DivProdId as divisi  
        FROM MKT.iproduk b 
        JOIN MKT.T_OTC_GRPPRD_DETAIL c ON b.iprodid=c.GRP_IDPROD
        JOIN MKT.T_OTC_GRPPRD d ON c.GRP_FKIDEN = d.GRP_IDENTS
        WHERE b.DivProdId='OTC' 
        ORDER BY d.GRP_NAMESS, c.GRP_FKIDEN";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select igroup, nodpl, iprodid, beli_min, beli_max, discount, keterangan from dbdiscount.t_dpl WHERE igroup in 
            (select max(igroup) from dbdiscount.t_dpl as b WHERE dbdiscount.t_dpl.iprodid=b.iprodid) 
            AND tahun='$pthn' AND semester='$psemester'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "ALTER TABLE $tmp00 ADD COLUMN beli_min2 INT(4), ADD COLUMN beli_max2 INT(4), ADD COLUMN discount2 DECIMAL(20,2), ADD COLUMN keterangan2 VARCHAR(500), ADD COLUMN nodpl2 VARCHAR(50)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp00 as a JOIN $tmp01 as b on a.iprodid=b.iprodid SET a.beli_min2=b.beli_min, a.beli_max2=b.beli_max, a.discount2=b.discount, a.keterangan2=b.keterangan, a.nodpl2=b.nodpl";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
?>

<div class='tbldata'>
    <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
        <thead>
            <tr>
                <th width='2%px' class='divnone'></th>
                <th width='5%px'>No</th>
                <th width='20%' >Group</th>
                <th width='30%' >Produk</th>
                <th width='5%' align="right">Pembelian Minimal</th>
                <th width='5%' align="right">Pembelian Maksimal</th>
                <th width='5%' align="right">Discount (%)</th>
                
                <th width='5%' align="right">Beli Min. (Last)</th>
                <th width='5%' align="right">Beli Max. (Last)</th>
                <th width='5%' align="right">Disc. (%) (Last)</th>
                <th width='5%'>NoDPL (Last)</th>
                <th width='10%'>Ket. (Last)</th>
            </tr>
        </thead>
        <tbody class='inputdatauc'>
            <?PHP
            $no=1;
            $query = "select * from $tmp00 order by grpname, nama_produk";
            $tampil=mysqli_query($cnmy, $query);
            while ($nrow= mysqli_fetch_array($tampil)){
                
                $pkodeidbr=$nrow['iprodid'];
                $pnmproduk=$nrow['nama_produk'];
                $pidgprprod=$nrow['grpprod'];
                $pgprprod=$nrow['grpname'];
                
                $pminbeli=$nrow['grpname'];
                
                $pnodpl2=$nrow['nodpl2'];
                $pminbeli2=$nrow['beli_min2'];
                $pmaxbeli2=$nrow['beli_max2'];
                $pdisc2=$nrow['discount2'];
                $pket2=$nrow['keterangan2'];
                
                $pminbeli="";
                $pmaxbeli="";
                $pdisc="";
                
                $pfldmin="<input type='text' size='10px' id='e_txtbelimin[$pkodeidbr]' name='e_txtbelimin[$pkodeidbr]' onblur=\"\" class='input-sm inputmaskrp2' autocomplete='off' value='$pminbeli'>";
                $pfldmax="<input type='text' size='10px' id='e_txtbelimax[$pkodeidbr]' name='e_txtbelimax[$pkodeidbr]' onblur=\"\" class='input-sm inputmaskrp2' autocomplete='off' value='$pmaxbeli'>";
                $pflddisc="<input type='text' size='10px' id='e_txtdisc[$pkodeidbr]' name='e_txtdisc[$pkodeidbr]' onblur='HitungTotalJumlahRp()' class='input-sm inputmaskrp2' autocomplete='off' value='$pdisc'>";
                
                
                
                $chkbox = "<input type='checkbox' id='chk_kodeid[$pkodeidbr]' name='chk_kodeid[]' value='$pkodeidbr' checked>";
                
                echo "<tr>";
                echo "<td nowrap class='divnone'>$chkbox $pidgprprod</td>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$pgprprod</td>";
                echo "<td nowrap>$pnmproduk</td>";
                echo "<td nowrap>$pfldmin</td>";
                echo "<td nowrap>$pfldmax</td>";
                echo "<td nowrap>$pflddisc</td>";
                
                echo "<td nowrap align='right'>$pminbeli2</td>";
                echo "<td nowrap align='right'>$pmaxbeli2</td>";
                echo "<td nowrap align='right'>$pdisc2</td>";
                echo "<td nowrap>$pnodpl2</td>";
                echo "<td >$pket2</td>";
                
                echo "</tr>";
                
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>


<?PHP
mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
?>


<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>

<style>
    .form-group, .input-group, .control-label {
        margin-bottom:2px;
    }
    .control-label {
        font-size:11px;
    }
    #datatable input[type=text], #tabelnobr input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:11px;
        height: 25px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }

    table.datatable, table.tabelnobr {
        color: #000;
        font-family: Helvetica, Arial, sans-serif;
        width: 100%;
        border-collapse:
        collapse; border-spacing: 0;
        font-size: 11px;
        border: 0px solid #000;
    }

    table.datatable td, table.tabelnobr td {
        border: 1px solid #000; /* No more visible border */
        height: 10px;
        transition: all 0.1s;  /* Simple transition for hover effect */
    }

    table.datatable th, table.tabelnobr th {
        background: #DFDFDF;  /* Darken header a bit */
        font-weight: bold;
    }

    table.datatable td, table.tabelnobr td {
        background: #FAFAFA;
    }

    /* Cells in even rows (2,4,6...) are one color */
    tr:nth-child(even) td { background: #F1F1F1; }

    /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
    tr:nth-child(odd) td { background: #FEFEFE; }

    tr td:hover.biasa { background: #666; color: #FFF; }
    tr td:hover.left { background: #ccccff; color: #000; }

    tr td.center1, td.center2 { text-align: center; }

    tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
    tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
    /* Hover cell effect! */
    tr td {
        padding: -10px;
    }
    .divnone {
        display: none;
    }
</style>

<script>
    $(document).ready(function() {
        var dataTable = $('#datatable').DataTable( {
            "ordering": false,
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false
        } );
    });
</script>