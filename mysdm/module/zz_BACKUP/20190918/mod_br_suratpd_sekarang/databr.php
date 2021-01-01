<?php
session_start();
if ($_GET['module']=="viewdataanne"){
    
    ?>
    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    <script src="js/inputmask.js"></script>
    <?PHP
    
    include "../../config/koneksimysqli.php";
    $totalinput=0;
    $pket=$_GET['ket'];
    
    $pact=$_POST['uact'];
    $pkodeid=$_POST['ukodeid'];
    $pnodivadj=$_POST['unodivadj'];
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
    
    $filterlampiran="";
    
    if (!empty($jenis)) $filterlampiran = " and case when ifnull(lampiran,'N')='' then 'N' else lampiran end ='$jenis' ";
    
    $fadvance="";
    /*
    $fadvance= " and case when ifnull(ca,'N')='' then 'N' else ca end ='Y' ";
    if ($padvance=="A") $fadvance= " and case when ifnull(ca,'N')='' then 'N' else ca end ='N' ";
    elseif ($padvance=="B") $fadvance= " and case when ifnull(ca,'N')='' then 'N' else ca end ='Y' ";
    */
    
    
    $ftypetgl = " AND  tgl BETWEEN '$mytgl1' AND '$mytgl2' ";
    if ($pertipe=="T") $ftypetgl = " AND tgltrans BETWEEN '$mytgl1' AND '$mytgl2' ";
    if ($pertipe=="S") $ftypetgl = " AND tglrpsby BETWEEN '$mytgl1' AND '$mytgl2' ";
    
    $fdivisi = "";
    if (!empty($pdivisi)) {
        $fdivisi = " AND divprodid='$pdivisi' ";
    }
    $userid=$_SESSION['IDCARD'];
    
    
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
    
    $filter_kwitasnsi=" AND IFNULL(jenis_rpt,'')<>'B' ";
    if ($padvance=="B") $filter_kwitasnsi="";
    
    if ($pkodeid=="7") {
        
        $query = "select distinct a.bridinput from dbmaster.t_suratdana_br1 a JOIN dbmaster.t_suratdana_br b "
                . " on a.idinput=b.idinput WHERE b.nodivisi='$pnodivadj'";
        $query = "create TEMPORARY table $tmp00 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "select * from hrd.br0 WHERE brId IN (select distinct IFNULL(bridinput,'') bridinput FROM $tmp00)";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "select * from dbmaster.t_suratdana_br1 WHERE idinput='$pidinput'";
        $query = "create TEMPORARY table $tmp04 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        
    }else{
        
        $query = "select distinct a.bridinput from dbmaster.t_suratdana_br1 a JOIN dbmaster.t_suratdana_br b "
                . " on a.idinput=b.idinput WHERE b.stsnonaktif<>'Y' AND a.kodeinput='C' $filter_kwitasnsi $nfilterid_sudah";
        $query = "create TEMPORARY table $tmp00 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        if ($pact=="editdata") {

            $query = "select * from dbmaster.t_suratdana_br1 WHERE idinput='$pidinput'";
            $query = "create TEMPORARY table $tmp04 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            $query = "DELETE FROM $tmp00 WHERE bridinput IN (select distinct IFNULL(bridinput,'') bridinput FROM $tmp04)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        }


        $nfilter_sudahada=" AND brid NOT IN (select distinct IFNULL(bridinput,'') bridinput FROM $tmp00)";

        if ($pact=="editdata") {
            $filter_sudah_cek=" brid IN (select distinct IFNULL(bridinput,'') bridinput FROM $tmp04) ";

            $query = "select * from hrd.br0 WHERE (1=1 $ftypetgl $filterlampiran $fadvance $fdivisi AND "
                    . " brid NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
                    . " COA4 IN (SELECT DISTINCT IFNULL(COA4,'') COA4 FROM dbmaster.coa_wewenang WHERE karyawanId='$userid') $nfilter_sudahada)"
                    . " OR $filter_sudah_cek";

        }else{

            $query = "select * from hrd.br0 WHERE 1=1 $ftypetgl $filterlampiran $fadvance $fdivisi AND "
                    . " brid NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
                    . " COA4 IN (SELECT DISTINCT IFNULL(COA4,'') COA4 FROM dbmaster.coa_wewenang WHERE karyawanId='$userid') $nfilter_sudahada";
        }
    
        
    
    
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    
    $query = "select dokterId, nama FROM hrd.dokter WHERE dokterId IN "
            . " (select distinct dokterId FROM $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.karyawanId, c.nama nama_karyawan, a.dokterId, a.dokter, b.nama nama_dokter,
        a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, jumlah1, a.realisasi2 from $tmp01 a 
        LEFT JOIN $tmp02 b on a.dokterId=b.dokterId
        LEFT JOIN hrd.karyawan c on a.karyawanId=c.karyawanId";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if ($padvance=="A"){
    }else{
        mysqli_query($cnmy, "UPDATE $tmp03 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)>0");
    }
            

    if ($pact=="editdata") {
        
        $query = "select a.*, b.urutan, b.bridinput, b.amount, b.jml_adj, b.aktivitas1 ketadj1 from $tmp03 a LEFT JOIN $tmp04 b on a.brId=b.bridinput";
        $query = "create TEMPORARY table $tmp05 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        if ($padvance=="B"){
            mysqli_query($cnmy, "UPDATE $tmp05 SET jumlah=amount WHERE IFNULL(bridinput,'')<>''");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
    
    }else{
        
        $query = "select *, CAST(null as DECIMAL(10,0)) as urutan, CAST('' as CHAR(20)) as bridinput from $tmp03";
        $query = "create TEMPORARY table $tmp05 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    
    /*
    $query = "DELETE FROM dbmaster.tmp_input_spd_br WHERE (karyawanid='$_SESSION[IDCARD]' AND session_id='$_SESSION[IDSESI]') OR ( tgl_proses < CURRENT_DATE() )";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "INSERT INTO dbmaster.tmp_input_spd_br (session_id, karyawanid, tgl_proses, "
            . " transfer_ke, urutan, brid, jumlah, noslip, tgltransfer, tglbr, nama_karyawan, nama_dokter, keterangan, nama_realisasi, divisi)"
            . " select '$_SESSION[IDSESI]' as session_id, '$_SESSION[IDCARD]' as karyawanid, CURRENT_DATE(), "
            . " '' as transfer_ke, urutan, brId, jumlah, noslip, tgltrans, tgl, "
            . " nama_karyawan, nama_dokter, aktivitas1, realisasi1, divprodid "
            . " FROM $tmp05";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    */
    
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
                    <th width='20px'>Urutan</th>
                    <th width='20px'>Adjusment</th>
                    <th align="center" nowrap>No. Slip</th>
                    <th align="center">Tgl. Transfer</th>
                    <th align="center">Tgl. Input</th>
                    <th align="center" nowrap>Nama Pembuat</th>
                    <th align="center" nowrap>Nama Dokter</th>
                    <th align="center" nowrap>Keterangan</th>
                    <th align="center" nowrap>Nama Realisasi</th>
                    <th align="center" nowrap>Jumlah</th>
                    <!--<th align="center" nowrap>Divisi</th>-->
                    <th align="center" nowrap>ID</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $purut_opt="<option value='' selected></option>";
                for($xu=1;$xu<=60;$xu++) {
                    $purut_opt .="<option value='$xu'>$xu</option>";
                }
                
                $no=1;
                $query = "select * from $tmp05 order by divprodid, noslip, realisasi1, nama_karyawan, brId";
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pbrid = $row['brId'];
                    $pnoslip = $row['noslip'];
                    $ptgltrans = "";
                    if (!empty($row['tgltrans']) AND $row['tgltrans']<>"0000-00-00")
                        $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));

                    $ptglinput =date("d-M-Y", strtotime($row['tgl']));

                    $pnamakaryawan = $row['nama_karyawan'];
                    $piddokter = $row['dokterId'];
                    $pnmdokter = $row['nama_dokter'];
                    if (empty($pnmdokter)) $pnmdokter = $row['dokter'];
                    $paktivitas1 = $row['aktivitas1'];
                    $paktivitas2 = $row['aktivitas2'];
                    $prealisasi1 = $row['realisasi1'];
                    $pdivisi = $row['divprodid'];

                    $pjumlah = $row['jumlah'];
                    $pjumlah=number_format($pjumlah,0,",",",");
                    

                    $pjmlreal = $row['realisasi2'];
                    if (!empty($row['realisasi2']))
                        $pjmlreal=number_format($row['realisasi2'],0,",",",");

                    
                    $ncheck_sudah="";
                    if ($pact=="editdata") {
                        $pbrid_input = $row['bridinput'];
                        $purutan_no = $row['urutan'];
                        $purut_opt="<option value='' selected></option>";
                        for($xu=1;$xu<=60;$xu++) {
                            if ((double)$purutan_no==(double)$xu)
                                $purut_opt .="<option value='$xu' selected>$xu</option>";
                            else
                                $purut_opt .="<option value='$xu'>$xu</option>";
                        }
                        
                        if (!empty($pbrid_input)) $ncheck_sudah="checked";
                    }
                    $pnmselecturut="<select id='cb_urut[$pbrid]' name='cb_urut[$pbrid]' onChange=\"cekBoxDataBR('$pbrid', 'cb_urut[$pbrid]', 'chk_jml1[$pbrid]', 'chk_adj[$pbrid]', 'txt_adj[$pbrid]', 'txt_adj_ket[$pbrid]')\">$purut_opt</select>";
                    
                    //$chkbox = "<input type='checkbox' id='chk_jml1[$pbrid]' name='chk_jml1[]' value='$pbrid' onclick='HitungTotalDariCekBox()' $ncheck_sudah>";
                    $chkbox = "<input type='checkbox' id='chk_jml1[$pbrid]' name='chk_jml1[]' value='$pbrid' onclick=\"CentangCekBoxDataBR('$pbrid', 'cb_urut[$pbrid]', 'chk_jml1[$pbrid]', 'chk_adj[$pbrid]', 'txt_adj[$pbrid]')\" $ncheck_sudah>";

                    $pinput_jumlah="<input type='hidden' id='txt_jml[$pbrid]' name='txt_jml[$pbrid]' value='$pjumlah' Readonly>";

                    
                    $pket_adj=$row['ketadj1'];
                    $jml_adj = $row['jml_adj'];
                    $jml_adj=number_format($jml_adj,0,",",",");
                    
                    $ncheck_sudah_adj="";
                    $nvisible_txtadj="visibility:hidden";
                    if ((double)$jml_adj<>0) {
                        $ncheck_sudah_adj="checked";
                        $nvisible_txtadj="";
                    }
                    
                    $chkbox_adj = "<input type='checkbox' id='chk_adj[$pbrid]' name='chk_adj[$pbrid]' value='$pbrid' onclick=\"ShowHideTextBoxAdj('$pbrid', 'cb_urut[$pbrid]', 'chk_jml1[$pbrid]', 'chk_adj[$pbrid]', 'txt_adj[$pbrid]', 'txt_adj_ket[$pbrid]')\" $ncheck_sudah_adj>";
                    $txtbox_adj = "<input type='text' style='$nvisible_txtadj' id='txt_adj[$pbrid]' name='txt_adj[$pbrid]' value='$jml_adj' onblur=\"CentangCekBoxDataBR('$pbrid', 'cb_urut[$pbrid]', 'chk_jml1[$pbrid]', 'chk_adj[$pbrid]', 'txt_adj[$pbrid]', 'txt_adj_ket[$pbrid]')\" size='7px' class='input-sm inputmaskrp2'>";
                    $txtbox_adj_ket = "<input type='text' style='$nvisible_txtadj' "
                            . " id='txt_adj_ket[$pbrid]' name='txt_adj_ket[$pbrid]' value='$pket_adj' "
                            . " onblur=\"CentangCekBoxDataBR('$pbrid', 'cb_urut[$pbrid]', 'chk_jml1[$pbrid]', 'chk_adj[$pbrid]', 'txt_adj[$pbrid]', 'txt_adj_ket[$pbrid]')\" "
                            . " size='30px' class='input-sm'>";
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$chkbox<t/d>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnmselecturut $pinput_jumlah</td>";
                    echo "<td nowrap>$chkbox_adj $txtbox_adj<br/>"//&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            . "$txtbox_adj_ket</td>";
                    echo "<td nowrap>$pnoslip</td>";
                    echo "<td nowrap>$ptgltrans</td>";
                    echo "<td nowrap>$ptglinput</td>";
                    echo "<td>$pnamakaryawan</td>";
                    echo "<td>$pnmdokter</td>";
                    echo "<td>$paktivitas1</td>";
                    echo "<td>$prealisasi1</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    //echo "<td nowrap>$pdivisi</td>";
                    echo "<td nowrap>$pbrid</td>";
                    echo "</tr>";

                    $no++;
                }
            ?>
            </tbody>
        </table>
        
    </div>
    
    
    
    <div hidden class='form-group'>
        &nbsp;&nbsp;&nbsp;
        <button type='button' class='btn btn-success btn-xs' onclick='sortTable()'>Sort Tabel</button> 
        <span class='required'></span>
    </div>
    
    <div class='x_content'>
        
        <table id='datatable_nn' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th hidden>No</th>
                    <th width='10px'>Urutan</th>
                    <th width='10px'>Adjusment</th>
                    <th align="center" nowrap>No. Slip</th>
                    <th align="center">Tgl. Transfer</th>
                    <th align="center">Tgl. Input</th>
                    <th align="center" nowrap>Nama Pembuat</th>
                    <th align="center" nowrap>Nama Dokter</th>
                    <th align="center" nowrap>Keterangan</th>
                    <th align="center" nowrap>Nama Realisasi</th>
                    <th align="center" nowrap>Jumlah</th>
                    <th align="center" nowrap>ID</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $purut_opt="<option value='' selected></option>";
                for($xu=1;$xu<=60;$xu++) {
                    $purut_opt .="<option value='$xu'>$xu</option>";
                }
                
                $no=1;
                $query = "select * from $tmp05 order by urutan, divprodid, noslip, realisasi1, nama_karyawan, brId";
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pbrid = $row['brId'];
                    $pnoslip = $row['noslip'];
                    $ptgltrans = "";
                    if (!empty($row['tgltrans']) AND $row['tgltrans']<>"0000-00-00")
                        $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));

                    $ptglinput =date("d-M-Y", strtotime($row['tgl']));

                    $pnamakaryawan = $row['nama_karyawan'];
                    $piddokter = $row['dokterId'];
                    $pnmdokter = $row['nama_dokter'];
                    if (empty($pnmdokter)) $pnmdokter = $row['dokter'];
                    $paktivitas1 = $row['aktivitas1'];
                    $paktivitas2 = $row['aktivitas2'];
                    $prealisasi1 = $row['realisasi1'];
                    $pdivisi = $row['divprodid'];

                    $pjumlah = $row['jumlah'];
                    $pjumlah=number_format($pjumlah,0,",",",");

                    $pjmlreal = $row['realisasi2'];
                    if (!empty($row['realisasi2']))
                        $pjmlreal=number_format($row['realisasi2'],0,",",",");
                    
                    $no_nurut_=$no;
                    $nurut_="";
                    $pbrid_input="";
                    if ($pact=="editdata") {
                        $nurut_ = $row['urutan'];
                        $no_nurut_=$row['urutan'];
                        
                        $pbrid_input = $row['bridinput'];
                    }
                    
                    if (strlen($no_nurut_)==1) $no_nurut_="0".$no_nurut_;
                    
                    $tb_style=" style='display : none;' ";
                    if (!empty($pbrid_input)) $tb_style=" ";
                    $nm_tr_tabel="tb_".$pbrid;
                    
                    
                    $jml_adj = $row['jml_adj'];
                    $jml_adj=number_format($jml_adj,0,",",",");
                    if ((double)$jml_adj==0) $jml_adj="";
                    
                    echo "<tr id='$nm_tr_tabel' $tb_style>";
                    echo "<td hidden>$no_nurut_</td>";
                    echo "<td nowrap>$nurut_</td>";
                    echo "<td nowrap>$jml_adj</td>";
                    echo "<td nowrap>$pnoslip</td>";
                    echo "<td nowrap>$ptgltrans</td>";
                    echo "<td nowrap>$ptglinput</td>";
                    echo "<td>$pnamakaryawan</td>";
                    echo "<td>$pnmdokter</td>";
                    echo "<td>$paktivitas1</td>";
                    echo "<td>$prealisasi1</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td nowrap>$pbrid</td>";
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
            HitungTotalDariCekBox();
        }

        function HitungTotalDariCekBox() {
            var ekode =document.getElementById('cb_kode').value;
            if (ekode=="7") {
                return false;
            }
            
            var chk_arr1 =  document.getElementsByName('chk_jml1[]');
            var chklength1 = chk_arr1.length;
            var newchar = '';
            
            var nTotal_="0";
            for(k=0;k< chklength1;k++)
            {
                if (chk_arr1[k].checked == true) {
                    var kata = chk_arr1[k].value;
                    var fields = kata.split('-');    
                    var anm_jml="txt_jml["+fields[0]+"]";
                    var ajml=document.getElementById(anm_jml).value;
                    ajml = ajml.split(',').join(newchar);
                    
                    nTotal_ =parseInt(nTotal_)+parseInt(ajml);
                }
            }
            
            document.getElementById('e_jmlusulan').value=nTotal_;
            
            
            /*
            
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
                url:"module/mod_br_suratpd/viewdata.php?module=hitungtotalcekboxbr",
                data:"unoidbr="+allnobr+"&uadvance="+eadvance,
                success:function(data){
                    //$("#loading2").html("");
                    document.getElementById('e_jmlusulan').value=data;
                }
            });
            */
               
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
        function cekBoxDataBR(nidbr, nmcb, nmchk, nchkadj, ntxtadj, ntxtketadj) {
            var ecb_br=document.getElementById(nmcb).value;
            if (ecb_br!=""){
                document.getElementById(nmchk).checked = true;
            }else{
                document.getElementById(nmchk).checked = false;
            }
            
            HitungTotalDariCekBox();
            
            BukaTutupTabelInput(nidbr, nmchk, ecb_br, nchkadj, ntxtadj, ntxtketadj);
            //alert(nmcb+", ada, "+nmchk);
        }
        
        function CentangCekBoxDataBR(nidbr, nmcb, nmchk, nchkadj, ntxtadj, ntxtketadj) {
            HitungTotalDariCekBox();
            
            var ecb_br=document.getElementById(nmcb).value;
            BukaTutupTabelInput(nidbr, nmchk, ecb_br, nchkadj, ntxtadj, ntxtketadj);
        }
        
        function BukaTutupTabelInput(nidbr, nmchk, nurutan, nchkadj, ntxtadj, ntxtketadj) {
            var nm_r = "tb_"+nidbr;
            var nrow = document.getElementById(nm_r);
            if (document.getElementById(nmchk).checked == true) {
                nrow.style.display = '';
            }else{
                nrow.style.display = 'none';
            }
            
            var ninput_urut=nurutan;
            if (nurutan.length==1) ninput_urut="0"+""+nurutan;
            document.getElementById(nm_r).cells.item(0).innerHTML=ninput_urut;
            document.getElementById(nm_r).cells.item(1).innerHTML=nurutan;
            if (document.getElementById(nchkadj).checked == true) {
                if (document.getElementById(ntxtadj).value=="0"){
                    document.getElementById(nm_r).cells.item(2).innerHTML="";
                }else{
                    document.getElementById(nm_r).cells.item(2).innerHTML=document.getElementById(ntxtadj).value;
                }
            }else{
                document.getElementById(nm_r).cells.item(2).innerHTML="";
            }
            sortTable();
        }
        
        

        function sortTable() {
            var table, rows, switching, i, x, y, shouldSwitch;
            table = document.getElementById("datatable_nn");
            switching = true;
            /*Make a loop that will continue until
            no switching has been done:*/
            while (switching) {
                //start by saying: no switching is done:
                switching = false;
                rows = table.rows;
                /*Loop through all table rows (except the
                first, which contains table headers):*/
                for (i = 1; i < (rows.length - 1); i++) {
                    //start by saying there should be no switching:
                    shouldSwitch = false;
                    /*Get the two elements you want to compare,
                    one from current row and one from the next:*/
                    x = rows[i].getElementsByTagName("TD")[0];
                    y = rows[i + 1].getElementsByTagName("TD")[0];
                    //check if the two rows should switch place:
                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                      //if so, mark as a switch and break the loop:
                      shouldSwitch = true;
                      break;
                    }
                }
                
                if (shouldSwitch) {
                    /*If a switch has been marked, make the switch
                    and mark that a switch has been done:*/
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                }
                
            }
        }

        function ShowHideTextBoxAdj(nidbr, nmcb, nmchk, nchkajd, ntxtadj, ntxtketadj){
            if (document.getElementById(nchkajd).checked == true) {
                document.getElementById(ntxtadj).style.visibility = 'visible';
                document.getElementById(ntxtketadj).style.visibility = 'visible';
            }else{
                document.getElementById(ntxtadj).style.visibility = 'hidden';
                document.getElementById(ntxtketadj).style.visibility = 'hidden';
            }
            CentangCekBoxDataBR(nidbr, nmcb, nmchk, nchkajd, ntxtadj, ntxtketadj);
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
