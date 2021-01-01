<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>
<?PHP
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
    session_start();
    include "../../config/koneksimysqli_it.php";
    include "../../config/fungsi_sql.php";
    include "../../config/library.php";
    $cnmy=$cnit;
    
    $cket=$_POST['usts'];
    
    $date1=$_POST['utgl'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $bulan= date("Ym", strtotime($date1));
    $periode1= date("Y-m", strtotime($date1));
    
    $jabatan_id=$_POST['uidjabatan'];
    $sr_id=$_POST['uidkaryawan'];
    
    $_SESSION['SPGMSTGJTGLCAB']=date("F Y", strtotime($date1));
    
    
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DPROSMRCALINC01_".$userid."_$now ";
    $tmp02 =" dbtemp.DPROSMRCALINC02_".$userid."_$now ";
    $tmp03 =" dbtemp.DPROSMRCALINC03_".$userid."_$now ";
    $tmp04 =" dbtemp.DPROSMRCALINC04_".$userid."_$now ";
    
    
    
    if ($cket=="1") {
        $query = "SELECT jumlah FROM hrd.hrkrj WHERE left(periode1,7)='$periode1'";
        $result = mysqli_query($cnmy, $query);
        $row = mysqli_fetch_array($result);
        $num_results = mysqli_num_rows($result);
        $jml_hari_krj = $row['jumlah']; 
        if (empty($jml_hari_krj)) $jml_hari_krj=0;

        $filterjabatan="";
        if (!empty($jabatan_id)) $filterjabatan=" AND jabatanid='$jabatan_id' ";

        $filterkaryawan="";
        if (!empty($sr_id)) $filterkaryawan=" AND srid='$sr_id' ";
        
        $filterkaryawan2="";
        if (!empty($sr_id)) $filterkaryawan2=" AND karyawanid='$sr_id' ";


        $query = "select distinct IFNULL(karyawanid,'') karyawanid from dbmaster.t_call_incentive WHERE (left(bulan,7)= '$periode1') $filterkaryawan2 $filterjabatan";
        $query = "create TEMPORARY table $tmp04 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        //$filtersudahproses=" AND srid NOT IN (select distinct IFNULL(karyawanid,'') FROM dbmaster.t_call_incentive WHERE (left(bulan,7)= '$periode1'))";
        $filtersudahproses=" AND srid NOT IN (select distinct IFNULL(karyawanid,'') FROM $tmp04)";
        
        $query = "select * from hrd.persen_call WHERE (left(tgl,7)= '$periode1') $filterkaryawan $filterjabatan $filtersudahproses";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "SELECT persen_call.*,karyawan.nama  
                FROM $tmp03 persen_call 
                JOIN hrd.karyawan karyawan ON persen_call.srid = karyawan.karyawanid";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "select distinct srid karyawanid, nama, jabatanid, CAST(null as DECIMAL(20,10)) as tpersen from $tmp02";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $jab=0;
        $jpoint=0;
        $totcall=0;
        $totpoint1=0;
        $totpoint2=0;
        $summary_=0;

        $query = "select distinct srid karyawanid, nama, jabatanid from $tmp02";
        $tampil = mysqli_query($cnmy, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $pidkaryawan=$row['karyawanid'];
            $pidjabatan=$row['jabatanid'];

            $jab=0;
            if ($pidjabatan=='08') {
                $jab = 4;
            } else {
                if (($pidjabatan=='10') or ($pidjabatan=='18')) {
                    $jab = 6;
                } else {
                    if ($pidjabatan=='15') {
                        $jab = 10;
                    }
                }
            }

            $jpoint = (double)$jab * (double)$jml_hari_krj;


            $totcall=0;
            $totpoint1=0;
            $totpoint2=0;
            $summary_=0;


            $query = "select * from $tmp02 WHERE srid='$pidkaryawan' ORDER BY tgl";
            $tampil2 = mysqli_query($cnmy, $query);
            while ($row2= mysqli_fetch_array($tampil2)) {
                $totcall = $totcall + $row2['call1'];


                if ($row2['point1'] != 0) {
                    if ($row2['point1'] >= 0) {
                        $totpoint2 = $totpoint2 + $row2['point1'];
                    }else{
                        $totpoint1 = $totpoint1 + abs($row2['point1']);
                    }
                }

            }

            $summary_ = (( (double)$totcall+(double)$totpoint2) / ((double)$jpoint-(double)$totpoint1)) * 100;

            mysqli_query($cnmy, "UPDATE $tmp01 SET tpersen='$summary_' WHERE karyawanid='$pidkaryawan'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        }
    
        
    }else{
        include "../../config/koneksimysqli_ms.php";
        $cnmy=$cnms;
        
        $filterjabatan="";
        if (!empty($jabatan_id)) $filterjabatan=" AND a.jabatanid='$jabatan_id' ";

        $filterkaryawan="";
        if (!empty($sr_id)) $filterkaryawan=" AND a.karyawanid='$sr_id' ";
        
        $query = "select a.id, a.karyawanid, nama, a.jabatanid, a.jumlah as tpersen from ms.t_call_incentive a JOIN ms.karyawan b on a.karyawanid=b.karyawanid"
                . " WHERE DATE_FORMAT(bulan,'%Y-%m')='$periode1' $filterkaryawan $filterjabatan";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    $chkall = "<input type='checkbox' id='chkbtnbr' value='select' onClick=\"SelAllCheckBox('chkbtnbr', 'chkbox_br[]')\" />";
?>


<form method='POST' action='<?PHP echo "?module=$_POST[module]&act=input&idmenu=$_POST[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_POST['module']; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_POST['idmenu']; ?>' Readonly>
    <input type='hidden' id='u_tgl1' name='u_tgl1' value='<?PHP echo $tgl1; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    <input type="hidden" class="form-control" id='e_status' name='e_status' autocomplete="off" required='required'  value='<?PHP echo "$cket"; ?>' Readonly>
    
    
    
    <div class='x_content'>
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='20px'><?PHP echo $chkall; ?></th>
                    <th width='300px' align="center">Nama</th>
                    <th align="center" width='80px'>%</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    $query = "select * from $tmp01 order by nama";
                    $tampil = mysqli_query($cnmy, $query);
                    while ($sp= mysqli_fetch_array($tampil)) {
                        
                        $pkaryawanid=$sp['karyawanid'];
                        $pkaryawannm=$sp['nama'];
                        $pjabatanid=$sp['jabatanid'];
                        $ppersen=ROUND($sp['tpersen'],2);
                        
                        $idno=$pkaryawanid;//.",".$tgl1
                        
                        $pjabatankry="<input type='hidden' size='8px' id='txtjabatankry[]' name='txtjabatankry[]' class='input-sm' autocomplete='off' value='$pjabatanid'>";
                        $pjmlpersen="<input type='hidden' size='8px' id='txtjmlpersen[]' name='txtjmlpersen[]' class='input-sm inputmaskrp2' autocomplete='off' value='$ppersen'>";
                        
                        if ($cket=="2") $idno=$sp['id'];
                        
                        $cekbox = "<input type=checkbox value='$idno' name=chkbox_br[]>";
                        
                        echo "<tr>";
                        echo "<td>$no</td>";
                        echo "<td>$cekbox</td>";
                        echo "<td>$pkaryawannm $pjabatankry</td>";
                        echo "<td>$ppersen $pjmlpersen</td>";
                        echo "</tr>";
                        
                        $no++;
                    }
                ?>
            </tbody>
        </table>

            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div hidden class='col-sm-3'>
                        <button type='button' class='btn btn-default btn-xs'>Periode & cabang</button> <span class='required'></span>
                       <div class="form-group">
                            <div class='input-group date' id=''>
                                <input type="text" class="form-control" id='e_periodepilih' name='e_periodepilih' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo "$ptanggal"; ?>' Readonly>
                                <input type="text" class="form-control" id='e_periodepilih2' name='e_periodepilih2' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo "$ptanggal2"; ?>' Readonly>
                                <input type="text" class="form-control" id='e_cabangpilih' name='e_cabangpilih' autocomplete="off" required='required'  value='<?PHP echo "$pidcabang"; ?>' Readonly>
                                <input type="text" class="form-control" id='e_status' name='e_status' autocomplete="off" required='required'  value='<?PHP echo "$cket"; ?>' Readonly>
                            </div>
                       </div>
                   </div>
                    
                    <?PHP if ($cket=="1" OR $cket=="3") { ?>

                        <div class='col-sm-3'>
                            <div style="padding-bottom:10px;">&nbsp;</div>
                           <div class="form-group">
                                <input type='button' class='btn btn-success btn-sm' id="s-submit" value="Proses" onclick='disp_confirm("simpan", "chkbox_br[]")'>
                           </div>
                       </div>
                    <?PHP }elseif ($cket=="2") { ?>
                    
                        <div class='col-sm-3'>
                            <small>&nbsp;</small>
                           <div class="form-group">
                               <input type='button' class='btn btn-info btn-sm' id="s-submit" value="Un Proses" onclick='disp_confirm("hapus", "chkbox_br[]")'>
                           </div>
                       </div>
                    <?PHP } ?>
                    
                </div>
            </div>
        
    </div>
    
</form>

<?PHP
hapusdata : 
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp04");
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
</style>

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
    
    function disp_confirm(ket, cekbr){
        
        var iperiode =  document.getElementById('u_tgl1').value;
        var istatus =  document.getElementById('e_status').value;
        
        if (ket=="simpan") {
            var cmt = confirm('Apakah akan melakukan proses '+ket+' ...?');
        }else if (ket=="hapus") {
            var cmt = confirm('Apakah akan melakukan unproses ...?');
        }else{
            var cmt = confirm('Apakah akan melakukan proses ...?');
        }
        if (cmt == false) {
            return false;
        }
        
        var newchar = '';
        var txt_persen =  document.getElementsByName('txtjmlpersen[]');
        var jml_persen1="";
        var jml_persen="";
        
        var txt_jabatan =  document.getElementsByName('txtjabatankry[]');
        
        var chk_arr =  document.getElementsByName(cekbr);
        var chklength = chk_arr.length;             
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
                
                jml_persen1 = txt_persen[k].value;
                if (jml_persen1=="") jml_persen1="0";
                jml_persen1 = chk_arr[k].value+"_"+jml_persen1.split(',').join(newchar)+"_"+txt_jabatan[k].value;
                
                jml_persen = jml_persen+""+jml_persen1+",";
                
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
            
        }else{
            alert("Tidak ada data yang diproses...!!!");
            return false;
        }
        
        
        //alert(ket+", "+iperiode+", "+allnobr); return false;
        
        var txt="";
        if (ket=="reject" || ket=="pending") {
            var textket = prompt("Masukan alasan "+ket+" : ", "");
            if (textket == null || textket == "") {
                txt = textket;
            } else {
                txt = textket;
            }
        }
       
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mst_prosesinsentif/viewdata.php?module="+ket,
            data:"unoidbr="+allnobr+"&utgl="+iperiode+"&ujmlpersen="+jml_persen,
            success:function(data){
                $("#loading2").html("");
                if (istatus=="1") {
                    RefreshDataTabel('1')
                }else if (istatus=="2") {
                    RefreshDataTabel('2')
                }
                alert(data);
            }
        });
        
    }
</script>