<?php
session_start();


    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    include "../../config/koneksimysqli.php";
    
    $mytgl1 = $_POST['ubulan'];
    $mytgl2 = $_POST['ubulan2'];
    $ptypetgl = $_POST['utipe'];
    
    $_SESSION['SSMONITUSERTGL1']=$mytgl1;
    $_SESSION['SSMONITUSERTGL2']=$mytgl2;
    $_SESSION['SSMONITUSERTIPE']=$ptypetgl;
    
    
    $pperiode1= date("Y-m-01", strtotime($mytgl1));
    $pperiode2= date("Y-m-t", strtotime($mytgl2));
    
    $pbulan1= date("F Y", strtotime($mytgl1));
    $pbulan2= date("F Y", strtotime($mytgl2));
    
    $fkaryawan=$_SESSION['IDCARD'];
    $pidusergroup=$_SESSION['GROUP'];
    
    
    $now=date("mdYhis");
    $puserid=$_SESSION['USERID'];
    
    $tmp00 =" dbtemp.tmpbrmonituser00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpbrmonituser01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpbrmonituser02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpbrmonituser03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmpbrmonituser04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmpbrmonituser05_".$puserid."_$now ";
    $tmp11 =" dbtemp.tmpbrmonituser11_".$puserid."_$now ";
    
    
    $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih, b.kodeid, b.subkode from dbmaster.t_suratdana_br1 a "
            . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
            . " IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.nodivisi,'')<>'' AND a.kodeinput IN ('A', 'B', 'C') "
            . " AND b.divisi<>'OTC'";//AND b.tgl>='$pperiode1' 
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            $query = "ALTER table $tmp00 ADD COLUMN noidauto BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query = "CREATE UNIQUE INDEX `unx1` ON $tmp00 (noidauto)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
    $query = "select distinct tanggal, nobukti, idinput, nodivisi from dbmaster.t_suratdana_bank "
            . " WHERE IFNULL(stsnonaktif,'')<>'Y' and stsinput='K' and subkode not in ('29') "
            . " AND idinput IN (select distinct IFNULL(idinput,'') from $tmp00)";
    $query = "create TEMPORARY table $tmp11 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "ALTER table $tmp11 ADD COLUMN noidauto BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query = "CREATE UNIQUE INDEX `unx1` ON $tmp11 (noidauto)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            
        $query = "select bridinput, tgltransfersby, jumlah jmlsby, nobukti from dbmaster.t_br0_via_sby WHERE tgltransfersby BETWEEN '$pperiode1' AND '$pperiode2'";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    $query = "select brid, noslip, icabangid, tgl, tgltrans, tglrpsby, tgltrm, divprodid, COA4, kode, realisasi1, "
            . " jumlah, jumlah1, "
            . " aktivitas1, aktivitas2, dokterid, dokter, karyawanid, mrid, ccyid, lampiran, ca, "
            . " dpp, ppn_rp, pph_rp, tgl_fp, batal, retur "
            . " from hrd.br0 WHERE IFNULL(batal,'')<>'Y' AND IFNULL(retur,'')<>'Y' AND "
            . " brId NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND ";
    if ($ptypetgl=="1") {
        $query .= " ( (tgltrans BETWEEN '$pperiode1' AND '$pperiode2') OR brid IN (select distinct IFNULL(bridinput,'') FROM $tmp02) ) ";
    }else{
        $query .= " tgl BETWEEN '$pperiode1' AND '$pperiode2' ";
    }
    
    $query .= " AND IFNULL(dokterid,'') NOT IN ('', '(blank)', 'blank') ";
    $query .= " AND IFNULL(stsbr,'') IN ('KI') ";
    
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
    $query = "ALTER table $tmp01 ADD COLUMN nobuktibbk VARCHAR(20)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    //via SBY
    //if ($ptypetgl=="1") {
        $query = "UPDATE $tmp01 a JOIN (select bridinput, sum(jmlsby) as jmlsby from $tmp02 group by 1) b on "
                . " a.brid=b.bridinput SET a.jumlah1=b.jmlsby WHERE IFNULL(a.jumlah1,0)=0";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp01 a JOIN (select bridinput, tgltransfersby, nobukti from $tmp02 WHERE IFNULL(tgltransfersby,'')<>'' AND IFNULL(tgltransfersby,'0000-00-00')<>'0000-00-00' ) b on "
                . " a.brid=b.bridinput SET a.tgltrans=b.tgltransfersby, a.nobuktibbk=b.nobukti WHERE IFNULL(a.tgltrans,'')='' OR IFNULL(a.tgltrans,'0000-00-00')='0000-00-00'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    //}
        
        
        
    $query = "select a.*, d.nama nama_dokter, e.nama nama_karyawan, b.nama nama_cabang, c.nama nama_kode, f.NAMA4, g.nama as nama_mr "
            . " from $tmp01 a LEFT JOIN mkt.icabang b on a.icabangid=b.icabangid "
            . " LEFT JOIN hrd.br_kode c on a.kode=c.kodeid "
            . " LEFT JOIN hrd.dokter d on a.dokterId=d.dokterId"
            . " LEFT JOIN hrd.karyawan e on a.karyawanId=e.karyawanId "
            . " LEFT JOIN hrd.karyawan g on a.mrid=g.karyawanId "
            . " LEFT JOIN dbmaster.coa_level4 f on a.COA4=f.COA4";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "ALTER table $tmp02 ADD COLUMN idinput BIGINT(20), ADD COLUMN nodivisi VARCHAR(20), ADD COLUMN idinput1 BIGINT(20), ADD COLUMN nodivisi1 VARCHAR(20), ADD COLUMN idinput2 BIGINT(20), ADD COLUMN nodivisi2 VARCHAR(20)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    $query = "UPDATE $tmp02 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp00 WHERE IFNULL(pilih,'')<>'Y') b on a.brId=b.bridinput "
            . " SET a.nodivisi1=b.nodivisi, a.idinput1=b.idinput";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp00 WHERE IFNULL(pilih,'')='Y') b on a.brId=b.bridinput "
            . " SET a.nodivisi2=b.nodivisi, a.idinput2=b.idinput";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET nodivisi=nodivisi2, idinput=idinput2";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp02 SET nodivisi=nodivisi1, idinput=idinput1 WHERE IFNULL(nodivisi,'')=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
    //isi tanggal transfer no bukti bbk bobukti
    $query = "UPDATE $tmp02 a JOIN $tmp11 b on a.idinput=b.idinput SET a.nobuktibbk=b.nobukti";//a.nobukti=b.nobukti, a.tgltrans=b.tanggal
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER table $tmp02 ADD COLUMN jumlahfin DECIMAL(20,2)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET jumlahfin=jumlah";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp02 SET jumlahfin=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' 
      id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        
        <table id='datatableusrmonit' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='7px'></th>
                    <th width='5px'>ID</th>
                    <th width='50px'>Tgl. Input</th>
                    <th width='50px'>Tgl. Transfer</th>
                    <th width='50px'>MR ID</th>
                    <th width='50px'>NAMA MR</th>
                    <th width='50px'>Id User</th>
                    <th width='50px'>User</th>
                    <th align="center">Jumlah</th>
                    <th align="center">Realisasi</th>
                    <th align="center">Keterangan</th>
                    <th align="center">Karyawan Id</th>
                    <th align="center">Nama Karyawan</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $pgrtotal1=0;
                $pgrtotal2=0;
                $no=1;
                $query = "select * from $tmp02 ";
                $query .= " ORDER BY divprodid, nama_kode, NAMA4";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pbrid=$row['brid'];

                    $ptglbr=$row['tgl'];
                    $ptgltrs=$row['tgltrans'];
                    $pdivisi=$row['divprodid'];
                    $pnmdivisi=$pdivisi;
                    if ($pdivisi=="PEACO") $pnmdivisi="PEACOK";
                    if ($pdivisi=="PIGEO") $pnmdivisi="PIGEON";
                    
                    
                    
                    $pnmakun=$row['nama_kode'];
                    $pcoa=$row['COA4'];
                    $pnmcoa=$row['NAMA4'];
                    $ppnmcabang=$row['nama_cabang'];
                    $pidkaryawan=$row['karyawanid'];
                    $pnmkaryawan=$row['nama_karyawan'];
                    $piddokter=$row['dokterid'];
                    $pnmdokter=$row['nama_dokter'];
                    $pnoslip=$row['noslip'];
                    $pnmrealisasi=$row['realisasi1'];
                    $pjmlrp1=$row['jumlah'];
                    $pjmlrp2=$row['jumlah1'];
                    $pjmlfinrp=$row['jumlahfin'];
                    $ptgltrm=$row['tgltrm'];
                    $pket1=$row['aktivitas1'];
                    $pket2=$row['aktivitas2'];
                    $ptglrpsby=$row['tglrpsby'];
                    $pnodivisi=$row['nodivisi'];
                    $pnodivisi1=$row['nodivisi1'];
                    $pnodivisi2=$row['nodivisi2'];
                    $pnobuktibbk=$row['nobuktibbk'];
                    
                    $pmrid=$row['mrid'];
                    $pmrnama=$row['nama_mr'];



                    $pinnodivisi=$pnodivisi;

                    $pselisih=(double)$pjmlrp1-(double)$pjmlrp2;

                    $pgrtotal1=(double)$pgrtotal1+(double)$pjmlrp1;
                    $pgrtotal2=(double)$pgrtotal2+(double)$pjmlrp2;

                    $pdokternm="";
                    if (!empty($pnmdokter)) $pdokternm=$piddokter." - ".$pnmdokter;


                    if ($ptgltrs=="0000-00-00") $ptgltrs="";
                    if ($ptgltrm=="0000-00-00") $ptgltrm="";
                    if ($ptglrpsby=="0000-00-00") $ptglrpsby="";
                    
                    if (empty($ptgltrs)) $ptgllihatks=$ptglbr;
                    else $ptgllihatks=$ptgltrs;
                    
                    $ptglbr = date("d/m/Y", strtotime($ptglbr));
                    if (!empty($ptgltrs)) $ptgltrs = date("d/m/Y", strtotime($ptgltrs));
                    if (!empty($ptgltrm)) $ptgltrm = date("d/m/Y", strtotime($ptgltrm));
                    if (!empty($ptglrpsby)) $ptglrpsby = date("d/m/Y", strtotime($ptglrpsby));

                    $pjmlrp1=number_format($pjmlrp1,0,",",",");
                    $pjmlrp2=number_format($pjmlrp2,0,",",",");
                    $pselisih=number_format($pselisih,0,",",",");
                    $pjmlfinrp=number_format($pjmlfinrp,0,",",",");


                    $pketerangan=$pket1;
                    if (!empty($pket2)) {
                        if (!empty($pketerangan)) $pketerangan .=" ".$pket2;
                        else $pketerangan=$pket2;
                    }

                    $pbtnlihatks="<button type='button' class='btn btn-success btn-xs' data-toggle='modal' "
                            . " data-target='#myModal' onClick=\"TampilkanDataKSDokter('$ptgllihatks', '$piddokter', '$pidkaryawan', '$pmrid', '$pjmlfinrp')\">"
                            . "Lihat KS</button>";
                    
                    if (empty($ptgltrs)) {
                        $pbtnlihatks="";
                    }
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap class='str'>$pbtnlihatks</td>";
                    echo "<td nowrap>$pbrid</td>";
                    echo "<td nowrap>$ptglbr</td>";
                    echo "<td nowrap>$ptgltrs</td>";
                    echo "<td nowrap>$pmrid</td>";
                    echo "<td nowrap>$pmrnama</td>";
                    echo "<td nowrap>$piddokter</td>";
                    echo "<td nowrap>$pnmdokter</td>";
                    echo "<td nowrap align='right'>$pjmlfinrp</td>";
                    echo "<td nowrap>$pnmrealisasi</td>";
                    echo "<td >$pketerangan</td>";
                    echo "<td nowrap>$pidkaryawan</td>";
                    echo "<td nowrap>$pnmkaryawan</td>";
                    
                    /*
                    echo "<td nowrap>$pnmdivisi</td>";
                    echo "<td nowrap>$ppnmcabang</td>";
                    echo "<td nowrap>$pnmakun</td>";
                    echo "<td nowrap>$pcoa $pnmcoa</td>";
                    echo "<td nowrap>$pnmkaryawan</td>";
                    echo "<td nowrap class='str'>$pdokternm</td>";
                    echo "<td nowrap class='str'>$pnoslip</td>";
                    echo "<td nowrap align='right'>$pjmlrp1</td>";
                    echo "<td nowrap align='right'>$pjmlrp2</td>";
                    echo "<td nowrap align='right'>$pselisih</td>";
                    echo "<td nowrap>$ptgltrm</td>";
                    echo "<td >$pketerangan</td>";
                    echo "<td nowrap>$ptglrpsby</td>";
                    echo "<td nowrap>$pnodivisi2</td>";
                    echo "<td nowrap>$pnodivisi1</td>";
                    echo "<td nowrap>$pnobuktibbk</td>";
                    */
                    echo "</tr>";


                    $no++;
                }
                
                /*
                $pselisih=(double)$pgrtotal1-(double)$pgrtotal2;

                $pgrtotal1=number_format($pgrtotal1,0,",",",");
                $pgrtotal2=number_format($pgrtotal2,0,",",",");
                $pselisih=number_format($pselisih,0,",",",");

                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap colspan='11' align='center'>T O T A L : </td>";
                echo "<td nowrap align='right'>$pgrtotal1</td>";
                echo "<td nowrap align='right'>$pgrtotal2</td>";
                echo "<td nowrap align='right'>$pselisih</td>";
                echo "<td nowrap colspan='7'></td>";
                echo "</tr>";
                */
                ?>
            </tbody>
        </table>
                
        
    </div>
    
</form>

<style>
    .divnone {
        display: none;
    }
    #datatableusrmonit th {
        font-size: 13px;
    }
    #datatableusrmonit td { 
        font-size: 11px;
    }
</style>

<style>

table {
    text-align: left;
    position: relative;
    border-collapse: collapse;
    background-color:#FFFFFF;
}

th {
    background: white;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    z-index:1;
}

.th2 {
    background: white;
    position: sticky;
    top: 23;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    border-top: 1px solid #000;
}
</style>

<script>
    function TampilkanDataKSDokter(ptgltrs, piddokter, pidkaryawan, pmrid, trp) {
        $.ajax({
            type:"post",
            url:"module/mod_br_monitoringki/lihatdataks.php?module=viewdataks",
            data:"utgltrs="+ptgltrs+"&uiddokter="+piddokter+"&uidkaryawan="+pidkaryawan+"&umrid="+pmrid+"&urp="+trp,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
</script>
<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp11");
    
    mysqli_close($cnmy);
?>