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
    
    
    //$ftypetgl = " AND a.tgltrans BETWEEN '$mytgl1' AND '$mytgl2' ";
    
    $ftypetgl = "  a.tgl BETWEEN '$mytgl1' AND '$mytgl2' ";
    if ($pertipe=="T") $ftypetgl = " a.tgltrans BETWEEN '$mytgl1' AND '$mytgl2' ";
    
    
    //via surabaya
    $fil_viasby="";
    //if ($padvance=="C") $fil_viasby = " AND IFNULL(via,'')='Y' ";
    
    
    $fdivisi = "";
    if (!empty($pdivisi)) $fdivisi = " AND a.DIVISI='$pdivisi' ";
    
    
    $userid=$_SESSION['IDCARD'];
    $tr_nhidden=" class='divnone' ";
    if ($pact=="editdata") {
        $tr_nhidden="";
    }
    
    
    
    $filsudahada="";
    if ($pact=="editdata") $filsudahada=" AND idinput<> '$pidinput' ";
    
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.DSETH00_".$userid."_$now ";
    $tmp01 =" dbtemp.DSETH01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSETH02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSETH03_".$userid."_$now ";
    $tmp04 =" dbtemp.DSETH04_".$userid."_$now ";
    $tmp05 =" dbtemp.DSETH05_".$userid."_$now ";
    
    
    
    
        $query = "select distinct a.urutan, a.trans_ke, a.jml_adj, a.aktivitas1 ketadj1, a.idinput, IFNULL(a.bridinput,'') bridinput, a.amount, "
                . " CAST('' as CHAR(1)) as sinput FROM dbmaster.t_suratdana_br1 a JOIN "
                . " dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE 1=1 "
                . "  ";
        if ($pact=="editdata") {
            $query .= " AND ( (b.stsnonaktif<>'Y' and b.divisi<>'OTC' AND a.kodeinput IN ('E') ) OR a.idinput='$pidinput')";//and IFNULL(b.pilih,'')<>'N'
        }else{
            $query .= " AND b.stsnonaktif<>'Y' and b.divisi<>'OTC' AND a.kodeinput IN ('E') ";//and IFNULL(b.pilih,'')<>'N'
        }
        
        $query = "create TEMPORARY table $tmp00 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        if ($pact=="editdata") {
            $query = "UPDATE $tmp00 SET sinput='Y' WHERE idinput='$pidinput'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
        
        
        $query = "select * FROM $tmp00 WHERE IFNULL(sinput,'')='Y'";
        $query = "create TEMPORARY table $tmp04 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    
    
    $query = "select a.DIVISI divisi, a.klaimId, a.karyawanid, c.nama nama_karyawan,
        a.distid, b.nama nama_dist, a.aktivitas1, a.jumlah, a.tgl, a.tgltrans, 
        a.realisasi1, a.noslip, a.COA4, d.NAMA4, CAST('' as CHAR(1)) as sinput  
        from hrd.klaim a LEFT JOIN MKT.distrib0 b on a.distid=b.distid 
        LEFT JOIN hrd.karyawan c on a.karyawanid=c.karyawanId 
        LEFT JOIN dbmaster.coa_level4 d on a.COA4=d.COA4
        WHERE 1=1 ";
    
    if ($pact=="editdata") {
        $query .= " AND ( ($ftypetgl $fil_viasby AND klaimId NOT IN (select distinct IFNULL(bridinput,'') FROM $tmp00 WHERE IFNULL(sinput,'')<>'Y')) OR "
                . "  klaimId IN (select distinct IFNULL(bridinput,'') FROM $tmp04 WHERE IFNULL(sinput,'')='Y') )";
    }else{
        $query .= " AND a.klaimId NOT IN (select DISTINCT IFNULL(klaimId,'') FROM hrd.klaim_reject) AND $ftypetgl AND klaimId NOT IN (select distinct IFNULL(bridinput,'') FROM $tmp00 WHERE IFNULL(sinput,'')<>'Y')";
    }
    
    
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    if ($pact=="editdata") {
        
        $query = "select a.*, b.urutan, b.bridinput, b.trans_ke, b.jml_adj, b.ketadj1, b.amount from $tmp01 a LEFT JOIN $tmp04 b on a.klaimId=b.bridinput";
        $query = "create TEMPORARY table $tmp05 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp05 a JOIN $tmp04 b on a.klaimId=b.bridinput SET a.sinput=b.sinput";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    }else{
        
        $query = "select *, CAST(null as DECIMAL(10,0)) as urutan, "
                . " CAST('' as CHAR(20)) as bridinput, CAST('' as CHAR(2)) as trans_ke, CAST(null as DECIMAL(20,2)) jml_adj, "
                . " CAST('' as CHAR(10)) as ketadj1, CAST(NULL as DECIMAL(20,2)) as amount from $tmp01";
        $query = "create TEMPORARY table $tmp05 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    ?>

        <div class='form-group'>
            &nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-danger btn-xs' onclick='HitungTotalDariCekBoxKD()'>Hitung Ulang</button> <span class='required'></span>
        </div>
        <div class='x_content'>
            
            
            <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
                <thead>
                    <tr>
                        <th width='10px'><input type="checkbox" id="chkall1[]" name="chkall1[]" onclick="SelAllCheckBox('chkall1[]', 'chk_jml1[]')" value='select'></th>
                        <th width='10px'>No</th>
                        <th width='20px'>Urutan</th>
                        <th width='10px'>Non BCA</th>
                        <th align="center" nowrap>No. Slip</th>
                        <th align="center">Tgl. Transfer</th>
                        <th align="center" nowrap>Kode</th>
                        <th align="center" nowrap>Perkiraan</th>
                        <th align="center" nowrap>Supplier</th>
                        <th align="center" nowrap>Nama Pembuat</th>
                        <th align="center" nowrap>Keterangan</th>
                        <th align="center" nowrap>Jumlah</th>
                        <th align="center" nowrap <?PHP echo $tr_nhidden; ?>>Jml Input</th>
                        <th align="center" nowrap>ID</th>
                        <th align="center" nowrap>Tanggal</th>
                        <!--<th align="center" nowrap>Divisi</th>-->
                    </tr>
                </thead>
                <tbody>

                    <?PHP
                    
                        $purut_opt="<option value='' selected></option>";
                        for($xu=1;$xu<=60;$xu++) {
                            $purut_opt .="<option value='$xu'>$xu</option>";
                        }
                        
                        //harus ada, untuk cek doang
                        echo "<input type='hidden' id='chk_jml1[]' name='chk_jml1[]' value=''>"
                            . "<input type='hidden' id='chk_jml2[]' name='chk_jml2[]' value=''>";

                        $no=1;
                        $query = "select * from $tmp05 order by noslip, klaimId";
                        $tampil=mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            $pbrid = $row['klaimId'];
                            $pnoslip = $row['noslip'];
                            $ptgltrans = "";
                            if (!empty($row['tgltrans']) AND $row['tgltrans']<>"0000-00-00")
                                $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));
                            
                            $ptglinput =date("d-M-Y", strtotime($row['tgl']));

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


                            $ncheck_trans="";
                            $ncheck_sudah="";
                            if ($pact=="editdata") {
                                $pbrid_input = $row['bridinput'];
                                $purutan_no = $row['urutan'];
                                $ptrans_Ke = $row['trans_ke'];
                                $purut_opt="<option value='' selected></option>";
                                for($xu=1;$xu<=60;$xu++) {
                                    if ((double)$purutan_no==(double)$xu)
                                        $purut_opt .="<option value='$xu' selected>$xu</option>";
                                    else
                                        $purut_opt .="<option value='$xu'>$xu</option>";
                                }

                                if (!empty($pbrid_input)) $ncheck_sudah="checked";
                                if (!empty($ptrans_Ke)) $ncheck_trans="checked";
                            }

                            $pnmselecturut="<select id='cb_urut[$pbrid]' name='cb_urut[$pbrid]' onChange=\"cekBoxDataBR('cb_urut[$pbrid]', 'chk_jml1[$pbrid]')\">$purut_opt</select>";
                    
                            $chkbox_bca = "<input type='checkbox' id='chk_transke[$pbrid]' name='chk_transke[$pbrid]' value='NB' $ncheck_trans>";//NB = NON BCA
                            
                                    $pamount = $row['amount'];
                                    $pamount=number_format($pamount,0,",",",");

                                    $nnjumlahpilih=$pjumlah;
                                    $psinputsudah = $row['sinput'];
                                    $nval_chk="";
                                    if ($pact=="editdata") {
                                        if ($psinputsudah=="Y") {
                                            $nnjumlahpilih=$pamount;
                                            $nval_chk="checked";
                                        }
                                    }

                                    $pinput_jumlah="<input type='hidden' id='txt_jml[$pbrid]' name='txt_jml[$pbrid]' value='$nnjumlahpilih' Readonly>";
                            
                            
                            
                            $chkbox = "<input type='checkbox' id='chk_jml1[$pbrid]' name='chk_jml1[]' onclick='HitungTotalDariCekBoxKD()' value='$pbrid' $ncheck_sudah>";
                            
                            echo "<tr>";
                            echo "<td nowrap>$chkbox $pinput_jumlah<t/d>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap>$pnmselecturut</td>";
                            echo "<td nowrap>$chkbox_bca</td>";
                            echo "<td nowrap>$pnoslip</td>";
                            echo "<td nowrap>$ptgltrans</td>";
                            echo "<td nowrap>$pcoa</td>";
                            echo "<td>$pnmcoa</td>";
                            echo "<td>$pnmsup</td>";
                            echo "<td>$pnamakaryawan</td>";
                            echo "<td>$paktivitas1</td>";
                            echo "<td nowrap align='right'>$pjumlah</td>";
                            echo "<td nowrap align='right' $tr_nhidden>$pamount</td>";
                            echo "<td nowrap>$pbrid</td>";
                            echo "<td nowrap>$ptglinput</td>";
                            //echo "<td nowrap>$pdivisi</td>";
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

                //$("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
                $.ajax({
                    type:"post",
                    url:"module/mod_br_spd/viewdata.php?module=hitungtotalcekboxkd",
                    data:"unoidbr="+allnobr+"&uadvance="+eadvance,
                    success:function(data){
                        //$("#loading2").html("");
                        document.getElementById('e_jmlusulan').value=data;
                        document.getElementById('e_jmltotal').value=data;
                    }
                });

            }
        </script>
         
    <script type="text/javascript">
        function cekBoxDataBR(nmcb, nmchk) {
            var ecb_br=document.getElementById(nmcb).value;
            if (ecb_br!=""){
                document.getElementById(nmchk).checked = true;
            }else{
                document.getElementById(nmchk).checked = false;
            }
            HitungTotalDariCekBoxKD();
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
