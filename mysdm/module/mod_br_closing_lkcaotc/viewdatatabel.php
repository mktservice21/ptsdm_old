<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>

<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }
</style>

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>


<?PHP
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    include "../../config/library.php";
    $cnit=$cnmy;
    $date1=$_POST['utgl'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $bulan= date("Ym", strtotime($date1));
    
    $tgl_sblmnya_ = date('F Y', strtotime('-1 month', strtotime($date1)));
    
    $_SESSION['CLSLKCA']=date("F Y", strtotime($date1));
    
    
    $tglnow = date("d/m/Y");
    $tgl01 = $_POST['utgl'];
    $periode1 = date("Y-m", strtotime($tgl01));
    $per1 = date("F Y", strtotime($tgl01));
    
    $stsreport = $_POST['usts'];
    
    include ("seleksi_data_lk_caotc.php");

?>


<form method='POST' action='<?PHP echo "?module=$_POST[module]&act=input&idmenu=$_POST[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_POST['module']; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_POST['idmenu']; ?>' Readonly>
    <input type='hidden' id='u_tgl1' name='u_tgl1' value='<?PHP echo $tgl1; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
    
    <div class='x_content'>
        
        <?PHP
            $chkall = "<input type='checkbox' id='pilihsemua' name='pilihsemua' value='select' onClick=\"SelAllCheckBox('pilihsemua', 'chkpilih[]')\"/>";
            if ($stsreport=="C" OR $stsreport=="S") $chkall="";
        ?>
        
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                <th width="30px" align="center" nowrap><?PHP echo $chkall; ?></th>
                <th align="center" nowrap>NAMA</th>
                <th align="center" nowrap>DIVISI</th>
                <th align="center" nowrap>No LK</th>
                <th align="center" nowrap>Credit</th>
                <th align="center" nowrap>Saldo REAL</th>
                <th align="center" nowrap>CA <?PHP echo $per1; ?></th>
                <th align="center" nowrap>Selisih</th>
                <th align="center" >CA  <?PHP echo $per2; ?></th>
                <th align="center" >AR / AP <?PHP echo $tgl_sblmnya_; ?></th>
                <th align="center" >JUML TRSF</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    
                    $gtotjumlah=0;
                    $gtotca1=0;
                    $gtotselisih=0;
                    $gtotca2=0;
                    $gtotadj=0;
                    $gtottrans=0;
                    $nourutpilih="";
                    
                    $query = "select distinct divisi, karyawanid, nama_karyawan, saldo, ca1, ca2, jml_adj from $tmp01 order by divisi, nama_karyawan, karyawanid";
                    $result = mysqli_query($cnit, $query);
                    $records = mysqli_num_rows($result);
                    
                    if ($records) {
                        $reco = 1;
                        while ($row = mysqli_fetch_array($result)) {
                            $pdivisi=$row['divisi'];
                            $pkaryawanid=$row['karyawanid'];
                            $pnmkaryawan=$row['nama_karyawan'];
                            $prprutin=number_format($row['saldo'],0,",",",");
                            $pca1=number_format($row['ca1'],0,",",",");
                            $pca2=number_format($row['ca2'],0,",",",");
                            $pjumlahadj=number_format($row['jml_adj'],0,",",",");
                            
                            $pselisih=$row['ca1']-$row['saldo'];
                            
                            
                            
                            //$pjmltrans= ( (double)$row['ca2']-(double)$pselisih );
                            //$pjumlahadj=0;
                            //if ($pselisih>0 AND $row['ca2']==0) $pjmltrans=0;
                            //elseif ($pselisih>0 AND $row['ca2']>0) $pjmltrans=$row['ca2'];
                            //elseif ($pselisih==0 AND $row['ca2']>0) $pjmltrans=$row['ca2'];
                            
                            $pjmltrans= ( (double)$row['ca2']-(double)$pselisih ) + (double)$row['jml_adj'];
                            //if ((double)$pjmltrans<0) $pjmltrans=0;
                            if ($pselisih>0 AND $row['ca2']==0) $pjmltrans=0;
                            elseif ($pselisih>0 AND $row['ca2']>0) $pjmltrans=$row['ca2'] + (double)$row['jml_adj'];
                            elseif ($pselisih==0 AND $row['ca2']>0) $pjmltrans=$row['ca2'] + (double)$row['jml_adj'];
                            
                            
                            
                            $gtotca1=(double)$gtotca1+(double)$row['ca1'];
                            $gtotca2=(double)$gtotca2+(double)$row['ca2'];
                            $gtotadj=(double)$gtotadj+(double)$row['jml_adj'];
                            $gtotselisih=(double)$gtotselisih+(double)$pselisih;
                            $gtottrans=(double)$gtottrans+(double)$pjmltrans;
                            
                            $query = "select * from $tmp01 where karyawanid='$pkaryawanid' order by idrutin";
                            $result2 = mysqli_query($cnit, $query);
                                    
                            
                            $belum=false;    
                            while ($row2 = mysqli_fetch_array($result2)) {
                                
                                
                                $pnolk=$row2['idrutin'];
                                $pidca1=$row2['idca1'];
                                $pidca2=$row2['idca2'];
                                $pketerangan=$row2['keterangan'];
                                $pjumlah=number_format($row2['credit'],0,",",",");
                                $gtotjumlah=$gtotjumlah+$row2['credit'];
                                
                                
                                
                                $chkck="";
                                $pnourutnya=$row2['nourut'];
                                $ceklisnya = "<input type='checkbox' value='$pnourutnya' name='chkpilih[]' id='chkpilih[]' class='cekbr' $chkck>";
                                
                                if ($stsreport=="C" OR $stsreport=="S") {
                                    $nourutpilih=$nourutpilih.$row2['nourut'].",";
                                    $ceklisnya="";
                                }
                                
                                if ($belum==true) {
                                    echo "<tr>";
                                    echo "<td>$ceklisnya</td>";
                                    echo "<td></td>";
                                    echo "<td></td>";
                                    
                                    echo "<td nowrap>$pnolk</td>";
                                    echo "<td nowrap align='right'>$pjumlah</td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "</tr>";
                                    
                                }else{
                                    $pselisih=number_format($pselisih,0,",",",");
                                    if ((double)$pjmltrans < 0) $pjmltrans=0;
                                    $pjmltrans=number_format($pjmltrans,0,",",",");
                                    echo "<tr>";
                                    echo "<td>$ceklisnya</td>";
                                    echo "<td>$pnmkaryawan</td>";
                                    echo "<td>$pdivisi</td>";
                                    echo "<td nowrap>$pnolk</td>";
                                    echo "<td nowrap align='right'>$pjumlah</td>";
                                    echo "<td nowrap align='right'>$prprutin</td>";
                                    echo "<td nowrap align='right'>$pca1</td>";
                                    echo "<td nowrap align='right'>$pselisih</td>";
                                    echo "<td nowrap align='right'>$pca2</td>";
                                    echo "<td nowrap align='right'>$pjumlahadj</td>";
                                    echo "<td nowrap align='right'>$pjmltrans</td>";
                                    echo "</tr>";
                                    
                                }
                                
                                $belum=true;
                                $no++;
                                $reco++;
                            }
                            
                            
                                
                        }
                        
                        
                        $gtotjumlah=number_format($gtotjumlah,0,",",",");
                        $gtotca1=number_format($gtotca1,0,",",",");
                        $gtotselisih=number_format($gtotselisih,0,",",",");
                        $gtotca2=number_format($gtotca2,0,",",",");
                        $gtotadj=number_format($gtotadj,0,",",",");
                        if ((double)$gtottrans < 0) $gtottrans=0;
                        $gtottrans=number_format($gtottrans,0,",",",");
                        
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";

                        echo "<td nowrap></td>";
                        echo "<td nowrap align='right'><b>$gtotjumlah</b></td>";
                        echo "<td nowrap align='right'><b>$gtotjumlah</b></td>";
                        echo "<td nowrap align='right'><b>$gtotca1</b></td>";
                        echo "<td nowrap align='right'><b>$gtotselisih</b></td>";
                        echo "<td nowrap align='right'><b>$gtotca2</b></td>";
                        echo "<td nowrap align='right'><b>$gtotadj</b></td>";
                        echo "<td nowrap align='right'><b>$gtottrans</b></td>";
                        echo "</tr>";
                        
                        if (!empty($nourutpilih)) $nourutpilih="(".substr($nourutpilih, 0, -1).")";
                        
                        if ($stsreport=="B") {
                            $gtotjumlah=0;
                            $gtotca1=0;
                            $gtotselisih=0;
                            $gtotca2=0;
                            $gtotadj=0;
                            $gtottrans=0;
                        }
                        
                    }
                    
                ?>
            </tbody>
        </table>

    </div>
    

    <div class='col-md-12 col-sm-12 col-xs-12'>

        <div id="div_jumlah">
            <div class='x_panel'>
                <div class='x_content'>
                    <div class='col-md-12 col-sm-12 col-xs-12'>


                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                            <div class='col-xs-3'>
                                 <div id='loading2'></div>
                                 
                                 <?PHP if ($stsreport=="B") { ?>
                                    <button type='button' class='btn btn-danger btn-xs' onclick='HitungTotalJumlah()'>Hitung Jumlah</button> <span class='required'></span>
                                 <?PHP } ?>
                                <input type='hidden' id='e_per1' name='e_per1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $per1; ?>' Readonly>
                                <input type='hidden' id='e_per2' name='e_per2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $per2; ?>' Readonly>
                                <input type='hidden' id='e_periode' name='e_periode' class='form-control col-md-7 col-xs-12' value='<?PHP echo $tgl1; ?>' Readonly>
                                <input type='hidden' id='e_nourut' name='e_nourut' class='form-control col-md-7 col-xs-12' value='<?PHP echo $nourutpilih; ?>' Readonly>
                                <input type='hidden' id='e_sudah' name='e_sudah' class='form-control col-md-7 col-xs-12' value='<?PHP echo $sudahclosing; ?>' Readonly>
                            </div>
                        </div>

                        <?PHP
                        $nnoidinput="";
                        $nnodiv_br="";
                        $niddanabank="";
                        $query = "select idinput, nodivisi from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'') <> 'Y' AND divisi ='OTC' 
                            AND CONCAT(kodeid,subkode)='221' AND DATE_FORMAT(tglf,'%Y%m')='$bulan'";
                        $tampil=mysqli_query($cnit, $query);
                        $ketemu= mysqli_num_rows($tampil);
                        if ($ketemu>0){
                            $nr= mysqli_fetch_array($tampil);
                            
                            $nnoidinput=$nr['idinput'];
                            $nnodiv_br=$nr['nodivisi'];
                            
                            $query = "select idinputbank, nobukti, tanggal from dbmaster.t_suratdana_bank where idinput='$nnoidinput' AND stsinput='K' AND IFNULL(stsnonaktif,'')<>'Y'";
                            $tampil_=mysqli_query($cnit, $query);
                            $xr= mysqli_fetch_array($tampil_);
                            
                            $niddanabank=$xr['idinputbank'];
                            if (empty($pnobukti)) $pnobukti=$xr['nobukti'];
                            if (!empty($xr['tanggal']) AND $xr['tanggal']<>"0000-00-00") $ptgltrans= date("d F Y", strtotime($xr['tanggal']));
                        }
                        //echo $query;
                        ?>
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Divisi / BR <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_nodivisi' name='e_nodivisi' class='form-control col-md-7 col-xs-12' value='<?PHP echo $nnodiv_br; ?>' Readonly>
                                <input type='hidden' id='e_iddanabank' name='e_iddanabank' class='form-control col-md-7 col-xs-12' value='<?PHP echo $niddanabank; ?>' Readonly>
                            </div>
                        </div>
                        
                        
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Saldo Real <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_saldo' name='e_saldo' class='form-control col-md-7 col-xs-12' value='<?PHP echo $gtotjumlah; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>CA <?PHP echo $per1; ?><span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_ca1' name='e_ca1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $gtotca1; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Selisih <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_selisih' name='e_selisih' class='form-control col-md-7 col-xs-12' value='<?PHP echo $gtotselisih; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>CA <?PHP echo $per2; ?><span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_ca2' name='e_ca2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $gtotca2; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Utang Piutang <?PHP echo $tgl_sblmnya_; ?><span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_jmladj' name='e_jmladj' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $gtotadj; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah Trsf. <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_jmltrsf' name='e_jmltrsf' class='form-control col-md-7 col-xs-12' value='<?PHP echo $gtottrans; ?>' Readonly>
                            </div>
                        </div>
    
                        <div hidden class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Trsf. <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <div class='input-group date' id='tgl1'>
                                    <input type='text' id='e_periode01' name='e_periode01' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $ptgltrans; ?>' data-inputmask="'mask': '99/99/9999'">
                                    <span class="input-group-addon">
                                       <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div hidden class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Bukti <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_nobukti' name='e_nobukti' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnobukti; ?>'>
                            </div>
                        </div>
                        

                        <div hidden class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Divisi / BR <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='hidden' id='txt_idinputdiv' name='txt_idinputdiv' class='form-control col-md-7 col-xs-12' value='<?PHP echo ""; ?>' Readonly>
                                <input type='hidden' id='txt_idnodiv' name='txt_idnodiv' class='form-control col-md-7 col-xs-12' value='<?PHP echo ""; ?>' Readonly>
                            </div>
                        </div>
                        
                        <?PHP if (!empty($nourutpilih)) { ?>
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                            <div class='col-xs-9'>
                                <div class="checkbox">
                                    <!--<button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo "simpan"; ?>")'>Simpan</button>-->
                                    <button type='button' class='btn btn-danger' id="btnhapus" name="btnhapus" onclick='disp_confirm("Hapus ?", "<?PHP echo "hapus"; ?>")'>Hapus</button>
                                </div>
                            </div>
                        </div>
                        <?PHP } ?>
                        
                    </div>
                </div>
            </div>
            
        </div>

    </div>

</form>
    


<?PHP
    mysqli_query($cnit, "DELETE FROM dbmaster.tmp_lk_closing_otc WHERE idsession='$_SESSION[IDSESI]'");
    
        $query = "INSERT INTO dbmaster.tmp_lk_closing_otc (nourut, tglinput, bulan, idrutin, idca1, idca2, userid, idsession, jumlah, totalrutin, ca1, ca2, karyawanid, divisi, sts, jml_adj) "
                . " select nourut, CURRENT_DATE() tglinput, '$tgl1' bulan, idrutin, idca1, idca2, '$_SESSION[IDCARD]' userid, "
                . " '$_SESSION[IDSESI]', credit, saldo, ca1, ca2, karyawanid, divisi, sts, jml_adj FROM $tmp01";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    mysqli_query($cnit, "drop TEMPORARY table $tmp01");
    mysqli_query($cnit, "drop TEMPORARY table $tmp08");
?>

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
    
    function HitungTotalJumlah() {
        var chk_arr =  document.getElementsByName('chkpilih[]');
        var chklength = chk_arr.length;             
        var allnobr="";
        
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                var kata = chk_arr[k].value;
                var fields = kata.split('-');
                //allnobr =allnobr + "'"+fields[0]+"',";
                allnobr =allnobr + fields[0]+",";
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }else{
            alert("Tidak ada data yang dipilih...!!!");
            return false;
        }
        var eper1 =document.getElementById('e_per1').value;
        var eper2 =document.getElementById('e_per2').value;
        var eperiode =document.getElementById('e_periode').value;
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_closing_lkcaotc/viewdata.php?module=hitungtotal",
            data:"uurut="+allnobr+"&uper1="+eper1+"&uper2="+eper2+"&uperiode="+eperiode,
            success:function(data){
                $("#loading2").html("");
                $("#div_jumlah").html(data);
            }
        });
        
    }
</script>

<script>
    
    function disp_confirm(pText_, eact)  {
        
        var enourut =document.getElementById('e_nourut').value;
        var etgltrans =document.getElementById('e_periode01').value;
        var enobukti =document.getElementById('e_nobukti').value;
        var esudah =document.getElementById('e_sudah').value;
        var eiddanabank =document.getElementById('e_iddanabank').value;
        var enodivbr =document.getElementById('e_nodivisi').value;
        var esaldoreal =document.getElementById('e_saldo').value;
        var ejmltrsf =document.getElementById('e_jmltrsf').value;
        
        var eidinputdiv =document.getElementById('txt_idinputdiv').value;
        var eidindivno =document.getElementById('txt_idnodiv').value;

        if (enourut=="") {
            alert("Tidak ada data yang dipilih...");
            return false;    
        }
        
        if (eact=="hapus") {
            
        }else{
            if (eidindivno=="") {
                alert("No divisi masih kosong...");
                return false;    
            }
        }
            
        if (eact=="") {
            var chk_arr =  document.getElementsByName('chkpilih[]');
            var chklength = chk_arr.length;             
            var allnobr="";

            for(k=0;k< chklength;k++)
            {
                if (chk_arr[k].checked == true) {
                    var kata = chk_arr[k].value;
                    var fields = kata.split('-');
                    //allnobr =allnobr + "'"+fields[0]+"',";
                    allnobr =allnobr + fields[0]+",";
                }
            }
            if (allnobr.length > 0) {
                var lastIndex = allnobr.lastIndexOf(",");
                allnobr = "("+allnobr.substring(0, lastIndex)+")";
            }else{
                alert("Tidak ada data yang dipilih...!!!");
                return false;
            }
            
            var eact="input";
            var pText_="Simpan";
            if (eact=="hapus") { var pText_="Hapus"; var eact="hapus"; }

        }
        
        //alert(enodivbr); return false;
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
                $.ajax({
                    type:"post",
                    url:"module/mod_br_closing_lkcaotc/aksi_closing_lkcaotc.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"unourut="+enourut+"&utgltrans="+etgltrans+"&unobukti="+enobukti+"&usudahpernah="+esudah+
                            "&uact="+eact+"&unodivbr="+enodivbr+"&uiddanabank="+eiddanabank+"&usaldoreal="+esaldoreal+
                            "&uidinputdiv="+eidinputdiv+"&uidindivno="+eidindivno+"&ujmltrsf="+ejmltrsf,
                    success:function(data){
                        $("#loading2").html("");
                        alert(data);
                        HideHitungSave();
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    function HideHitungSave() {
        document.getElementById("btnhitung").style.visibility = "hidden";
        document.getElementById("btnsave").style.visibility = "hidden";
    }
</script>

<script type="text/javascript">
    $(function() {
        $('#e_periode01').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            /*
            minDate: '0',
            maxDate: '+2Y',
            */
            onSelect: function(dateStr) {
                ShowNoBuktiBBK();
            } 
        });
    });
    
    function ShowNoBuktiBBK() {
        var inospd = "";
        var inodivisi = "";
        var itgl = document.getElementById('e_periode01').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_danabank/viewdata.php?module=viewnobuktidivisi",
            data:"utgl="+itgl+"&unospd="+inospd+"&unodivisi="+inodivisi,
            success:function(data){
                document.getElementById('e_nobukti').value=data;
            }
        });
    }
</script>


<script>
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("myBtn").style.display = "block";
      } else {
        document.getElementById("myBtn").style.display = "none";
      }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    }
</script>