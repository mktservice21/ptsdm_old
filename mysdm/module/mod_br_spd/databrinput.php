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
    
    $filterlampiran="";
    
    if (!empty($jenis)) $filterlampiran = " and case when ifnull(lampiran,'N')='' then 'N' else lampiran end ='$jenis' ";
    
    $fadvance="";
    /*
    $fadvance= " and case when ifnull(ca,'N')='' then 'N' else ca end ='Y' ";
    if ($padvance=="A") $fadvance= " and case when ifnull(ca,'N')='' then 'N' else ca end ='N' ";
    elseif ($padvance=="B") $fadvance= " and case when ifnull(ca,'N')='' then 'N' else ca end ='Y' ";
    */
    
    
    $userid=$_SESSION['IDCARD'];
    $tr_nhidden=" class='divnone' ";
    if ($pact=="editdata") {
        $tr_nhidden="";
    }
    
    
    
    $ftypetgl = " tgl BETWEEN '$mytgl1' AND '$mytgl2' ";
    if ($pertipe=="T") $ftypetgl = " tgltrans BETWEEN '$mytgl1' AND '$mytgl2' ";
    if ($pertipe=="S") $ftypetgl = " tglrpsby BETWEEN '$mytgl1' AND '$mytgl2' ";
    
    //via surabaya
    $fil_viasby="";
    if ($padvance=="V") $fil_viasby = " AND IFNULL(via,'')='Y' ";
    
    
    
    $fdivisi = "";
    if (!empty($pdivisi)) {
        $fdivisi = " AND divprodid='$pdivisi' ";
    }
    
    $filtercoa_wwn="";
    $query = "SELECT DISTINCT IFNULL(COA4,'') COA4 FROM dbmaster.coa_wewenang WHERE karyawanId='$userid'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        while ($ca= mysqli_fetch_array($tampil)) {
            $ncoa=$ca['COA4'];
            
            if (!empty($ncoa)) $filtercoa_wwn .="'".$ncoa."',";
        }
        if (!empty($filtercoa_wwn)) $filtercoa_wwn=" AND COA4 in (".substr($filtercoa_wwn, 0, -1).")";
    }
    
    
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.DSETHF00_".$userid."_$now ";
    $tmp01 =" dbtemp.DSETHF01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSETHF02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSETHF03_".$userid."_$now ";
    $tmp04 =" dbtemp.DSETHF04_".$userid."_$now ";
    $tmp05 =" dbtemp.DSETHF05_".$userid."_$now ";
        
    
        $query = "select distinct a.urutan, a.trans_ke, a.jml_adj, a.aktivitas1 ketadj1, a.idinput, IFNULL(a.bridinput,'') bridinput, a.amount, "
                . " CAST('' as CHAR(1)) as sinput FROM dbmaster.t_suratdana_br1 a JOIN "
                . " dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE 1=1 "
                . "  ";
        if ($pact=="editdata") {
            //$query .= " AND ( (b.stsnonaktif<>'Y' and b.divisi<>'OTC' AND a.kodeinput IN ('A', 'B', 'C') and IFNULL(b.pilih,'')<>'N') OR a.idinput='$pidinput')";
            if ($padvance=="B") {
                $query .= " AND ( (b.stsnonaktif<>'Y' and b.divisi<>'OTC' AND a.kodeinput IN ('A', 'B', 'C')) OR a.idinput='$pidinput')";
            }else{
                $query .= " AND ( (b.stsnonaktif<>'Y' and b.divisi<>'OTC' AND a.kodeinput IN ('A', 'B', 'C') and IFNULL(b.pilih,'')<>'N') OR a.idinput='$pidinput')";
            }
        }else{
            //$query .= " AND b.stsnonaktif<>'Y' and b.divisi<>'OTC' AND a.kodeinput IN ('A', 'B', 'C') and IFNULL(b.pilih,'')<>'N' ";
            if ($padvance=="B") {
                $query .= " AND b.stsnonaktif<>'Y' and b.divisi<>'OTC' AND a.kodeinput IN ('A', 'B', 'C') ";
            }else{
                $query .= " AND b.stsnonaktif<>'Y' and b.divisi<>'OTC' AND a.kodeinput IN ('A', 'B', 'C') and IFNULL(b.pilih,'')<>'N' ";
            }
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
        
        
        $puseryginput=$_SESSION['USERID'];
        //wewenang diganti jadi user input
        $filtercoa_wwn=" AND user1='$puseryginput' ";
    
    $nfilter_sudahada="";//biar tidak ada yang double (tp ditutup dulu) karena ada pengajuan yang sama (klaim)
                    
    $query = "select *, CAST('' as CHAR(1)) as sinput from hrd.br0 WHERE 1=1 ";
    if ($pact=="editdata") {
        $query .= " AND ( ($ftypetgl $fil_viasby $filterlampiran $fdivisi $fadvance $filtercoa_wwn AND brId NOT IN (select distinct IFNULL(bridinput,'') FROM $tmp00 WHERE IFNULL(sinput,'')<>'Y')) OR "
                . "  brId IN (select distinct IFNULL(bridinput,'') FROM $tmp04 WHERE IFNULL(sinput,'')='Y') )";
    }else{
        $query .= " AND $ftypetgl $fil_viasby $filterlampiran $fdivisi $fadvance $filtercoa_wwn AND brId NOT IN (select distinct IFNULL(bridinput,'') FROM $tmp00 WHERE IFNULL(sinput,'')<>'Y')";
    }
    
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select dokterId, nama FROM hrd.dokter WHERE dokterId IN "
            . " (select distinct dokterId FROM $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.karyawanId, c.nama nama_karyawan, a.dokterId, a.dokter, b.nama nama_dokter,
        a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, jumlah1, a.realisasi2, a.sinput from $tmp01 a 
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
        
        $query = "select a.*, b.urutan, b.bridinput, b.trans_ke, b.jml_adj, b.ketadj1, b.amount from $tmp03 a LEFT JOIN $tmp04 b on a.brId=b.bridinput";
        $query = "create TEMPORARY table $tmp05 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp05 a JOIN $tmp04 b on a.brId=b.bridinput SET a.sinput=b.sinput";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    }else{
        
        $query = "select *, CAST(null as DECIMAL(10,0)) as urutan, CAST('' as CHAR(20)) as bridinput, "
                . " CAST('' as CHAR(2)) as trans_ke, CAST(null as DECIMAL(20,2)) jml_adj, "
                . " CAST('' as CHAR(10)) as ketadj1, CAST(NULL as DECIMAL(20,2)) as amount from $tmp03";
        $query = "create TEMPORARY table $tmp05 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    ?>


    
    <div class='form-group'>
        &nbsp;&nbsp;&nbsp;
        <button type='button' class='btn btn-danger btn-xs' onclick='HitungTotalDariCekBox()'>Hitung Jumlah</button> 
        <span class='required'></span>
    </div>
    
    
     <div class='x_content' style="overflow-x:auto;">
        
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='10px'><input type="checkbox" id="chkall1[]" name="chkall1[]" onclick="SelAllCheckBox('chkall1[]', 'chk_jml1[]')" value='select'></th>
                    <th width='10px'>No</th>
                    <th width='20px'>Urutan</th>
                    <th width='10px'>Non BCA</th>
                    <th width='20px'>Adjusment</th>
                    <th align="center" nowrap>No. Slip</th>
                    <th align="center">Tgl. Transfer</th>
                    <th align="center">Tgl. Input</th>
                    <th align="center" nowrap>Nama Pembuat</th>
                    <th align="center" nowrap>Nama Dokter</th>
                    <th align="center" nowrap>Keterangan</th>
                    <th align="center" nowrap>Nama Realisasi</th>
                    <th align="center" nowrap>Jumlah</th>
                    <th align="center" nowrap <?PHP echo $tr_nhidden; ?>>Jml Input</th>
                    <th align="center" nowrap>ID</th>
                    <th align="center" nowrap>Divisi</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $purut_opt="<option value='' selected></option>";
                for($xu=1;$xu<=60;$xu++) {
                    $purut_opt .="<option value='$xu'>$xu</option>";
                }
                
                $no=1;
                //if ($userid=="0000000566" AND $pdivisi=="PIGEO") {
                //    $query = "select * from $tmp05 order by divprodid DESC, noslip, realisasi1, nama_karyawan, brId";
                //}else{
                    $query = "select * from $tmp05 order by brId, divprodid, noslip, realisasi1, nama_karyawan";
                //}
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
                    //if (!empty($row['realisasi2']))
                        //$pjmlreal=number_format($row['realisasi2'],0,",",",");

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
                        
                        //if (!empty($pbrid_input)) $ncheck_sudah="checked"; //pindah ke bawah
                        if (!empty($ptrans_Ke)) $ncheck_trans="checked";
                    }
                    
                    $pnmselecturut="<select id='cb_urut[$pbrid]' name='cb_urut[$pbrid]' onChange=\"cekBoxDataBR('$pbrid', 'cb_urut[$pbrid]', 'chk_jml1[$pbrid]', 'chk_adj[$pbrid]', 'txt_adj[$pbrid]', 'chk_transke[$pbrid]', 'txt_adj_ket[$pbrid]')\">$purut_opt</select>";
                    
                    $chkbox_bca = "<input type='checkbox' id='chk_transke[$pbrid]' name='chk_transke[$pbrid]' onclick=\"CentangCekBoxDataBR('$pbrid', 'cb_urut[$pbrid]', 'chk_jml1[$pbrid]', 'chk_adj[$pbrid]', 'txt_adj[$pbrid]', 'chk_transke[$pbrid]', 'txt_adj_ket[$pbrid]')\" value='NB' $ncheck_trans>";//NB = NON BCA
                    
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
                            
                    $chkbox = "<input type='checkbox' id='chk_jml1[$pbrid]' name='chk_jml1[]' value='$pbrid' onclick=\"CentangCekBoxDataBR('$pbrid', 'cb_urut[$pbrid]', 'chk_jml1[$pbrid]', 'chk_adj[$pbrid]', 'txt_adj[$pbrid]', 'chk_transke[$pbrid]', 'txt_adj_ket[$pbrid]')\" $nval_chk>";

                    
                    $pket_adj=$row['ketadj1'];
                    $jml_adj = $row['jml_adj'];
                    $jml_adj=number_format($jml_adj,0,",",",");
                    
                    $ncheck_sudah_adj="";
                    $nvisible_txtadj="visibility:hidden";
                    if ((double)$jml_adj<>0) {
                        $ncheck_sudah_adj="checked";
                        $nvisible_txtadj="";
                    }
                    
                    $chkbox_adj = "<input type='checkbox' id='chk_adj[$pbrid]' name='chk_adj[$pbrid]' value='$pbrid' onclick=\"ShowHideTextBoxAdj('$pbrid', 'cb_urut[$pbrid]', 'chk_jml1[$pbrid]', 'chk_adj[$pbrid]', 'txt_adj[$pbrid]', 'chk_transke[$pbrid]', 'txt_adj_ket[$pbrid]')\" $ncheck_sudah_adj>";
                    $txtbox_adj = "<input type='text' style='$nvisible_txtadj' id='txt_adj[$pbrid]' name='txt_adj[$pbrid]' value='$jml_adj' onblur=\"CentangCekBoxDataBR('$pbrid', 'cb_urut[$pbrid]', 'chk_jml1[$pbrid]', 'chk_adj[$pbrid]', 'txt_adj[$pbrid]', 'txt_adj_ket[$pbrid]')\" size='7px' class='input-sm inputmaskrp2'>";
                    $txtbox_adj_ket = "<input type='text' style='$nvisible_txtadj' "
                            . " id='txt_adj_ket[$pbrid]' name='txt_adj_ket[$pbrid]' value='$pket_adj' "
                            . " onblur=\"CentangCekBoxDataBR('$pbrid', 'cb_urut[$pbrid]', 'chk_jml1[$pbrid]', 'chk_adj[$pbrid]', 'txt_adj[$pbrid]', 'txt_adj_ket[$pbrid]')\" "
                            . " size='30px' class='input-sm'>";
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$chkbox $pinput_jumlah<t/d>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnmselecturut</td>";
                    echo "<td nowrap>$chkbox_bca</td>";
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
                    echo "<td nowrap align='right' $tr_nhidden>$pamount</td>";
                    echo "<td nowrap>$pbrid</td>";
                    echo "<td nowrap>$pdivisi</td>";
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
                    <th width='10px'>Trans. Ke</th>
                    <th align="center" nowrap>No. Slip</th>
                    <th align="center">Tgl. Transfer</th>
                    <th align="center">Tgl. Input</th>
                    <th align="center" nowrap>Nama Pembuat</th>
                    <th align="center" nowrap>Nama Dokter</th>
                    <th align="center" nowrap>Keterangan</th>
                    <th align="center" nowrap>Nama Realisasi</th>
                    <th align="center" nowrap>Jumlah</th>
                    <!--<th align="center" nowrap>ID</th>-->
                </tr>
            </thead>
            <tbody>
            <?PHP
                $purut_opt="<option value='' selected></option>";
                for($xu=1;$xu<=60;$xu++) {
                    $purut_opt .="<option value='$xu'>$xu</option>";
                }
                
                $no=1;
                $query = "select * from $tmp05 order by urutan, brId, divprodid, noslip, realisasi1, nama_karyawan";
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
                    //if (!empty($row['realisasi2']))
                        //$pjmlreal=number_format($row['realisasi2'],0,",",",");
                    
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
                    
                    
                    $ptrans_Ke = $row['trans_ke'];
                    if ($ptrans_Ke=="NB") $ptrans_Ke="Non BCA";
                    
                    $jml_adj = $row['jml_adj'];
                    $jml_adj=number_format($jml_adj,0,",",",");
                    if ((double)$jml_adj==0) $jml_adj="";
                    
                    echo "<tr id='$nm_tr_tabel' $tb_style>";
                    echo "<td hidden>$no_nurut_</td>";
                    echo "<td nowrap>$nurut_</td>";
                    echo "<td nowrap>$jml_adj</td>";
                    echo "<td nowrap>$ptrans_Ke</td>";
                    echo "<td nowrap>$pnoslip</td>";
                    echo "<td nowrap>$ptgltrans</td>";
                    echo "<td nowrap>$ptglinput</td>";
                    echo "<td>$pnamakaryawan</td>";
                    echo "<td>$pnmdokter</td>";
                    echo "<td>$paktivitas1</td>";
                    echo "<td>$prealisasi1</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    //echo "<td nowrap>$pbrid</td>";
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
        
        
        
        function HitungTotalDariCekBoxADJ() {
            var ekode =document.getElementById('cb_kode').value;
            if (ekode=="7") {
                return false;
            }
            
            var chk_arr1 =  document.getElementsByName('chk_jml1[]');
            var chklength1 = chk_arr1.length;
            var newchar = '';
            
            var nTotalAdj_="0";
            for(k=0;k< chklength1;k++)
            {
                if (chk_arr1[k].checked == true) {
                    var kata = chk_arr1[k].value;
                    var fields = kata.split('-');    
                    var anm_jml="txt_adj["+fields[0]+"]";
                    var ajml=document.getElementById(anm_jml).value;
                    if (ajml=="") ajml="0";
                    ajml = ajml.split(',').join(newchar);
                    
                    nTotalAdj_ =parseInt(nTotalAdj_)+parseInt(ajml);
                }
            }
            
            document.getElementById('e_jmladj').value=nTotalAdj_;
            
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
                    if (ajml=="") ajml="0";
                    ajml = ajml.split(',').join(newchar);
                    
                    nTotal_ =parseInt(nTotal_)+parseInt(ajml);
                }
            }
            
            document.getElementById('e_jmlusulan').value=nTotal_;
            HitungTotalDariCekBoxADJ();
            
            var ajml_adj=document.getElementById('e_jmladj').value;
            ajml_adj = ajml_adj.split(',').join(newchar);
            nTotal_ =parseInt(nTotal_)+parseInt(ajml_adj);
            document.getElementById('e_jmltotal').value=nTotal_;
            
            
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
        function cekBoxDataBR(nidbr, nmcb, nmchk, nchkadj, ntxtadj, ntranske, ntxtketadj) {
            var ecb_br=document.getElementById(nmcb).value;
            if (ecb_br!=""){
                document.getElementById(nmchk).checked = true;
            }else{
                document.getElementById(nmchk).checked = false;
            }
            HitungTotalDariCekBox();
            
            BukaTutupTabelInput(nidbr, nmchk, ecb_br, nchkadj, ntxtadj, ntranske, ntxtketadj);
            //alert(nmcb+", ada, "+nmchk);
        }
        
        function CentangCekBoxDataBR(nidbr, nmcb, nmchk, nchkadj, ntxtadj, ntranske, ntxtketadj) {
            HitungTotalDariCekBox();
            
            var ecb_br=document.getElementById(nmcb).value;
            BukaTutupTabelInput(nidbr, nmchk, ecb_br, nchkadj, ntxtadj, ntranske, ntxtketadj);
        }
        
        function BukaTutupTabelInput(nidbr, nmchk, nurutan, nchkadj, ntxtadj, ntranske, ntxtketadj) {
            var nm_r = "tb_"+nidbr;
            var nrow = document.getElementById(nm_r);
            if (document.getElementById(nmchk).checked == true) {
                nrow.style.display = '';
            }else{
                nrow.style.display = 'none';
            }
            
            var ninput_urut=nurutan;
            if (nurutan.length==1) ninput_urut="0"+""+nurutan;
            
            if (document.getElementById(nchkadj).checked == true) {
                if (document.getElementById(ntxtadj).value=="0"){
                    document.getElementById(nm_r).cells.item(2).innerHTML="";
                }else{
                    document.getElementById(nm_r).cells.item(2).innerHTML=document.getElementById(ntxtadj).value;
                }
            }else{
                document.getElementById(nm_r).cells.item(2).innerHTML="";
            }
            
            
            if (document.getElementById(ntranske).checked == true) {
                document.getElementById(nm_r).cells.item(3).innerHTML="Non BCA";
            }else{
                document.getElementById(nm_r).cells.item(3).innerHTML="";
            }
            
            
            document.getElementById(nm_r).cells.item(0).innerHTML=ninput_urut;
            document.getElementById(nm_r).cells.item(1).innerHTML=nurutan;
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
        
        function ShowHideTextBoxAdj(nidbr, nmcb, nmchk, nchkajd, ntxtadj, ntranske, ntxtketadj){
            if (document.getElementById(nchkajd).checked == true) {
                document.getElementById(ntxtadj).style.visibility = 'visible';
                document.getElementById(ntxtketadj).style.visibility = 'visible';
            }else{
                document.getElementById(ntxtadj).style.visibility = 'hidden';
                document.getElementById(ntxtketadj).style.visibility = 'hidden';
                
                document.getElementById(ntxtadj).value="0";
                
            }
            CentangCekBoxDataBR(nidbr, nmcb, nmchk, nchkajd, ntxtadj, ntranske, ntxtketadj);
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
