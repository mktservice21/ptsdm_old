<?php
session_start();
if ($_GET['module']=="viewdatakas"){
    
    ?>
    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    <script src="js/inputmask.js"></script>
    <?PHP
    
    include "../../config/koneksimysqli.php";
    $totalinput=0;
    $pket=$_GET['ket'];
    $pact=$_POST['uact'];
    $psubkode=$_POST['usubkode'];
    
    $pidinput=$_POST['eidinput'];
    
    $date1=$_POST['uper1'];
    $mytgl1= date("Y-m-d", strtotime($date1));
    $mytahun= date("Y", strtotime($date1));
    
    $date2=$_POST['uper2'];
    $mytgl2= date("Y-m-d", strtotime($date2));
    
    
    $userid=$_SESSION['IDCARD'];
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.DSETHY00_".$userid."_$now ";
    $tmp01 =" dbtemp.DSETHY01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSETHY02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSETHY03_".$userid."_$now ";
    
    
    $nfilterid_sudah="";
    if ($pact=="editdata") $nfilterid_sudah=" AND a.idinput<> '$pidinput' ";
    
    $query = "select distinct a.bridinput from dbmaster.t_suratdana_br1 a JOIN dbmaster.t_suratdana_br b "
            . " on a.idinput=b.idinput WHERE b.stsnonaktif<>'Y' AND a.kodeinput='K' $nfilterid_sudah";
    $query = "create TEMPORARY table $tmp00 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    if ($pact=="editdata") {
        
        $query = "select distinct bridinput from dbmaster.t_suratdana_br1 WHERE idinput='$pidinput'";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "DELETE FROM $tmp00 WHERE bridinput IN (select distinct IFNULL(bridinput,'') bridinput FROM $tmp03)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    $nfilter_sudahada=" AND a.kasId NOT IN (select distinct IFNULL(bridinput,'') bridinput FROM $tmp00)";

    
    
    $query = "select a.kasId, a.karyawanid, b.nama nama_karyawan, a.nama, a.kode, 
        a.aktivitas1, a.aktivitas2, a.jumlah, a.periode1, a.periode2
        from hrd.kas a LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanId WHERE 
        ( (a.periode1 BETWEEN '$mytgl1' AND '$mytgl2') OR (a.periode2 BETWEEN '$mytgl1' AND '$mytgl2') ) $nfilter_sudahada
        "; 

    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if ($pact=="editdata") {
    
        $query = "select a.*, b.bridinput from $tmp01 a LEFT JOIN $tmp03 b on a.kasId=b.bridinput";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }else{
        $query = "select *, CAST('' as CHAR(20)) as bridinput from $tmp01";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    ?>

        <div class='form-group'>
            &nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-danger btn-xs' onclick='HitungTotalDariCekBox()'>Hitung Jumlah</button> <span class='required'></span>
        </div>
        <div class='x_content'>
            
            
            <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
                <thead>
                    <tr>
                        <th width='10px'><input type="checkbox" id="chkall1[]" name="chkall1[]" onclick="SelAllCheckBox('chkall1[]', 'chk_jml1[]')" value='select'></th>
                        <th width='10px'>No</th>
                        <th align="center" nowrap>Kas Id</th>
                        <th align="center" nowrap>Tanggal</th>
                        <th align="center" nowrap>Nama</th>
                        <th align="center" nowrap>Aktivitas 1</th>
                        <th align="center" nowrap>Aktivitas 2</th>
                        <th align="center" nowrap>Jumlah</th>
                    </tr>
                </thead>
                <tbody>

                    <?PHP
                        //harus ada, untuk cek doang
                        echo "<input type='hidden' id='chk_jml1[]' name='chk_jml1[]' value=''>"
                            . "<input type='hidden' id='chk_jml2[]' name='chk_jml2[]' value=''>";

                        $no=1;
                        $query = "select * from $tmp02 order by periode2, kasId, nama_karyawan";
                        $tampil=mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            $pbrid = $row['kasId'];

                            $pnamakaryawan = $row['nama_karyawan'];
                            $pnama = $row['nama'];
                            $paktivitas1 = $row['aktivitas1'];
                            $paktivitas2 = $row['aktivitas2'];
                            $nperiode2 = $row['periode2'];
                            
                            $pjumlah = $row['jumlah'];
                            $pjumlah=number_format($pjumlah,0,",",",");

                            $pbrid_input = $row['bridinput'];
                            $ncheck_sudah="";
                            if ($pact=="editdata") {
                                if (!empty($pbrid_input)) $ncheck_sudah="checked";
                            }

                            $chkbox = "<input type='checkbox' id='chk_jml1[]' name='chk_jml1[]'  onclick=\"HitungTotalDariCekBox()\" value='$pbrid' $ncheck_sudah> ";
                            echo "<tr>";
                            echo "<td nowrap>$chkbox<t/d>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap>$pbrid</td>";
                            echo "<td nowrap>$nperiode2</td>";
                            echo "<td>$pnama</td>";
                            echo "<td>$paktivitas1</td>";
                            echo "<td>$paktivitas2</td>";
                            echo "<td nowrap align='right'>$pjumlah</td>";
                            echo "</tr>";

                            $no++;
                        }
                    ?>

                </tbody>
            </table>

        </div>

    
    <?PHP
    if ($psubkode=="22") {
        
        mysqli_query($cnmy, "drop TEMPORARY table $tmp00");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
        
        $nfilterid_sudah="";
        if ($pact=="editdata") $nfilterid_sudah=" AND a.idinput<> '$pidinput' ";

        $query = "select distinct a.bridinput from dbmaster.t_suratdana_br1 a JOIN dbmaster.t_suratdana_br b "
                . " on a.idinput=b.idinput WHERE b.stsnonaktif<>'Y' AND a.kodeinput='T' $nfilterid_sudah";
        $query = "create TEMPORARY table $tmp00 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        if ($pact=="editdata") {

            $query = "select distinct bridinput from dbmaster.t_suratdana_br1 WHERE idinput='$pidinput'";
            $query = "create TEMPORARY table $tmp03 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "DELETE FROM $tmp00 WHERE bridinput IN (select distinct IFNULL(bridinput,'') bridinput FROM $tmp03)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }

        $nfilter_sudahada=" AND a.idkasbon NOT IN (select distinct IFNULL(bridinput,'') bridinput FROM $tmp00)";



        $query = "select a.idkasbon, a.nama, a.keterangan, a.jumlah, a.tgl
            from dbmaster.t_kasbon a WHERE IFNULL(stsnonaktif,'')<>'Y' AND IFNULL(iselesai,'')<>'Y' AND 
            YEAR(a.tgl) >= '$mytahun' $nfilter_sudahada"; 

        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        if ($pact=="editdata") {

            $query = "select a.*, b.bridinput from $tmp01 a LEFT JOIN $tmp03 b on a.idkasbon=b.bridinput";
            $query = "create TEMPORARY table $tmp02 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        }else{
            $query = "select *, CAST('' as CHAR(20)) as bridinput from $tmp01";
            $query = "create TEMPORARY table $tmp02 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
        
        
    ?>
        <div class='form-group'>
            &nbsp;&nbsp;&nbsp;<b><u>KASBON</u></b><br/>&nbsp;
            &nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-warning btn-xs' onclick='HitungTotalKBDariCekBox()'>Hitung Jumlah Kasbon</button> <span class='required'></span>
        </div>
    
        <div class='x_content'>
            
            
            <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
                <thead>
                    <tr>
                        <th width='10px'><input type="checkbox" id="chk_kasbon" name="chk_kasbon" onclick="SelAllCheckBoxKB('chk_kasbon', 'chk_jml_kb[]')" value='select'></th>
                        <th width='10px'>No</th>
                        <th align="center" nowrap>ID</th>
                        <th align="center" nowrap>Tanggal</th>
                        <th align="center" nowrap>Nama</th>
                        <th align="center" nowrap>Jumlah</th>
                        <th align="center" nowrap>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?PHP

                        $no=1;
                        $query = "select * from $tmp02 order by nama, tgl, idkasbon";
                        $tampil=mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            $pbrid = $row['idkasbon'];

                            $pnama = $row['nama'];
                            $paktivitas1 = $row['keterangan'];
                            $nperiode = $row['tgl'];
                            
                            $pjumlah = $row['jumlah'];
                            $pjumlah=number_format($pjumlah,0,",",",");

                            $pbrid_input = $row['bridinput'];
                            $ncheck_sudah="";
                            if ($pact=="editdata") {
                                if (!empty($pbrid_input)) $ncheck_sudah="checked";
                            }

                            $chkbox = "<input type='checkbox' id='chk_jml_kb[]' name='chk_jml_kb[]'  onclick=\"HitungTotalKBDariCekBox()\" value='$pbrid' $ncheck_sudah> ";
                            echo "<tr>";
                            echo "<td nowrap>$chkbox<t/d>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap>$pbrid</td>";
                            echo "<td nowrap>$nperiode</td>";
                            echo "<td>$pnama</td>";
                            echo "<td nowrap align='right'>$pjumlah</td>";
                            echo "<td>$paktivitas1</td>";
                            echo "</tr>";

                            $no++;
                        }
                    ?>
                </tbody>
            </table>
            
        </div>
    
    
    <?PHP  
    }
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
                HitungTotalDariCekBox();
            }

            function HitungTotalDariCekBox() {
                
                var chk_arr1 =  document.getElementsByName('chk_jml1[]');
                var chklength1 = chk_arr1.length; 

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

                if (allnobr.length > 0) {
                    var lastIndex = allnobr.lastIndexOf(",");
                    allnobr = "("+allnobr.substring(0, lastIndex)+")";
                }

                //$("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
                $.ajax({
                    type:"post",
                    url:"module/mod_br_spdkas/viewdata.php?module=hitungtotalcekboxkas",
                    data:"unoidbr="+allnobr,
                    success:function(data){
                        //$("#loading2").html("");
                        document.getElementById('e_jmlusulan').value=data;
                    }
                });

            }
            
            
            
            function SelAllCheckBoxKB(nmbuton, data){
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
                HitungTotalKBDariCekBox();
            }
            
            function HitungTotalKBDariCekBox(){
                var chk_arr1 =  document.getElementsByName('chk_jml_kb[]');
                var chklength1 = chk_arr1.length; 

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

                if (allnobr.length > 0) {
                    var lastIndex = allnobr.lastIndexOf(",");
                    allnobr = "("+allnobr.substring(0, lastIndex)+")";
                }

                //$("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
                $.ajax({
                    type:"post",
                    url:"module/mod_br_spdkas/viewdata.php?module=hitungtotalcekboxkasbon",
                    data:"unoidbr="+allnobr,
                    success:function(data){
                        //$("#loading2").html("");
                        document.getElementById('e_jmlusulan_kb').value=data;
                    }
                });
            }
        </script>
            
    <?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp00");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    
}elseif ($_GET['module']=="xxxx"){
}elseif ($_GET['module']=="xxxx"){


}



?>
