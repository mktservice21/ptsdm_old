<?php
session_start();
$pmodule="dasd";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="viewdataprpo"){
    
    ?>
    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    <script src="js/inputmask.js"></script>
    <?PHP
    
    include "../../../config/koneksimysqli.php";
    
    $pact=$_POST['uact'];
    $pidinput=$_POST['uidinput'];
    $pkdsup=$_POST['usupp'];
    
    $pidgroup=$_SESSION['GROUP'];
    $userid=$_SESSION['IDCARD'];
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.TMPPOCDT00_".$userid."_$now ";
    $tmp01 =" dbtemp.TMPPOCDT01_".$userid."_$now ";
    
    
    $query = "select distinct a.idpr_po from dbpurchasing.t_po_transaksi_d as a "
            . " JOIN dbpurchasing.t_po_transaksi as b on a.idpo=b.idpo "
            . " WHERE IFNULL(b.stsnonaktif,'')<>'Y' AND a.idpo='$pidinput'";
    $query = "create TEMPORARY table $tmp00 ($query)"; 
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select d.idpo, d.idpr_po as sudahpo, a.idpr, a.idpr_d, a.idpr_po, c.karyawanid, 
            a.kdsupp, b.NAMA_SUP as nama_sup, b.ALAMAT as alamat, b.TELP as telp, 
            a.idbarang, a.namabarang, 
            a.idbarang_d, a.spesifikasi1, a.spesifikasi2, 
            a.uraian, a.keterangan, 
            a.jumlah, a.satuan, a.harga, a.disc, a.discrp, a.ppn, a.ppnrp, a.totalrp, a.aktif, a.userid 
            from dbpurchasing.t_pr_transaksi_po as a 
            JOIN dbmaster.t_supplier as b on a.kdsupp=b.KDSUPP
            JOIN dbpurchasing.t_pr_transaksi as c on a.idpr=c.idpr 
            LEFT JOIN (select distinct aa.idpo, aa.idpr_po from dbpurchasing.t_po_transaksi_d as aa 
            JOIN dbpurchasing.t_po_transaksi as bb on aa.idpo=bb.idpo WHERE IFNULL(bb.stsnonaktif,'')<>'Y') as d 
            on d.idpr_po=a.idpr_po WHERE IFNULL(c.stsnonaktif,'')<>'Y' AND 
            IFNULL(a.kdsupp,'')='$pkdsup' AND IFNULL(a.aktif,'')='Y' order by a.aktif, b.NAMA_SUP";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    if ($pact=="editdata") {
        $query = "DELETE FROM $tmp01 WHERE idpo<>'$pidinput' AND IFNULL(sudahpo,'0') <> '0' AND IFNULL(sudahpo,'') <> ''"; 
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "delete $tmp01 from $tmp01 "
                . " JOIN dbpurchasing.t_pr_transaksi_po as b "
                . " on $tmp01.idpr=b.idpr AND $tmp01.idpr_d=b.idpr_d "
                . " join dbpurchasing.t_po_transaksi_d as c "
                . " on b.idpr_po=c.idpr_po "
                . " join dbpurchasing.t_po_transaksi as d on c.idpo=d.idpo "
                . " WHERE IFNULL(d.stsnonaktif,'')<>'Y' AND d.idpo <> '$pidinput'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }else{
        $query = "DELETE FROM $tmp01 WHERE IFNULL(sudahpo,'0') <> '0' AND IFNULL(sudahpo,'') <> ''"; 
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp01 SET totalrp=IFNULL(jumlah,0)*IFNULL(harga,0)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "delete $tmp01 from $tmp01 "
                . " JOIN dbpurchasing.t_pr_transaksi_po as b "
                . " on $tmp01.idpr=b.idpr AND $tmp01.idpr_d=b.idpr_d "
                . " join dbpurchasing.t_po_transaksi_d as c "
                . " on b.idpr_po=c.idpr_po "
                . " join dbpurchasing.t_po_transaksi as d on c.idpo=d.idpo "
                . " WHERE IFNULL(d.stsnonaktif,'')<>'Y'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
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
                        <input type="checkbox" id="chkall[]" name="chkall[]" onclick="SelAllCheckBox('chkall[]', 'chk_detail[]')" value='select' >
                    </th>
                    <th width='30px'>ID PR</th>
                    <th width='30px'>Barang / Spesifikasi</th>
                    <th width='50px'>Jumlah</th>
                    <th width='10px'>satuan</th>
                    <th width='50px'>Harga</th>
                    <th width='50px'>Disc. (%)</th>
                    <th width='50px'>Total Rp.</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp01 order by namabarang";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pidprpo=$row['idpr_po'];
                    $pidpr=$row['idpr'];
                    $pidpr_d=$row['idpr_d'];
                    $pkdsup=$row['kdsupp'];
                    $pnmsup=$row['nama_sup'];
                    $palamatsup=$row['alamat'];
                    $ptlpsup=$row['telp'];
                    $psatuan=$row['satuan'];
                    $psts=$row['aktif'];
                    $psudahidpo=$row['sudahpo'];
                    
                    if ((DOUBLE)$psudahidpo==0) $psudahidpo="";
                    
                    $pstsaktif="Ya";
                    if ($psts=="N") $pstsaktif="Tidak";

                    $pidbrg=$row['idbarang'];
                    $pidbrg2=$row['idbarang_d'];
                    $pnmbarang=$row['namabarang'];
                    $pspesifikasi=$row['spesifikasi1'];
                    $pketerangan=$row['keterangan'];

                    $pjml=$row['jumlah'];
                    $pharga=$row['harga'];
                    $pdisc=$row['disc'];
                    $pdiscrp=$row['discrp'];
                    $ptotalrp=$row['totalrp'];

                    //$pjml=number_format($pjml,0,",",",");

                    $pedit="<a class='btn btn-warning btn-xs' href='?module=pchisivendorpr&act=editisivendor&idmenu=372&nmun=372&id=$pidpr&xid=$pidpr_d&nid=$pidprpo'>Edit</a>";
                    $pedit="";
                    
                    $pfldidpr="<input type='hidden' value='$pidpr' size='10px' id='e_txtpr[$pidprpo]' name='e_txtpr[$pidprpo]' class='input-sm' autocomplete='off'>";
                    $pfldidprd="<input type='hidden' value='$pidpr_d' size='10px' id='e_txtprd[$pidprpo]' name='e_txtprd[$pidprpo]' class='input-sm' autocomplete='off'>";
                    $pfldidbrg="<input type='hidden' value='$pidbrg' size='10px' id='e_txtidbrg[$pidprpo]' name='e_txtidbrg[$pidprpo]' class='input-sm' autocomplete='off'>";
                    $pfldidbrgd="<input type='hidden' value='$pidbrg2' size='10px' id='e_txtidbrgd[$pidprpo]' name='e_txtidbrgd[$pidprpo]' class='input-sm' autocomplete='off'>";
                    $pfldnmbrg="<input type='hidden' value='$pnmbarang' size='10px' id='e_txtnmbrg[$pidprpo]' name='e_txtnmbrg[$pidprpo]' class='input-sm' autocomplete='off'>";
                    $pfldspcbrg="<span hidden><textarea id='e_txtspcbrg[$pidprpo]' name='e_txtspcbrg[$pidprpo]'>$pspesifikasi</textarea></span>";
                    
                    $pfldsatuan="<input type='text' value='$psatuan' size='10px' id='e_txtsatuan[$pidprpo]' name='e_txtsatuan[$pidprpo]' class='input-sm' oninput=\"this.value = this.value.toUpperCase()\">";
                    
                    $ptifledtotal="'$pidprpo', 'e_txtjml[$pidprpo]', 'e_txtharga[$pidprpo]', 'e_txtdisc[$pidprpo]', 'e_txtdiscrp[$pidprpo]', 'e_txtjmltot[$pidprpo]'";
                    
                    $pfldjumlah="<input type='text' value='$pjml' size='10px' id='e_txtjml[$pidprpo]' name='e_txtjml[$pidprpo]' onblur=\"HitungTotalSatuanBrg($ptifledtotal)\" class='input-sm inputmaskrp2 txtright' autocomplete='off'>";
                    $pfldharga="<input type='text' value='$pharga' size='10px' id='e_txtharga[$pidprpo]' name='e_txtharga[$pidprpo]' onblur=\"HitungTotalSatuanBrg($ptifledtotal)\" class='input-sm inputmaskrp2 txtright' autocomplete='off'>";
                    $pflddisc="<input type='text' value='$pdisc' size='10px' id='e_txtdisc[$pidprpo]' name='e_txtdisc[$pidprpo]' onblur=\"HitungTotalSatuanBrg($ptifledtotal)\" class='input-sm inputmaskrp2 txtright' autocomplete='off'>";
                    $pflddiscrp="<input type='hidden' value='$pdiscrp' size='10px' id='e_txtdiscrp[$pidprpo]' name='e_txtdiscrp[$pidprpo]' onblur=\"HitungTotalSatuanBrg($ptifledtotal)\" class='input-sm inputmaskrp2 txtright' autocomplete='off' Readonly>";
                    $pfldtotjml="<input type='text' value='$ptotalrp' size='10px' id='e_txtjmltot[$pidprpo]' name='e_txtjmltot[$pidprpo]' onblur=\"HitungTotalSatuanBrg($ptifledtotal)\" class='input-sm inputmaskrp2 txtright' autocomplete='off'>";
                    
                    $ncheck_sudah="";
                    if (!empty($psudahidpo)) $ncheck_sudah="checked";
                    $pchkbox = "<input type='checkbox' id='chk_detail[$pidprpo]' name='chk_detail[]' value='$pidprpo' onclick=\"CentangCekBoxDataBR('$pidprpo', 'chk_detail[$pidprpo]')\" $ncheck_sudah>";
                    
                    
                    echo "<tr>";

                    echo "<td nowrap>$no $pfldidpr $pfldidprd $pfldidbrg $pfldidbrgd $pfldnmbrg $pfldspcbrg</td>";
                    echo "<td nowrap>$pchkbox $pedit</td>";
                    echo "<td nowrap>$pidpr</td>";
                    echo "<td ><b><u>$pnmbarang</u></b><br/>$pspesifikasi</td>";
                    echo "<td nowrap align='right'>$pfldjumlah</td>";
                    echo "<td nowrap >$pfldsatuan</td>";
                    echo "<td nowrap align='right'>$pfldharga</td>";
                    echo "<td nowrap align='right'>$pflddisc $pflddiscrp</td>";
                    echo "<td nowrap align='right'>$pfldtotjml</td>";

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
            HitungTotalDariCekBox();
        }
            
            
        function CentangCekBoxDataBR(nidbr, nmchk) {
            HitungTotalDariCekBox();
        }
            
        function HitungTotalDariCekBox() {

            var chk_arr1 =  document.getElementsByName('chk_detail[]');
            var chklength1 = chk_arr1.length;
            var newchar = '';

            var nTotal_="0";
            for(k=0;k< chklength1;k++)
            {   

                if (chk_arr1[k].checked == true) {
                    var kata = chk_arr1[k].value;
                    var fields = kata.split('-');    
                    var anm_jml="e_txtjmltot["+fields[0]+"]";
                    var ajml=document.getElementById(anm_jml).value;
                    if (ajml=="") ajml="0";
                    ajml = ajml.split(',').join(newchar);

                    nTotal_ =parseFloat(nTotal_)+parseFloat(ajml);
                }
            }

            document.getElementById('e_jmlusulan').value=nTotal_;
            HitungJumlahUsulan();
            //HitungPPN();
            //HitungDiscount();
            //HitungPembualatan();
        }
         
//$ptifledtotal="'$pidprpo', 'e_txtjml[$pidprpo]', 'e_txtharga[$pidprpo]', 'e_txtdisc[$pidprpo]', 'e_txtdiscrp[$pidprpo]', 'e_txtjmltot[$pidprpo]'";         
        function HitungTotalSatuanBrg(iid, txtjml, txtharga, txtdisc, txtdiscrp, txtjmltot) {
            var newchar = '';
            var ijml = document.getElementById(txtjml).value;
            var iharga = document.getElementById(txtharga).value;
            var idisc = document.getElementById(txtdisc).value;
            
            if (ijml=="") ijml="0";
            if (iharga=="") iharga="0";
            if (idisc=="") idisc="0";
            
            ijml = ijml.split(',').join(newchar);
            iharga = iharga.split(',').join(newchar);
            idisc = idisc.split(',').join(newchar);
            
            var itotal="0";
            itotal=parseFloat(ijml)*parseFloat(iharga);
            
            
            var idiscrp="0";
            idiscrp=parseFloat(itotal)*parseFloat(idisc)/100;
            document.getElementById(txtdiscrp).value=idiscrp;
            
            var itotplusdisc="0";
            itotplusdisc=parseFloat(itotal)-parseFloat(idiscrp);
            
            
            document.getElementById(txtjmltot).value=itotplusdisc;
            
            HitungTotalDariCekBox();
            
            
        }
    </script>
        
<?PHP
    
    
    hapusdata:
        mysqli_query($cnmy, "drop TEMPORARY table $tmp00");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
        mysqli_close($cnmy);
    
}

?>