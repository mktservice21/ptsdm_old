<?php session_start(); ?>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>
    
<script>
    $('#e_tglberlaku').datepicker({
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        firstDay: 1,
        dateFormat: 'dd MM yy',
        onSelect: function(dateStr) {
            ShowNoBukti();
            //ShowDataSudahSimpan();
        } 
    });
        
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
        HitungTotalJumlahData();
    }
    
    function HitungTotalJumlahData() {
        var chk_arr1 =  document.getElementsByName('chk_jml1[]');
        var chk_arr2 =  document.getElementsByName('chk_jml2[]');
        var chklength1 = chk_arr1.length; 
        var chklength2 = chk_arr2.length;
        
        var allnobr="";
        var TotalPilih=0;
        
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                var kata = chk_arr1[k].value;
                var fields = kata.split('-');
                allnobr =allnobr + "'"+fields[0]+"',";
                TotalPilih++;
            }
        }
        
        for(k=0;k< chklength2;k++)
        {
            if (chk_arr2[k].checked == true) {
                var kata = chk_arr2[k].value;
                var fields = kata.split('-');
                allnobr =allnobr + "'"+fields[0]+"',";
                TotalPilih++;
            }
        }
        
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_spd_eth/viewdata.php?module=hitungtotal",
            data:"unoidbr="+allnobr,
            success:function(data){
                $("#loading2").html("");
                document.getElementById('e_jmlusulan').value=data;
            }
        });
        
    }
    
    function ShowNoBukti() {
        var idiv = document.getElementById('cb_divisi').value;
        var ikode = "";
        var ikodesub = "";
        var itgl = document.getElementById('e_tglberlaku').value;
        var ijenis=document.getElementById('cb_jenis').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=viewnomorbukti",
            data:"udivisi="+idiv+"&ukode="+ikode+"&ukodesub="+ikodesub+"&utgl="+itgl+"&ujenis="+ijenis,
            success:function(data){
                document.getElementById('e_nomordiv').value=data;
            }
        });
    }
    
    
    function ShowDataSudahSimpan() {
        var idiv = document.getElementById('cb_divisi').value;
        var ikode = "";
        var ikodesub = "";
        var itgl = document.getElementById('e_tglberlaku').value;
        var ijenis=document.getElementById('cb_jenis').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_spd_eth/viewdata.php?module=caridatasudahsimpan",
            data:"udivisi="+idiv+"&ukode="+ikode+"&ukodesub="+ikodesub+"&utgl="+itgl+"&ujenis="+ijenis,
            success:function(data){
                alert(data);
            }
        });
    }
    
    function disp_confirm(pText_,ket)  {
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_br_spd_eth/aksi_spd_eth.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>
<?php

    include "../../config/koneksimysqli_it.php";
    include "../../config/koneksimysqli.php";
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $pdivisi=$_POST['udivisi'];
    $ptipe=$_POST['utgltipe'];
    $pjenis=$_POST['ujenis'];
    
    $tgl1= date("Y-m-d", strtotime($date1));
    $bulan1= date("Ym", strtotime($date1));
    
    $tgl2= date("Y-m-d", strtotime($date2));
    $bulan2= date("Ym", strtotime($date2));
    
    $userid=$_SESSION['IDCARD'];
    
    $ftypetgl = " AND  a.tgl BETWEEN '$tgl1' AND '$tgl2' ";
    if ((INT)$ptipe==2) $ftypetgl = " AND a.tgltrans BETWEEN '$tgl1' AND '$tgl2' ";
    
    $fdivisi = "";
    $fdivisi2 = "";
    if (!empty($pdivisi)) {
        $fdivisi = " AND a.divprodid='$pdivisi' ";
        $fdivisi2 = " AND a.divprodid<>'$pdivisi' ";
    }
    
    $fjenis=" AND IFNULL(lampiran,'N')='Y'";
    if ($pjenis=="N") $fjenis=" AND IFNULL(lampiran,'N')<>'Y'";
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DSETH01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSETH02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSETH03_".$userid."_$now ";
    
    //cari yang sudah ada
    $ntglpilih = date('Y-m-d', strtotime($date1));
    $query = "select distinct IFNULL(bridinput,'') bridinput FROM dbmaster.t_suratdana_br1 WHERE idinput in "
            . "(select distinct IFNULL(idinput,'') idinput from dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND divisi<>'$pdivisi' AND tgl='$ntglpilih')";
    
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    //END 
    
    $querysql = "select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.karyawanId, c.nama nama_karyawan, a.dokterId, a.dokter, b.nama nama_dokter,
        a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, a.realisasi2 from hrd.br0 a 
        LEFT JOIN hrd.dokter b on a.dokterId=b.dokterId
        LEFT JOIN hrd.karyawan c on a.karyawanId=c.karyawanId 
        WHERE a.brid NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) $ftypetgl $fjenis AND 
        a.COA4 IN (SELECT DISTINCT IFNULL(COA4,'') COA4 FROM dbmaster.coa_wewenang WHERE karyawanId='$userid') AND
         brId NOT IN (select DISTINCT IFNULL(bridinput,'') bridinput FROM $tmp03)";
    
    $myquery = $querysql." $fdivisi ";
    $query = "create TEMPORARY table $tmp01 ($myquery)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if (!empty($fdivisi2)) {
        $myquery = $querysql." $fdivisi2 ";
        $query = "create TEMPORARY table $tmp02 ($myquery)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    $idbr="";
    $tgl1 = date('d F Y', strtotime($date1));
    $pdivnomor="";
    $jumlah=0;
    
    $query = "select SUM(jumlah) jumlah from $tmp01";
    $tampil=mysqli_query($cnmy, $query);
    $ro= mysqli_fetch_array($tampil);
    if (!empty($ro['jumlah'])) $jumlah=$ro['jumlah'];
    
    $ntglpilih = date('Y-m-d', strtotime($date1));
    $query = "select idinput, divisi, nodivisi from dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' and divisi='$pdivisi' and tgl='$ntglpilih'";
    $tampilkan=mysqli_query($cnmy, $query);
    $ketemuada= mysqli_num_rows($tampilkan);
    if ($ketemuada>0) {
        $ad= mysqli_fetch_array($tampilkan);
        if (!empty($ad['nodivisi'])) $pdivnomor=$ad['nodivisi'];
        $idbr=$ad['idinput'];
        
        
        
        $query = "INSERT INTO $tmp03 select distinct IFNULL(bridinput,'') bridinput FROM dbmaster.t_suratdana_br1 WHERE idinput='$idbr'";
        //$query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
        mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE brId NOT IN (select IFNULL(bridinput,'') bridinput FROM $tmp03)");
        mysqli_query($cnmy, "DELETE FROM $tmp02 WHERE brId NOT IN (select IFNULL(bridinput,'') bridinput FROM $tmp03)");
        
        $jumlah=0;
        $query = "select SUM(jumlah) jumlah from $tmp01";
        $tampil=mysqli_query($cnmy, $query);
        $ro= mysqli_fetch_array($tampil);
        if (!empty($ro['jumlah'])) $jumlah=$ro['jumlah'];
    
        $jml2=0;
        $query = "select SUM(jumlah) jml from $tmp02";
        $tampil2=mysqli_query($cnmy, $query);
        $ro2= mysqli_fetch_array($tampil2);
        if (!empty($ro2['jml'])) $jml2=$ro2['jml'];
        $jumlah=$jumlah+$jml2;
    }
    
?>


<span method='POST' action='<?PHP echo "?module=$_POST[module]&act=input&idmenu=$_POST[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_POST['module']; ?>' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_POST['idmenu']; ?>' Readonly>
    <input type='hidden' id='u_tgl1' name='u_tgl1' value='<?PHP echo $tgl1; ?>' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
    
           
    <div class='x_panel'>
        <div class='x_content'>
            <div class='col-md-12 col-sm-12 col-xs-12'>

                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                    <div class='col-md-4'>
                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idbr; ?>' Readonly>
                        <input type='hidden' id='cb_jenis' name='cb_jenis' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pjenis; ?>' Readonly>
                        <input type='hidden' id='cb_divisi' name='cb_divisi' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivisi; ?>' Readonly>
                    </div>
                </div>


                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal Pengajuan Dana</label>
                    <div class='col-md-3'>
                        <div class='input-group date' id=''>
                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl1; ?>'>
                            <span class='input-group-addon'>
                                <span class='glyphicon glyphicon-calendar'></span>
                            </span>
                        </div>
                    </div>
                </div>


                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. Divisi / No. BR <span class='required'></span></label>
                    <div class='col-xs-3'>
                        <input type='text' id='e_nomordiv' name='e_nomordiv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivnomor; ?>'>
                    </div>
                </div>


                <div class='form-group'>
                    <div id='loading2'></div>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                        <button type='button' class='btn btn-danger btn-xs' onclick='HitungTotalJumlahData()'>Hitung Jumlah</button> <span class='required'></span>
                    </label>
                    <div class='col-md-3'>
                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlah; ?>' >
                    </div>
                </div>


                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                    <div class='col-xs-9'>
                        <div class="checkbox">
                            <?PHP 
                                if (empty($pdivnomor)) { 
                                    echo "<button type='button' class='btn btn-success' onclick=\"disp_confirm('Simpan ?', '')\">Save</button>";
                                }else{
                                    echo "<button type='button' class='btn btn-danger' onclick=\"disp_confirm('Hapus ?', 'hapus')\">Hapus</button>";
                                }
                            ?>
                            <!--<button type='hidden' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo ""; ?>")'>Save</button>-->
                        </div>
                    </div>
                </div>


            </div>

        </div>

    </div>
                    
           
    <div id="s_div">
        
        <div class='x_content'>
            <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
                <thead>
                    <tr>
                        <th width='10px'><input type="checkbox" id="chkall1[]" name="chkall1[]" onclick="SelAllCheckBox('chkall1[]', 'chk_jml1[]')" checked></th>
                        <th width='10px'>No</th>
                        <th align="center" nowrap>No. Slip</th>
                        <th align="center">Tgl. Transfer</th>
                        <th align="center" nowrap>Nama Pembuat</th>
                        <th align="center" nowrap>Nama Dokter</th>
                        <th align="center" nowrap>Keterangan</th>
                        <th align="center" nowrap>Nama Realisasi</th>
                        <th align="center" nowrap>Jumlah</th>
                        <th align="center" nowrap>Divisi</th>
                    </tr>
                </thead>
                <tbody>

                    <?PHP
                        //harus ada, untuk cek doang
                        echo "<input type='hidden' id='chk_jml1[]' name='chk_jml1[]' value=''>"
                            . "<input type='hidden' id='chk_jml2[]' name='chk_jml2[]' value=''>";

                        $no=1;
                        $query = "select * from $tmp01 order by noslip, brId";
                        $tampil=mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            $pbrid = $row['brId'];
                            $pnoslip = $row['noslip'];
                            $ptgltrans = "";
                            if (!empty($row['tgltrans']))
                                $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));

                            $pnamakaryawan = $row['nama_karyawan'];
                            $piddokter = $row['dokterId'];
                            $pnmdokter = $row['nama_dokter'];
                            if (empty($pnmdokter)) $pnmdokter = $row['dokter'];
                            $paktivitas1 = $row['aktivitas1'];
                            $paktivitas2 = $row['aktivitas2'];
                            $prealisasi1 = $row['realisasi1'];
                            $pdivisi = $row['divprodid'];

                            $pjumlah = $row['jumlah'];
                            $pjmlreal = $row['realisasi2'];

                            $pjumlah=number_format($row['jumlah'],0,",",",");
                            if (!empty($row['realisasi2']))
                                $pjmlreal=number_format($row['realisasi2'],0,",",",");


                            $chkbox = "<input type='checkbox' id='chk_jml1[]' name='chk_jml1[]' value='$pbrid' checked> ";
                            echo "<tr>";
                            echo "<td nowrap>$chkbox<t/d>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap>$pnoslip</td>";
                            echo "<td nowrap>$ptgltrans</td>";
                            echo "<td>$pnamakaryawan</td>";
                            echo "<td>$pnmdokter</td>";
                            echo "<td>$paktivitas1</td>";
                            echo "<td>$prealisasi1</td>";
                            echo "<td nowrap align='right'>$pjumlah</td>";
                            echo "<td nowrap>$pdivisi</td>";
                            echo "</tr>";

                            $no++;
                        }
                    ?>

                    <?PHP
                    if (!empty($fdivisi2)) {
                        echo "<tr><td colspan='10'></td></tr>";
                        if (!empty($pdivnomor)){
                            echo "<tr><th><input type='checkbox' id='chkall2[]' name='chkall2[]' onclick=\"SelAllCheckBox('chkall2[]', 'chk_jml2[]')\" value='deselect' checked></th><th colspan='9'></th></tr>";
                        }else{
                            echo "<tr><th><input type='checkbox' id='chkall2[]' name='chkall2[]' onclick=\"SelAllCheckBox('chkall2[]', 'chk_jml2[]')\" value='select'></th><th colspan='9'></th></tr>";
                        }

                        //$no=1;
                        $query = "select * from $tmp02 order by divprodid, noslip, brId";
                        $tampil=mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            $pbrid = $row['brId'];
                            $pnoslip = $row['noslip'];
                            $ptgltrans = "";
                            if (!empty($row['tgltrans']))
                                $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));

                            $pnamakaryawan = $row['nama_karyawan'];
                            $piddokter = $row['dokterId'];
                            $pnmdokter = $row['nama_dokter'];
                            if (empty($pnmdokter)) $pnmdokter = $row['dokter'];
                            $paktivitas1 = $row['aktivitas1'];
                            $paktivitas2 = $row['aktivitas2'];
                            $prealisasi1 = $row['realisasi1'];
                            $pdivisi = $row['divprodid'];

                            $pjumlah = $row['jumlah'];
                            $pjmlreal = $row['realisasi2'];

                            $pjumlah=number_format($row['jumlah'],0,",",",");
                            if (!empty($row['realisasi2']))
                                $pjmlreal=number_format($row['realisasi2'],0,",",",");

                            if (!empty($pdivnomor)){
                                $chkbox = "<input type='checkbox' id='chk_jml2[]' name='chk_jml2[]' value='$pbrid' checked> ";
                            }else{
                                $chkbox = "<input type='checkbox' id='chk_jml2[]' name='chk_jml2[]' value='$pbrid'> ";
                            }
                            echo "<tr>";
                            echo "<td nowrap>$chkbox<t/d>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap>$pnoslip</td>";
                            echo "<td nowrap>$ptgltrans</td>";
                            echo "<td>$pnamakaryawan</td>";
                            echo "<td>$pnmdokter</td>";
                            echo "<td>$paktivitas1</td>";
                            echo "<td>$prealisasi1</td>";
                            echo "<td nowrap align='right'>$pjumlah</td>";
                            echo "<td>$pdivisi</td>";
                            echo "</tr>";

                            $no++;
                        }

                    }
                    ?>
                </tbody>
            </table>

        </div>
        
    </div>
    
</span>

<?PHP
mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
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
    $(document).ready(function() {
        <?PHP if (empty($pdivnomor)) { ?>
            ShowNoBukti();
        <?PHP } ?>
    } );
</script>