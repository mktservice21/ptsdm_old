<?php
date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

session_start();

$pmodule="";
$pketpilih="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];
if (isset($_GET['ket'])) $pketpilih=$_GET['ket'];

if ($pmodule=="spdklaimdisc" AND $pketpilih=="dataklaimdisc") {
    
    ?>
    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    <script src="js/inputmask.js"></script>
    <?PHP
    
    
    include "../../../config/koneksimysqli.php";
    
    $pact=$_POST['uact'];
    $pidinput=$_POST['uidinput'];
    $pdivisi=$_POST['udivisi'];
    $pjenisid=$_POST['ujenis'];
    $ptgl=$_POST['utgl'];
    $ppertipe=$_POST['upertipe'];
    $pperiode1=$_POST['uper1'];
    $pperiode2=$_POST['uper2'];
    
    $ptgl= date("Y-m-d", strtotime($ptgl));
    $pbulan01= date("Y-m-01", strtotime($pperiode1));
    $pbulan02= date("Y-m-t", strtotime($pperiode2));
    
    $puserid="";
    
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpkdinputpd00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpkdinputpd01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpkdinputpd02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpkdinputpd03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmpkdinputpd04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmpkdinputpd05_".$puserid."_$now ";
    
    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG....";
        goto hapusdata;
    }
    
    $pkaryawanid=$_SESSION['IDCARD'];
    $pgroupid=$_SESSION['GROUP'];
    
    $query = "select distinct a.urutan, a.trans_ke, a.jml_adj, a.aktivitas1 ketadj1, a.idinput, IFNULL(a.bridinput,'') bridinput, a.amount, "
            . " CAST('' as CHAR(1)) as sinput FROM dbmaster.t_suratdana_br1 a JOIN "
            . " dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE 1=1 "
            . "  ";
    if ($pact=="editdata") {
        $query .= " AND ( (IFNULL(b.stsnonaktif,'')<>'Y' AND a.kodeinput IN ('E') ) OR a.idinput='$pidinput')";
    }else{
        $query .= " AND IFNULL(b.stsnonaktif,'')<>'Y' AND a.kodeinput IN ('E') ";
    }

    $query = "create TEMPORARY table $tmp00 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if ($pact=="editdata") {
        $query = "UPDATE $tmp00 SET sinput='Y' WHERE idinput='$pidinput'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    $query = "select * FROM $tmp00 WHERE IFNULL(sinput,'')='Y'";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $ftypetgl = " a.bulan BETWEEN '$pbulan01' AND '$pbulan02' ";
    if ($ppertipe=="K") $ftypetgl = " a.bulan BETWEEN '$pbulan01' AND '$pbulan02' ";
    if ($ppertipe=="I") $ftypetgl = " a.tgl BETWEEN '$pbulan01' AND '$pbulan02' ";
    
    $query = "select a.DIVISI divisi, a.klaimId as klaimid, a.karyawanid, c.nama nama_karyawan,
        a.distid, b.nama nama_dist, a.pengajuan, a.aktivitas1, a.jumlah, a.tgl, a.bulan, a.tgltrans, 
        a.realisasi1, a.noslip, a.COA4, d.NAMA4, CAST('' as CHAR(1)) as sinput  
        from hrd.klaim a LEFT JOIN MKT.distrib0 b on a.distid=b.distid 
        LEFT JOIN hrd.karyawan c on a.karyawanid=c.karyawanId 
        LEFT JOIN dbmaster.coa_level4 d on a.COA4=d.COA4
        WHERE 1=1 ";
    if ($pgroupid=="1" OR $pgroupid=="24") {
    }else{
        $query .=" AND a.karyawanid='$pkaryawanid' ";
    }
    
    
    if ($pact=="editdata") {
        $query .= " AND ($ftypetgl OR a.klaimId IN (select distinct IFNULL(bridinput,'') FROM $tmp00 WHERE IFNULL(sinput,'')='Y') ) ";
        $query .= " AND a.klaimId NOT IN (select distinct IFNULL(bridinput,'') FROM $tmp00 WHERE IFNULL(sinput,'')<>'Y') ";
    }else{
        $query .= " AND $ftypetgl ";
        $query .= " AND a.klaimId NOT IN (select DISTINCT IFNULL(klaimId,'') FROM hrd.klaim_reject) ";
        $query .= " AND a.klaimId NOT IN (select distinct IFNULL(bridinput,'') FROM $tmp00)";
    }
    
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "ALTER TABLE $tmp02 ADD COLUMN amount DECIMAL(20,2), ADD COLUMN urutan INT(4), "
            . " ADD COLUMN trans_ke VARCHAR(10), ADD COLUMN bridinput VARCHAR(20)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if ($pact=="editdata") {
        $query = "UPDATE $tmp02 as a JOIN $tmp00 as b on a.klaimid=b.bridinput SET "
                . " a.sinput=b.sinput, a.urutan=b.urutan, a.trans_ke=b.trans_ke, a.bridinput=b.bridinput, a.amount=b.amount WHERE "
                . " IFNULL(b.sinput,'')='Y'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    
    
    
    $tr_nhidden=" class='divnone' ";
    if ($pact=="editdata") {
        $tr_nhidden="";
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
                        <th align="center" nowrap>Jumlah</th>
                        <th align="center" nowrap <?PHP echo $tr_nhidden; ?>>Jml Input</th>
                        <th align="center" nowrap>Supplier</th>
                        <th align="center" nowrap>Keterangan</th>
                        <th align="center" nowrap>Nama Pembuat</th>
                        <th align="center" nowrap>ID</th>
                        <th align="center" nowrap>Divisi</th>
                        <th align="center" nowrap>Tgl. Input</th>
                        <th align="center" nowrap>Bulan</th>
                    </tr>
                </thead>
                <tbody>
                    <?PHP
                        $purut_opt="<option value='' selected></option>";
                        for($xu=1;$xu<=60;$xu++) {
                            $purut_opt .="<option value='$xu'>$xu</option>";
                        }
                        
                        $no=1;
                        $query = "select * from $tmp02 order by nama_karyawan, nama_dist, noslip, pengajuan, klaimid";
                        $tampil=mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            $pbrid = $row['klaimid'];
                            $ptglinput = $row['tgl'];
                            $nbulan = $row['bulan'];
                            $pnoslip = $row['noslip'];
                            $pcoa = $row['COA4'];
                            $pnmcoa = $row['NAMA4'];
                            $pnmsup = $row['nama_dist'];
                            $pnamakaryawan = $row['nama_karyawan'];
                            $paktivitas1 = $row['aktivitas1'];
                            $ppengajuan = $row['pengajuan'];
                            $pjumlah = $row['jumlah'];
                            $pamount = $row['amount'];
                            $psinputsudah = $row['sinput'];
                            $ptgltrans = $row['tgltrans'];
                            if ($ptgltrans=="0000-00-00") $ptgltrans = "";
                            
                            $nnjumlahpilih=$pjumlah;
                            
                            $ncheck_trans="";
                            $ncheck_sudah="";
                            $nval_chk="";
                            if ($pact=="editdata") {
                                if ($psinputsudah=="Y") {
                                    $nnjumlahpilih=$pamount;
                                    $nval_chk="checked";
                                }
                                
                                
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
                            
                            $pnamapengajuan=$ppengajuan;
                            if ($ppengajuan=="CAN") $pnamapengajuan="CANARY";
                            if ($ppengajuan=="PIGEO") $pnamapengajuan="PIGEON";
                            if ($ppengajuan=="PEACO") $pnamapengajuan="PEACOCK";
                            
                            $ptglinput =date("d/m/Y", strtotime($ptglinput));
                            $nbulan =date("F Y", strtotime($nbulan));
                            
                            $pjumlah=number_format($pjumlah,2,".",",");
                            $pamount=number_format($pamount,2,".",",");
                            
                            $pnmselecturut="<select id='cb_urut[$pbrid]' name='cb_urut[$pbrid]' onChange=\"cekBoxDataBR('cb_urut[$pbrid]', 'chk_jml1[$pbrid]')\">$purut_opt</select>";
                            $chkbox_bca = "<input type='checkbox' id='chk_transke[$pbrid]' name='chk_transke[$pbrid]' value='NB' $ncheck_trans>";//NB = NON BCA
                            $pinput_jumlah="<input type='hidden' id='txt_jml[$pbrid]' name='txt_jml[$pbrid]' value='$nnjumlahpilih' Readonly>";
                            $chkbox = "<input type='checkbox' id='chk_jml1[$pbrid]' name='chk_jml1[]' onclick='HitungTotalDariCekBoxKD()' value='$pbrid' $ncheck_sudah>";
                            
                            
                            echo "<tr>";
                            echo "<td nowrap>$chkbox $pinput_jumlah<t/d>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap>$pnmselecturut</td>";
                            echo "<td nowrap>$chkbox_bca</td>";
                            echo "<td nowrap>$pnoslip</td>";
                            echo "<td nowrap>$ptgltrans</td>";
                            echo "<td nowrap align='right'>$pjumlah</td>";
                            echo "<td nowrap align='right' $tr_nhidden>$pamount</td>";
                            echo "<td>$pnmsup</td>";
                            echo "<td>$paktivitas1</td>";
                            echo "<td>$pnamakaryawan</td>";
                            echo "<td nowrap>$pbrid</td>";
                            echo "<td nowrap>$pnamapengajuan</td>";
                            echo "<td nowrap>$ptglinput</td>";
                            echo "<td nowrap>$nbulan</td>";
                            echo "</tr>";
                            
                            $no++;
                        }
                    ?>
                </tbody>
            </table>
        
    </div>
    
        
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
                    
                    nTotal_ =parseFloat(nTotal_)+parseFloat(ajml);
                }
            }
            
            document.getElementById('e_jmlusulan').value=nTotal_;
        }
        
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
    
    <?PHP
    
        
    hapusdata:
        mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp00");
        mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp01");
        mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp02");
        mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp03");
        mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp04");
        mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp05");
        mysqli_close($cnmy);
}

