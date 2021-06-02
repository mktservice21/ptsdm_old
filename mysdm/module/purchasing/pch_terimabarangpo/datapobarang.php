<?php
session_start();
$pmodule="dasd";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="viewdatapobarang"){
    
    ?>
    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    <script src="js/inputmask.js"></script>
    <?PHP
    
    include "../../../config/koneksimysqli.php";
    
    $pact=$_POST['uact'];
    $pidinput=$_POST['uidinput'];
    $pidpo=$_POST['upo'];
    
    if ($pidinput=="0") $pidinput="";
    
    
    $pidgroup=$_SESSION['GROUP'];
    $userid=$_SESSION['IDCARD'];
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmppotrk00_".$userid."_$now ";
    $tmp01 =" dbtemp.tmppotrk01_".$userid."_$now ";

    $query = "select d.divisi, a.idpo, a.idpo_d, a.idpr_po, b.kdsupp, c.idbarang, c.namabarang, "
            . " c.jumlah from dbpurchasing.t_po_transaksi_d as a "
            . " JOIN dbpurchasing.t_po_transaksi as b on a.idpo=b.idpo "
            . " JOIN dbpurchasing.t_pr_transaksi_po as c on a.idpr_po=c.idpr_po "
            . " JOIN dbpurchasing.t_pr_transaksi as d on c.idpr=d.idpr"
            . " WHERE a.idpo='$pidpo'";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER TABLE $tmp00 ADD COLUMN jmlsudahterima DECIMAL(20,0), ADD COLUMN jmlsisa DECIMAL(20,0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.* from dbpurchasing.t_po_transaksi_terima as a JOIN $tmp00 as b ON a.idpo_d=b.idpo_d WHERE "
            . " IFNULL(a.stsnonaktif,'')<>'Y'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp00 as a JOIN (select idpo_d, SUM(jml_terima) as jml_terima FROM $tmp01 GROUP BY 1) as b ON a.idpo_d=b.idpo_d SET "
            . " a.jmlsudahterima=b.jml_terima";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp00 SET jmlsisa=IFNULL(jumlah,0)-IFNULL(jmlsudahterima,0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $pchkallname="deselect";
    $pchkallpilih="checked";
    $query = "select * FROM $tmp00 WHERE IFNULL(jmlsisa,'0')='0'";
    $tampilk= mysqli_query($cnmy, $query);
    $ketemuk= mysqli_num_rows($tampilk);
    if ((INT)$ketemuk>0) {
        $pchkallpilih="";
        $pchkallname="select";
    }
    
?>

    
    
    <div hidden class='form-group'>
        &nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-danger btn-xs' onclick='HitungTotalDariCekBox()'>Hitung Jumlah</button> <span class='required'></span>
    </div>
    <div class='x_content'>
        
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='20px'>
                        <input type="checkbox" id="chkall[]" name="chkall[]" onclick="SelAllCheckBox('chkall[]', 'chk_detail[]')" value='<?PHP echo $pchkallname; ?>' <?PHP echo $pchkallpilih; ?> >
                    </th>
                    <th width='20px'>Divisi</th>
                    <th width='30px'>ID Barang</th>
                    <th width='30px'>Barang / Spesifikasi</th>
                    <th width='50px'>Jml. PO</th>
                    <th width='10px'>Jml. Sudah Terima</th>
                    <th width='50px'>Jml. Terima</th>
                    <th width='50px'>Sisa</th>
                    <th width='70px'>Keterangan Terima</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp00 ";
                if (empty($pidinput)) {
                    $query .=" ORDER BY IFNULL(jmlsisa,9999) desc, divisi, namabarang";
                }else{
                    $query .=" ORDER BY divisi, namabarang";
                }
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $piddiv=$row['divisi'];
                    $pidpod=$row['idpo_d'];
                    $pidbarang=$row['idbarang'];
                    $pnmbarang=$row['namabarang'];
                    $pjumlah=$row['jumlah'];
                    $pjmlsdh=$row['jmlsudahterima'];
                    $pjmlsisa=$row['jmlsisa'];
                    $pjmltrm=0;
                    
                    $pkettrm="";//$row['jmlsisa'];
                    
                    if (empty($pjmlsisa)) $pjmlsisa=0;
                    
                    $ncheck_sudah="checked";
                    if ((INT)$pjmlsisa==0 AND empty($pidinput)) {
                        $ncheck_sudah="";
                    }
                    $pchkbox = "<input type='checkbox' id='chk_detail[$pidpod]' name='chk_detail[]' value='$pidpod' onclick=\"CentangCekBoxDataBR('$pidpod', 'chk_detail[$pidpod]')\" $ncheck_sudah>";
                    
                    
                    if (empty($txt_jmlpo)) $txt_jmlpo=0;
                    if (empty($pjmlsdh)) $pjmlsdh=0;
                    if (empty($pjmlsisa)) $pjmlsisa=0;
                    if (empty($pjmltrm)) $pjmltrm=0;
                    
                    $nstyle_text=" style='text-align:right; background-color: transparent; border: 0px solid;' ";
                    $nstyle_right=" style='text-align:right;' ";
                    
                    $txt_idbrg="<input type='hidden' value='$pidbarang' size='10px' id='txtidbrg[$pidpod]' name='txtidbrg[$pidpod]' class='' autocomplete='off' Readonly $nstyle_text>";
                    $txt_iddivisi="<input type='hidden' value='$piddiv' size='10px' id='txtiddiv[$pidpod]' name='txtiddiv[$pidpod]' class='' autocomplete='off' Readonly $nstyle_text>";
                    $txt_jmlpo="<input type='text' value='$pjumlah' size='10px' id='txtjmlpo[$pidpod]' name='txtjmlpo[$pidpod]' class='inputmaskrp2' autocomplete='off' Readonly $nstyle_text>";
                    $txt_jmlsdhtrm="<input type='text' value='$pjmlsdh' size='10px' id='txtjmlsdh[$pidpod]' name='txtjmlsdh[$pidpod]' class='inputmaskrp2' autocomplete='off' Readonly $nstyle_text>";
                    $txt_jmltrm="<input type='text' onblur=\"HitungJumlahSisa('$pidpod', 'txtjmlpo[$pidpod]', 'txtjmlsdh[$pidpod]', 'txtjmltrm[$pidpod]', 'txtjmlsisa[$pidpod]')\" value='$pjmltrm' size='10px' "
                            . " id='txtjmltrm[$pidpod]' name='txtjmltrm[$pidpod]' class='inputmaskrp2' autocomplete='off' >";
                    $txt_jmlsisa="<input type='text' value='$pjmlsisa' size='10px' id='txtjmlsisa[$pidpod]' name='txtjmlsisa[$pidpod]' class='inputmaskrp2' autocomplete='off' Readonly $nstyle_text>";
                    
                    $txt_ketterima="<input type='text' value='$pkettrm' size='40px' id='txtkettrm[$pidpod]' name='txtkettrm[$pidpod]' class='' >";
                    
                    
                    $pjumlah=number_format($pjumlah,0,",",",");
                    $pjmlsdh=number_format($pjmlsdh,0,",",",");
                    
                    if (empty($pidbarang)) {
                        $pchkbox="";
                    }
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pchkbox</td>";
                    echo "<td nowrap>$piddiv $txt_iddivisi</td>";
                    echo "<td nowrap>$pidbarang $txt_idbrg </td>";
                    echo "<td nowrap>$pnmbarang</td>";
                    echo "<td nowrap align='right'>$txt_jmlpo</td>";
                    echo "<td nowrap align='right'>$txt_jmlsdhtrm</td>";
                    echo "<td nowrap align='right'>$txt_jmltrm</td>";
                    echo "<td nowrap align='right'>$txt_jmlsisa</td>";
                    echo "<td nowrap >$txt_ketterima</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
    
    
    <style>
        .divnone {
            display: none;
        }
        #datatablespggj th {
            font-size: 12px;
        }
        #datatablespggj td { 
            font-size: 11px;
        }
        .imgzoom:hover {
            -ms-transform: scale(3.5); /* IE 9 */
            -webkit-transform: scale(3.5); /* Safari 3-8 */
            transform: scale(3.5);

        }
        
        .txtright { text-align: right; }

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

    </style>
        
    <script>
        function SelAllCheckBox(nmbuton, data){
            var checkboxes = document.getElementsByName(data);
            var button = document.getElementById(nmbuton);
            if(button.value == 'select'){
                for (var i in checkboxes){
                    checkboxes[i].checked = 'FALSE';
                }
                button.value = 'deselect'
            }else{
                for (var i in checkboxes){
                    checkboxes[i].checked = '';
                }
                button.value = 'select';
            }
        }
        
        
        function HitungJumlahSisa(sidpo, stxtmlpo, stxtjmlsdh, stxttrm, stxtsisa) {
            var newchar = '';
            var ijmlpo = document.getElementById(stxtmlpo).value;
            var ijmlsdh = document.getElementById(stxtjmlsdh).value;
            var ijmltrm = document.getElementById(stxttrm).value;
            var ijmlsisa = document.getElementById(stxtsisa).value;
            
            if (ijmlpo=="") ijmlpo="0";
            if (ijmlsdh=="") ijmlsdh="0";
            if (ijmltrm=="") ijmltrm="0";
            if (ijmltrm=="") ijmltrm="0";
            
            ijmlpo = ijmlpo.split(',').join(newchar);
            ijmlsdh = ijmlsdh.split(',').join(newchar);
            ijmltrm = ijmltrm.split(',').join(newchar);
            ijmltrm = ijmltrm.split(',').join(newchar);
            
            var itotalsisa="0";
            itotalsisa=parseFloat(ijmlpo)-parseFloat(ijmltrm);
            
            document.getElementById(stxtsisa).value=itotalsisa;
        }
    </script>
        
<?PHP
    
    
    hapusdata:
        mysqli_query($cnmy, "drop TEMPORARY table $tmp00");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
        mysqli_close($cnmy);
    
}

?>