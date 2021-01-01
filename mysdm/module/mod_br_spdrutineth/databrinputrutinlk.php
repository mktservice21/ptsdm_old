<?php
session_start();
if ($_GET['module']=="viewdatabrinput"){
    
    ?>
    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    <script src="js/inputmask.js"></script>
    <?PHP
    
    include "../../config/koneksimysqli.php";
    $totalinput=0;
    
    
    $pact=$_POST['uact'];
    $pidinput=$_POST['eidinput'];
    $pdivisi=$_POST['udivisi'];
    
    $tgl01=$_POST['utgl'];
    $periode1= date("Y-m", strtotime($tgl01));
    
    
    $userid=$_SESSION['IDCARD'];
    
    $fdivisi = "";
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.DSETHF00_".$userid."_$now ";
    $tmp01 =" dbtemp.DSETHF01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSETHF02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSETHF03_".$userid."_$now ";
    $tmp04 =" dbtemp.DSETHF04_".$userid."_$now ";
    $tmp05 =" dbtemp.DSETHF05_".$userid."_$now ";
        
    
    $nfilter_sudahada="";//biar tidak ada yang double (tp ditutup dulu) karena ada pengajuan yang sama (klaim)
    $nfilterid_sudah="";
    if ($pact=="editdata") {
        $nfilterid_sudah=" AND a.idinput <> '$pidinput'";
    }
    $query = "select distinct a.bridinput from dbmaster.t_suratdana_br1 a JOIN dbmaster.t_suratdana_br b "
            . " on a.idinput=b.idinput WHERE b.stsnonaktif<>'Y' AND a.kodeinput='S' $nfilterid_sudah";
    $query = "create TEMPORARY table $tmp00 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    //tutup dulu, keterangan diatas
    //$nfilter_sudahada=" AND idsewa NOT IN (select distinct IFNULL(bridinput,'') bridinput FROM $tmp00)";
    
        
    $query = "select idsewa, karyawanid, nama, divisi, areaid, nama_area, tglmulai, tglakhir, jumlah, "
            . " keterangan from dbmaster.v_sewa WHERE IFNULL(stsnonaktif,'')<>'Y' AND "
            . " '$periode1' BETWEEN DATE_FORMAT(tglmulai,'%Y-%m') AND DATE_FORMAT(tglakhir,'%Y-%m') ";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    if ($pact=="editdata") {
        
        $query = "select * from dbmaster.t_suratdana_br1 WHERE idinput='$pidinput'";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "select a.*, b.urutan, b.bridinput, b.trans_ke from $tmp01 a LEFT JOIN $tmp02 b on a.idsewa=b.bridinput";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    }else{
        
        $query = "select *, CAST(null as DECIMAL(10,0)) as urutan, CAST('' as CHAR(20)) as bridinput, CAST('' as CHAR(2)) as trans_ke from $tmp01";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    ?>


    
    <div class='form-group'>
        &nbsp;&nbsp;&nbsp;
        <button type='button' class='btn btn-danger btn-xs' onclick='HitungTotalDariCekBox()'>Hitung Jumlah</button> 
        <span class='required'></span>
    </div>
    
    
    <div class='x_content'>
        
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='10px'><input type="checkbox" id="chkall1[]" name="chkall1[]" onclick="SelAllCheckBox('chkall1[]', 'chk_jml1[]')" value='select'></th>
                    <th width='10px'>No</th>
                    <th align="center" nowrap>ID</th>
                    <th align="center">Karyawan</th>
                    <th align="center">Area</th>
                    <th align="center" nowrap>Tgl. Mulai</th>
                    <th align="center" nowrap>Tgl. Akhir</th>
                    <th align="center" nowrap>Jumlah</th>
                    <th align="center" nowrap>Keterangan</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                
                $no=1;
                $query = "select * from $tmp03 order by idsewa";
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pbrid = $row['idsewa'];

                    $ptglmulai =date("d-M-Y", strtotime($row['tglmulai']));
                    $ptglakhir =date("d-M-Y", strtotime($row['tglakhir']));

                    $pnamakaryawan = $row['nama'];
                    $pnamaarea = $row['nama_area'];
                    $paktivitas1 = $row['keterangan'];

                    $pjumlah = $row['jumlah'];
                    $pjumlah=number_format($pjumlah,0,",",",");


                    $ncheck_sudah="";
                    if ($pact=="editdata") {
                        $pbrid_input = $row['bridinput'];
                        
                        if (!empty($pbrid_input)) $ncheck_sudah="checked";
                    }
                    
                    $chkbox = "<input type='checkbox' id='chk_jml1[$pbrid]' name='chk_jml1[]' value='$pbrid' onclick='HitungTotalDariCekBox()' $ncheck_sudah>";

                    echo "<tr>";
                    echo "<td nowrap>$chkbox<t/d>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pbrid</td>";
                    echo "<td>$pnamakaryawan</td>";
                    echo "<td>$pnamaarea</td>";
                    echo "<td nowrap>$ptglmulai</td>";
                    echo "<td nowrap>$ptglakhir</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td>$paktivitas1</td>";
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
    
    <script type="text/javascript">
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
            var eadvance="";
            
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
            
            //$("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
            $.ajax({
                type:"post",
                url:"module/mod_br_spdrutineth/viewdata.php?module=hitungtotalcekboxbr",
                data:"unoidbr="+allnobr+"&uadvance="+eadvance,
                success:function(data){
                    //$("#loading2").html("");
                    document.getElementById('e_jmlusulan').value=data;
                }
            });

        }


        $(document).ready(function() {
            var table = $('#datatable2').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [7] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7] }//nowrap

                ],
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );

        } );
    </script>
    
    <script type="text/javascript">
        function cekBoxDataBR(nmcb, nmchk) {
            var ecb_br=document.getElementById(nmcb).value;
            if (ecb_br!=""){
                document.getElementById(nmchk).checked = true;
            }else{
                document.getElementById(nmchk).checked = false;
            }
            HitungTotalDariCekBox();
            //alert(nmcb+", ada, "+nmchk);
        }
    </script>
    
    <?PHP

    hapusdata:
        mysqli_query($cnmy, "drop TEMPORARY table $tmp00");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp04");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp05");
    
    
}elseif ($_GET['module']=="xxxx"){
}elseif ($_GET['module']=="xxxx"){


}



?>
