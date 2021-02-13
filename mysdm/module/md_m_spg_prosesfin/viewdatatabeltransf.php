<?php
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
    session_start();
    include "../../config/koneksimysqli.php";
    
    $pidcard=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    if (empty($pidcard)) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DGJSPGOTC01_".$pidcard."_$now ";
    $tmp02 =" dbtemp.DGJSPGOTC02_".$pidcard."_$now ";
    $tmp03 =" dbtemp.DGJSPGOTC03_".$pidcard."_$now ";
    
    $date1=$_POST['utgl'];
    $periode1= date("Ym", strtotime($date1));
    $ptanggal= date("Y-m-d", strtotime($date1));
    $ecabang=$_POST['ucabang'];
    $cnodivisibr=$_POST['unodiv'];
    
    $periode_pil_ket= date("F Y", strtotime($date1));
    
    $fcabang = "";
    
    $fperiode = " AND DATE_FORMAT(a.periode, '%Y%m') = '$periode1'";
    if (!empty($ecabang) AND ($ecabang <> "*")) {
        if ($ecabang=="JKT_MT") {
            $fcabang = " AND a.icabangid='0000000007' AND a.alokid='001' ";
        }elseif ($ecabang=="JKT_RETAIL") {
            $fcabang = " AND a.icabangid='0000000007' AND a.alokid='002' ";
        }else{
            $fcabang = " AND a.icabangid='$ecabang' ";
        }
    }
    
    $filternodivs_idinput="";
    if (!empty($cnodivisibr)) {
        $filternodivs_idinput=" AND IFNULL(idinput,'')='$cnodivisibr' ";
    }
        
        
        $query = "SELECT
            a.idbrspg,
            a.id_spg,
            b.nama,
            a.periode tglbr,
            a.icabangid,
            c.nama nama_cabang, a.alokid,
            a.jml_harikerja harikerja,
            a.total,
            a.realisasi,
            a.keterangan,
            a.nodivisi,
            a.idinput,
            a.brotcid,
            a.periode_insentif, 
            IFNULL(a.brotcid2,'') as brotcid2,
            IFNULL(a.brotcid3,'') as brotcid3,
            a.total lebihkurang, a.total insentif, a.total gaji, a.keterangan hk, a.total rpmakan, a.total makan, a.total sewa, a.total pulsa, a.total parkir, a.total bbm,
            a.total rlebihkurang, a.total rinsentif, a.total rgaji, a.total rmakan, a.total rsewa, a.total rpulsa, a.total rparkir, a.total rbbm,
            a.total slebihkurang, a.total sinsentif, a.total sgaji, a.total smakan, a.total ssewa, a.total spulsa, a.total sparkir, a.total sbbm
            FROM
            dbmaster.t_spg_gaji_br0 a
            JOIN mkt.spg b ON a.id_spg = b.id_spg
            LEFT JOIN mkt.icabang_o c on a.icabangid=c.icabangid_o 
            WHERE a.stsnonaktif<>'Y' AND IFNULL(a.idinput,'')<>'' AND IFNULL(a.nodivisi,'')<>'' $fperiode $fcabang $filternodivs_idinput";

        $query ="CREATE TEMPORARY TABLE $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query ="Alter table $tmp01 ADD COLUMN jmlbpjs_kry DECIMAL(20,2), ADD COLUMN jmlbpjs_sdm DECIMAL(20,2), ADD COLUMN gaji_asli DECIMAL(20,2)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
        mysqli_query($cnmy, "UPDATE $tmp01 SET lebihkurang=0, insentif=0, gaji=0, hk='', rpmakan=0, makan=0, sewa=0, pulsa=0, parkir=0, bbm=0");
        mysqli_query($cnmy, "UPDATE $tmp01 SET rlebihkurang=0, rinsentif=0, rgaji=0, rmakan=0, rsewa=0, rpulsa=0, rparkir=0, rbbm=0");//ralisasi
        mysqli_query($cnmy, "UPDATE $tmp01 SET slebihkurang=0, sinsentif=0, sgaji=0, smakan=0, ssewa=0, spulsa=0, sparkir=0, sbbm=0");//selisih

        $query = "SELECT * FROM dbmaster.t_spg_gaji_br1 WHERE idbrspg IN (select distinct idbrspg FROM $tmp01)";
        $query ="CREATE TEMPORARY TABLE $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.insentif=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid IN ('01', '07') ),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.gaji=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='02'),0)");

        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rpmakan=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='03'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.makan=IFNULL((SELECT sum(rptotal) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='03'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 SET hk=CONCAT(harikerja,' x ', FORMAT(rpmakan,0,'ta_in'))");

        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.sewa=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='04'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.pulsa=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='05'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.parkir=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='06'),0)");
        
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.bbm=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='08'),0)");
        
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.lebihkurang=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='09'),0)");

        $query ="UPDATE $tmp01 as a JOIN (select idbrspg, rptotal2 FROM $tmp02 WHERE kodeid='10') as b on a.idbrspg=b.idbrspg SET a.jmlbpjs_sdm=b.rptotal2";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

        $query ="UPDATE $tmp01 as a JOIN (select idbrspg, rptotal2 FROM $tmp02 WHERE kodeid='11') as b on a.idbrspg=b.idbrspg SET a.jmlbpjs_kry=b.rptotal2";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
        //realisasi
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rinsentif=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='01'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rgaji=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='02'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rmakan=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='03'),0)");

        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rsewa=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='04'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rpulsa=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='05'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rparkir=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='06'),0)");
        
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rbbm=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='08'),0)");
        
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rlebihkurang=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='09'),0)");

        mysqli_query($cnmy, "UPDATE $tmp01 SET sinsentif=insentif-rinsentif");
        mysqli_query($cnmy, "UPDATE $tmp01 SET sgaji=gaji-rgaji");
        mysqli_query($cnmy, "UPDATE $tmp01 SET smakan=makan-rmakan");
        mysqli_query($cnmy, "UPDATE $tmp01 SET ssewa=sewa-rsewa");
        mysqli_query($cnmy, "UPDATE $tmp01 SET spulsa=pulsa-rpulsa");
        mysqli_query($cnmy, "UPDATE $tmp01 SET sparkir=parkir-rparkir");
        
        mysqli_query($cnmy, "UPDATE $tmp01 SET slebihkurang=lebihkurang-rlebihkurang");
        
        mysqli_query($cnmy, "UPDATE $tmp01 SET sbbm=bbm-rbbm");
        
        
        $query = "UPDATE $tmp01 SET nama_cabang='JAKARTA MT', icabangid='JKT_MT' WHERE icabangid='0000000007' AND alokid='001'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp01 SET nama_cabang='JAKARTA RETAIL', icabangid='JKT_RETAIL' WHERE icabangid='0000000007' AND alokid='002'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
        mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
        
        $query = "select CAST('' as CHAR(50)) as periode_inc, icabangid, nama_cabang, nodivisi, idinput, brotcid, CAST('' as CHAR(10)) as brotcid2, CAST('' as CHAR(10)) as brotcid3, sum(insentif) insentif, sum(gaji) gaji, sum(makan) makan, 
                sum(sewa) sewa, sum(pulsa) pulsa, sum(bbm) bbm, sum(parkir) parkir, 
                sum(rinsentif) rinsentif, sum(rgaji) rgaji, sum(rmakan) rmakan, sum(rsewa) rsewa, sum(rpulsa) rpulsa, sum(rbbm) rbbm, sum(rparkir) rparkir,
                sum(sinsentif) sinsentif, sum(sgaji) sgaji, sum(smakan) smakan, sum(ssewa) ssewa, sum(spulsa) spulsa, sum(sbbm) sbbm, sum(sparkir) sparkir, 
                sum(lebihkurang) lebihkurang, sum(rlebihkurang) rlebihkurang, sum(slebihkurang) slebihkurang, 
                sum(jmlbpjs_kry) as jmlbpjs_kry, sum(jmlbpjs_sdm) as jmlbpjs_sdm
                from $tmp01 GROUP BY 1,2,3,4,5";
                
        $query ="CREATE TEMPORARY TABLE $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query ="alter table $tmp02 add ttotal decimal(20,2), add subpost CHAR(2), ADD kodeid CHAR(2),
            ADD tglbr date, ADD user1 INTEGER(4), ADD lampiran char(1), ADD ca char(1), add via CHAR(1), add bralid char(2),
            add ccyid char(5), add coa4 CHAR(20), ADD kodewilayah char(10), add modifdate timestamp, add lampiran2 char(1), add keterangan char(255), ADD noidinput INTEGER(11)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query ="UPDATE $tmp02 SET ttotal =IFNULL(gaji,0)+IFNULL(makan,0)+IFNULL(sewa,0)+IFNULL(pulsa,0)+IFNULL(bbm,0)+IFNULL(parkir,0)+IFNULL(lebihkurang,0)-IFNULL(jmlbpjs_kry,0)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query ="UPDATE $tmp02 SET keterangan=CONCAT('KLIAM BIAYA GAJI SPG ',nama_cabang, ', PERIODE $periode_pil_ket')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query ="UPDATE $tmp02 SET subpost='05', kodeid='12', coa4='754-01', tglbr=CURRENT_DATE(), user1='$puserid', lampiran='Y', ca='N', via='N', bralid='bl', ccyid='IDR', kodewilayah='', modifdate=now(), lampiran2='Y'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        

        //insentif
        //mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
        
        $query = "select * from $tmp02 WHERE IFNULL(insentif,0)<>0";
        $query ="CREATE TEMPORARY TABLE $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query ="UPDATE $tmp03 a JOIN $tmp01 b on a.icabangid=b.icabangid set a.periode_inc=DATE_FORMAT(b.periode_insentif,'%M %Y') WHERE "
                . " IFNULL(b.periode_insentif,'')<>''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query ="UPDATE $tmp03 SET ttotal=insentif, subpost='03', kodeid='08', coa4='704-05', keterangan=CONCAT('KLIAM INSENTIF SPG ',nama_cabang, ', PERIODE ', periode_inc) ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnmy, "UPDATE $tmp02 SET brotcid2=''");
        
        $query ="INSERT INTO $tmp02 SELECT * FROM $tmp03";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //END insentif
        
        
        //BPJS
        mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
        
        $query = "select icabangid, nama_cabang, nodivisi, idinput, sum(IFNULL(jmlbpjs_kry,0)+IFNULL(jmlbpjs_sdm,0)) as ttotal from $tmp01 WHERE IFNULL(jmlbpjs_sdm,0)<>0";
        $query ="CREATE TEMPORARY TABLE $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query ="alter table $tmp03 add subpost CHAR(2), ADD kodeid CHAR(2),
            ADD tglbr date, ADD user1 INTEGER(4), ADD lampiran char(1), ADD ca char(1), add via CHAR(1), add bralid char(2),
            add ccyid char(5), add coa4 CHAR(20), ADD kodewilayah char(10), add modifdate timestamp, add lampiran2 char(1), add keterangan char(255), ADD noidinput INTEGER(11)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query ="UPDATE $tmp03 SET subpost='10', kodeid='93', coa4='', keterangan=CONCAT('BPJS KETENAGAKERJAAN ',nama_cabang, ', PERIODE $periode_pil_ket') ";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnmy, "UPDATE $tmp02 SET brotcid3=''");
        
        $query ="INSERT INTO $tmp02 (icabangid, nama_cabang, nodivisi, idinput, keterangan, subpost, kodeid, coa4, ttotal)"
                . " SELECT icabangid, nama_cabang, nodivisi, idinput, keterangan, subpost, kodeid, coa4, ttotal "
                . "FROM $tmp03";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //END BPJS
        
        
        $query ="UPDATE $tmp02 a JOIN (select icabangid, brotcid, brotcid2, insentif from $tmp01 WHERE IFNULL(insentif,0)<>0 AND IFNULL(brotcid2,'') <>'') b on a.brotcid=b.brotcid and a.icabangid=b.icabangid SET "
                . " a.brotcid2=b.brotcid2 WHERE IFNULL(a.insentif,0)<>0 AND a.subpost='03' AND a.kodeid='08'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query ="UPDATE $tmp02 a JOIN (select icabangid, brotcid3 from $tmp01 WHERE IFNULL(insentif,0)<>0 AND IFNULL(brotcid3,'') <>'') b on a.icabangid=b.icabangid SET "
                . " a.brotcid3=b.brotcid3 WHERE a.subpost='10' AND a.kodeid='93'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
        $query ="SELECT a.*, b.nama namakode, b.nmsubpost, c.NAMA4 FROM $tmp02 a LEFT JOIN hrd.brkd_otc b on a.kodeid=b.kodeid AND a.subpost=b.subpost "
                . " LEFT JOIN dbmaster.coa_level4 c on a.coa4=c.COA4";
        $query ="CREATE TEMPORARY TABLE $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "ALTER TABLE $tmp01 CHANGE noidinput noidinput INT(10) AUTO_INCREMENT PRIMARY KEY";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

		
		
        $query = "UPDATE $tmp01 a JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput SET a.tglbr=b.tgl";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
		
		
        $query = "UPDATE $tmp01 a JOIN hrd.br_otc b on a.brotcid=b.brotcid SET a.tglbr=b.tglbr";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
		
		
		
?>

<script src="js/inputmask.js"></script>

<form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">
        
        
        <div class='col-md-12 col-sm-12 col-xs-12'>

            <div class='x_panel'>


                <div hidden class='col-sm-3'>
                    <button type='button' class='btn btn-default btn-xs'>Periode & cabang</button> <span class='required'></span>
                   <div class="form-group">
                        <div class='input-group date' id=''>
                            <input type="text" class="form-control" id='e_periodepilih' name='e_periodepilih' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo "$ptanggal"; ?>' Readonly>
                            <input type="text" class="form-control" id='e_cabangpilih' name='e_cabangpilih' autocomplete="off" required='required'  value='<?PHP echo "$ecabang"; ?>' Readonly>
                        </div>
                   </div>
               </div>
                

                <div class='col-sm-7'>
                    <small>&nbsp;</small>
                   <div class="form-group">
                        <?PHP
                            echo "&nbsp; &nbsp; &nbsp; &nbsp;";
                            echo "<input type='button' class='btn btn-dark btn-sm' id='s-submit' value='Simpan Transfer' onclick=\"disp_confirm_simpantrans('Simpan...?', '')\">";
                       ?>
                   </div>
               </div>
                

            </div>

        </div>
        
        <div class="title_left">
            <h4 style="font-size : 12px;">
                <?PHP
                    $text="Transfer Data BR OTC";
                    echo "<b>$text</b>";
                ?>
            </h4>
        </div>

        <div class="clearfix"></div>
            
            
        <table id='dtabelspgtrsf' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th align="center"></th>
                    <th align="center">No</th>
                    <th align="center">ID</th>
                    <th align="center">Tgl. BR</th>
                    <th align="center">Cabang</th>
                    <th align="center">No BR/Divisi</th>
                    <th align="center">Nama</th>
                    <th align="center">COA</th>
                    <th align="center">Jumlah</th>
                    <th align="center">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $gtotaljumlah=0;
                $query ="select * from $tmp01 order by nama_cabang, namakode";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $idno=$row['noidinput'];
                    $pbrotcid=$row['brotcid'];
                    $pbrotcid2=$row['brotcid2'];
                    $pbrotcid3=$row['brotcid3'];
                    $ptgl=$row['tglbr'];
                    $pidcabang=$row['icabangid'];
                    $pnmcabang=$row['nama_cabang'];
                    $pnodivisi=$row['nodivisi'];
                    $pidinput=$row['idinput'];
                    $pnamakode=$row['namakode'];
                    $pnamacoa=$row['NAMA4'];
                    $pjumlahrp=$row['ttotal'];
                    $pketerangan=$row['keterangan'];
                    
                    $gtotaljumlah=(double)$gtotaljumlah+(double)$pjumlahrp;
                    
                    $ptglbr=date("d/m/Y", strtotime($ptgl));
                    $pjumlah=number_format($pjumlahrp,0,",",",");
                    
                    
                    $cekbox = "<input type=checkbox value='$idno' id='chkbox_br[$idno]' name='chkbox_br[$idno]' checked>";
                    if (!empty($pbrotcid)) {
                        $cekbox="";
                    }
                    
                    $psubkodeid=$row['subpost'];
                    $pkodeid=$row['kodeid'];
                    $pcoa=$row['coa4'];
                    
                    $iket="1";
                    if ($psubkodeid=="03" AND $pkodeid="08" AND !empty($pbrotcid2)) {
                        $pbrotcid=$pbrotcid2;
                        $iket="2";
                    }
                    if ($psubkodeid=="10" AND $pkodeid="93" AND !empty($pbrotcid3)) {
                        $pbrotcid=$pbrotcid3;
                        $iket="3";
                    }
                    
                    $txt_brotcid="<input type='text' value='$pbrotcid' id='txtbrotcid[$idno]' name='txtbrotcid[$idno]' size='8px' Readonly>";
                    $txt_idinputspd="<input type='text' value='$pidinput' id='txtidinputspd[$idno]' name='txtidinputspd[$idno]' size='8px' Readonly>";
                    $txt_nodivisi="<input type='text' value='$pnodivisi' id='txtnodivisi[$idno]' name='txtnodivisi[$idno]' size='8px' Readonly>";
                    $txt_cabangid="<input type='text' value='$pidcabang' id='txtidcabang[$idno]' name='txtidcabang[$idno]' size='8px' Readonly>";
                    $txt_alokid="";
                    //$txt_alokid="<input type='text' value='$palokid' id='txtalokid[$idno]' name='txtalokid[$idno]' size='8px' Readonly>";
                    $txt_subkode="<input type='text' value='$psubkodeid' id='txtsubkode[$idno]' name='txtsubkode[$idno]' size='8px' Readonly>";
                    $txt_kodeid="<input type='text' value='$pkodeid' id='txtkodeid[$idno]' name='txtkodeid[$idno]' size='8px' Readonly>";
                    $txt_tglbr="<input type='text' value='$ptgl' id='txttglbr[$idno]' name='txttglbr[$idno]' size='8px' Readonly>";
                    $txt_ket="<input type='text' value='$pketerangan' id='txtket[$idno]' name='txtket[$idno]' size='8px' Readonly>";
                    $txt_jml="<input type='text' value='$pjumlahrp' id='txtjmlrp[$idno]' name='txtjmlrp[$idno]' size='8px' Readonly>";
                    $txt_coa="<input type='text' value='$pcoa' id='txtcoa[$idno]' name='txtcoa[$idno]' size='8px' Readonly>";
                    
                    $f_text="<div hidden>$txt_brotcid $txt_idinputspd $txt_nodivisi $txt_cabangid $txt_alokid $txt_subkode $txt_kodeid $txt_tglbr $txt_ket $txt_jml $txt_coa</div>";
                    
                    $hapusdata= "<input type='button' class='btn btn-danger btn-xs' id='s-hapus' value='Hapus' onclick=\"HapusDataTransBR('hapus', '$pbrotcid', '$pidinput', '$iket')\">";
                    $hapusdata="";
                    
                    if (!empty($pbrotcid)) {
                        $cekbox=$hapusdata;
                    }
                    
                    echo "<tr>";
                    echo "<td nowrap>$cekbox</td>";
                    echo "<td nowrap>$no $f_text</td>";
                    echo "<td nowrap>$pbrotcid</td>";
                    echo "<td nowrap>$ptglbr</td>";
                    echo "<td nowrap>$pnmcabang</td>";
                    echo "<td nowrap>$pnodivisi</td>";
                    echo "<td nowrap>$pnamakode</td>";
                    echo "<td nowrap>$pnamacoa</td>";
                    echo "<td nowrap>$pjumlah</td>";
                    echo "<td nowrap>$pketerangan</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                
                $gtotaljumlah=number_format($gtotaljumlah,0,",",",");
                
                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>$gtotaljumlah</b></td>";
                echo "<td nowrap></td>";
                echo "</tr>";
                
                ?>
            </tbody>
        </table>
            
    </div>
    
</form>

<script>
    $(document).ready(function() {
        var dataTable = $('#dtabelspgtrsf').DataTable( {
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "ordering": false,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [8] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7,8,9] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 440,
            "scrollX": true/*,
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            responsive: true*/
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    function disp_confirm_simpantrans(pText_,ket)  {
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/md_m_spg_prosesfin/simpan_transferbr.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    function HapusDataTransBR(eact, abridotc, aidinput, iket)  {
        if (abridotc=="") {
            alert("tidak ada data yang dihapus..."); return false;
        }
        if (aidinput=="") {
            alert("tidak ada data yang dihapus..."); return false;
        }
        
        var pText_="Simpan";
        if (eact=="hapus") var pText_="Hapus...?";
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                $.ajax({
                    type:"post",
                    url:"module/md_m_spg_prosesfin/viewdata.php?module=hapusdatabrtrans&act="+eact+"&idmenu="+idmenu,
                    data:"ubridotc="+abridotc+"&uidinput="+aidinput+"&uket="+iket,
                    success:function(data){
                        if (data.length > 1) {
                            alert(data);
                        }else{
                            TransferData('1');
                        }
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
    }
</script>

<style>
    .divnone {
        display: none;
    }
    #dtabelspgtrsf th {
        font-size: 13px;
    }
    #dtabelspgtrsf td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>
<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
?>