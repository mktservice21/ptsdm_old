<?php
session_start();
if ($_GET['module']=="viewdatakd"){
    
    ?>
    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    <script src="js/inputmask.js"></script>
    <?PHP
    
    include "../../config/koneksimysqli.php";
    $totalinput=0;
    $pket=$_GET['ket'];
    
    $pact=$_POST['uact'];
    $pidinput=$_POST['eidinput'];
    $pdivisi=$_POST['udivisi'];
    
    $tgl01=$_POST['utgl'];
    $periode1= date("Y-m-d", strtotime($tgl01));
    
    $date1=$_POST['uper1'];
    $mytgl1= date("Y-m-d", strtotime($date1));
    
    $date2=$_POST['uper2'];
    $mytgl2= date("Y-m-d", strtotime($date2));
    
    $jenis=$_POST['ujenis'];
    $pertipe=$_POST['upertipe'];
    $padvance=$_POST['uadvance'];
    
    
    $ftypetgl = " AND a.tgltrans BETWEEN '$mytgl1' AND '$mytgl2' ";
    
    
    $fdivisi = "";
    if (!empty($pdivisi)) $fdivisi = " AND a.DIVISI='$pdivisi' ";
    
    
    $userid=$_SESSION['IDCARD'];
    
    $filsudahada="";
    if ($pact=="editdata") $filsudahada=" AND idinput<> '$pidinput' ";
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DSETH01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSETH02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSETH03_".$userid."_$now ";
    
    //cari yang sudah ada
    $ntglpilih = date('Y-m-d', strtotime($tgl01));
    $query = "select distinct IFNULL(bridinput,'') bridinput FROM dbmaster.t_suratdana_br1 WHERE idinput in "
            . "(select distinct IFNULL(idinput,'') idinput from dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND divisi<>'$pdivisi' AND "
            . "tgl='$ntglpilih' AND jenis_rpt='K')";
    
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    //END 
    
    $query = "select a.DIVISI divisi, a.klaimId, a.karyawanid, c.nama nama_karyawan,
        a.distid, b.nama nama_dist, a.aktivitas1, a.jumlah, a.tgl, a.tgltrans, 
        a.realisasi1, a.noslip, a.COA4, d.NAMA4  
        from hrd.klaim a LEFT JOIN MKT.distrib0 b on a.distid=b.distid 
        LEFT JOIN hrd.karyawan c on a.karyawanid=c.karyawanId 
        LEFT JOIN dbmaster.coa_level4 d on a.COA4=d.COA4
        WHERE a.klaimId NOT IN (select DISTINCT IFNULL(klaimId,'') FROM hrd.klaim_reject) $ftypetgl AND 
         a.klaimId NOT IN (select DISTINCT IFNULL(bridinput,'') bridinput FROM $tmp03) ";
    
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    ?>

        <div class='form-group'>
            &nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-danger btn-xs' onclick='HitungTotalDariCekBoxKD()'>Hitung Ulang</button> <span class='required'></span>
        </div>
        <div class='x_content'>
            
            
            <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
                <thead>
                    <tr>
                        <th width='10px'><input type="checkbox" id="chkall1[]" name="chkall1[]" onclick="SelAllCheckBox('chkall1[]', 'chk_jml1[]')" checked></th>
                        <th width='10px'>No</th>
                        <th align="center" nowrap>No. Slip</th>
                        <th align="center">Tgl. Transfer</th>
                        <th align="center" nowrap>Kode</th>
                        <th align="center" nowrap>Perkiraan</th>
                        <th align="center" nowrap>Supplier</th>
                        <th align="center" nowrap>Nama Pembuat</th>
                        <th align="center" nowrap>Keterangan</th>
                        <th align="center" nowrap>Jumlah</th>
                    </tr>
                </thead>
                <tbody>

                    <?PHP
                        //harus ada, untuk cek doang
                        echo "<input type='hidden' id='chk_jml1[]' name='chk_jml1[]' value=''>"
                            . "<input type='hidden' id='chk_jml2[]' name='chk_jml2[]' value=''>";

                        $no=1;
                        $query = "select * from $tmp01 order by noslip, klaimId";
                        $tampil=mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            $pbrid = $row['klaimId'];
                            $pnoslip = $row['noslip'];
                            $ptgltrans = "";
                            if (!empty($row['tgltrans']))
                                $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));

                            $pnamakaryawan = $row['nama_karyawan'];
                            $pnmsup = $row['nama_dist'];
                            $paktivitas1 = $row['aktivitas1'];
                            $pcoa = $row['COA4'];
                            $pnmcoa = $row['NAMA4'];
                            
                            
                            /*
                            $pjml1=0;
                            if (!empty($row['jumlah1']))
                                $pjml1 = $row['jumlah1'];
                            
                            
                            if ($padvance=="A" OR (DOUBLE)$pjml1<=0)
                                $pjumlah = $row['jumlah'];
                            else{
                                $pjumlah=$pjml1;
                            }
                             * 
                             */
                            
                            $pjumlah = $row['jumlah'];
                            $pjumlah=number_format($pjumlah,0,",",",");


                            $chkbox = "<input type='checkbox' id='chk_jml1[]' name='chk_jml1[]' value='$pbrid' checked> ";
                            echo "<tr>";
                            echo "<td nowrap>$chkbox<t/d>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap>$pnoslip</td>";
                            echo "<td nowrap>$ptgltrans</td>";
                            echo "<td>$pcoa</td>";
                            echo "<td>$pnmcoa</td>";
                            echo "<td>$pnmsup</td>";
                            echo "<td>$pnamakaryawan</td>";
                            echo "<td>$paktivitas1</td>";
                            echo "<td nowrap align='right'>$pjumlah</td>";
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
                HitungTotalDariCekBoxKD();
            }

            function HitungTotalDariCekBoxKD() {
                var eadvance=document.getElementById('cb_jenispilih').value;
                
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
                    url:"module/mod_br_spd/viewdata.php?module=hitungtotalcekboxkd",
                    data:"unoidbr="+allnobr+"&uadvance="+eadvance,
                    success:function(data){
                        $("#loading2").html("");
                        document.getElementById('e_jmlusulan').value=data;
                    }
                });

            }
        </script>
            
    <?PHP
    
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    
}elseif ($_GET['module']=="xxxx"){
}elseif ($_GET['module']=="xxxx"){


}



?>
