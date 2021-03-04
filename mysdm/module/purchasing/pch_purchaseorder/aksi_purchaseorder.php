<?php
session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
$puserid="";
$pidcard="";
$pidgroup="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];
if (isset($_SESSION['GROUP'])) $pidgroup=$_SESSION['GROUP'];


$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];


if ($module=='pchpotransaksi')
{
    if ($act=="hapus") {
        
        if (empty($puserid)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        
        include "../../../config/koneksimysqli.php";
        $pkodenya=$_GET['id'];
        
        $query = "UPDATE dbpurchasing.t_po_transaksi SET stsnonaktif='Y' WHERE idpo='$pkodenya' LIMIT 1";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
        mysqli_close($cnmy);
        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        
    }elseif ($act=="input" OR $act=="update") {
        
        $pcardidlog=$_POST['e_idcardlogin'];
        if (empty($pcardidlog)) $pcardidlog=$pidcard;
        
        if (empty($pcardidlog)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        
        
        include "../../../config/koneksimysqli.php";
        
        $kodenya=$_POST['e_id'];
        $ptgl=$_POST['e_tglberlaku'];
        $ptglkr=$_POST['e_tglkirim'];
        
        $pkdsupp=$_POST['cb_supplier'];
        $pidbayar=$_POST['cb_bayar'];
        $pnotekirim=$_POST['e_noteskirim'];
        $pnote=$_POST['e_notes'];
        
        
        $pjmlusul_h=$_POST['e_jmlusulan'];
        $pppn_h=$_POST['cb_ppn'];
        $pppn_hrp=$_POST['e_jmlppnrp'];
        $pdisc_h=$_POST['e_jmldisc'];
        $pdisc_hrp=$_POST['e_jmldiscrp'];
        $pbulat_h=$_POST['e_jmlbulat'];
        $ptotbayar_h=$_POST['e_jmlbayarrp'];
        
        $jnspph_h=""; $ppph_h=0; $ppph_hrp=0;
        
        
        $pthn= date("Y", strtotime($ptgl));
        $pthnbln= date("ym", strtotime($ptgl));
        $ptglpo= date("Y-m-d", strtotime($ptgl));
        $ptglkirim= date("Y-m-d", strtotime($ptglkr));
        
        if (!empty($pnotekirim)) $pnotekirim = str_replace("'", " ", $pnotekirim);
        if (!empty($pnote)) $pnote = str_replace("'", " ", $pnote);
        
        $pjmlusul_h=str_replace(",","", $pjmlusul_h);
        $pppn_h=str_replace(",","", $pppn_h);
        $pppn_hrp=str_replace(",","", $pppn_hrp);
        $pdisc_h=str_replace(",","", $pdisc_h);
        $pdisc_hrp=str_replace(",","", $pdisc_hrp);
        $pbulat_h=str_replace(",","", $pbulat_h);
        $ptotbayar_h=str_replace(",","", $ptotbayar_h);
        
        $ppph_h=str_replace(",","", $ppph_h);
        $ppph_hrp=str_replace(",","", $ppph_hrp);
        
        $pidkdsup="";
        $pidprpdhanya="";
        unset($pinsert_data_detail);//kosongkan array
        $psimpandata=false;
        foreach ($_POST['chk_detail'] as $no_brid) {
            if (!empty($no_brid)) {
                $pidprpo= $no_brid;
                $pidpr= $_POST['e_txtpr'][$no_brid];
                $pidprd= $_POST['e_txtprd'][$no_brid];
                $pidbrg= $_POST['e_txtidbrg'][$no_brid];
                $pidbrgd= $_POST['e_txtidbrgd'][$no_brid];
                $pnmbrg= $_POST['e_txtnmbrg'][$no_brid];
                $pspcbrg= $_POST['e_txtspcbrg'][$no_brid];
                
                $pdjml= $_POST['e_txtjml'][$no_brid];
                $pdsatuan= $_POST['e_txtsatuan'][$no_brid];
                $pdharga= $_POST['e_txtharga'][$no_brid];
                $pddisc= $_POST['e_txtdisc'][$no_brid];
                $pddiscrp= $_POST['e_txtdiscrp'][$no_brid];
                $pdtotrp= $_POST['e_txtjmltot'][$no_brid];
                
                $pdjml=str_replace(",","", $pdjml);
                $pdharga=str_replace(",","", $pdharga);
                $pddisc=str_replace(",","", $pddisc);
                $pddiscrp=str_replace(",","", $pddiscrp);
                $pdtotrp=str_replace(",","", $pdtotrp);
                
                $pidkdsup .="'".$pidprpo."".$pkdsupp."',";
                $pidprpdhanya .="'".$pidprd."',";
                
                if ((DOUBLE)$pidbrgd==0 OR $pidbrgd=="0000000000") $pidbrgd="";
                
                $pinsert_data_detail[] = "('$pidpr', '$pidprd', '$pidprpo', '$pidbrg', '$pnmbrg', '$pidbrgd', '$pspcbrg', '$pdjml', '$pdsatuan', '$pdharga', '$pddisc', '$pddiscrp', '$pdtotrp', '$pkdsupp')";
                $psimpandata=true;
                
                
                //echo "$pidprpo : idpr : $pidpr, idprd : $pidprd<br/>idbrg : $pidbrg & $pidbrgd<br/>  Jml : $pdjml $pdsatuan hrg : $pdharga, disc : $pddisc, $pddiscrp, tot : $pdtotrp<br/>";
            }
        }
        
        
        if ($psimpandata==true) {
            if (!empty($pidkdsup)) {
                $pidkdsup="(".substr($pidkdsup, 0, -1).")";
                $pidprpdhanya="(".substr($pidprpdhanya, 0, -1).")";
            }else{
                $pidkdsup="('')";
                $pidprpdhanya="('')";
            }
            
            $idusepl=$puserid;
            if (empty($idusepl)) $idusepl=(DOUBLE)$pcardidlog;
            $now=date("mdYhis");
            $tmp01 =" dbtemp.TMPINPTDTPO01_".$puserid."_$now ";
            
            $query = "CREATE TEMPORARY TABLE $tmp01 (idpr varchar(15), idpr_d bigint(10) ZEROFILL, idpr_po bigint(10) ZEROFILL, "
                    . " idbarang varchar(10), namabarang varchar(150), "
                    . " idbarang_d int(10) ZEROFILL, spesifikasi varchar(500), jumlah double(20,2), satuan varchar(100), harga double(20,2), "
                    . " disc decimal(20,2), discrp decimal(20,2), totalrp decimal(20,2), "
                    . " idkategori int(4), idsatuan int(4), kdsupp varchar(5), idtipe int(5), ibaru varchar(1) )";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            
            $query_detail="INSERT INTO $tmp01 (idpr, idpr_d, idpr_po, idbarang, namabarang, idbarang_d, spesifikasi, "
                    . " jumlah, satuan, harga, disc, discrp, totalrp, kdsupp) VALUES ".implode(', ', $pinsert_data_detail);
            mysqli_query($cnmy, $query_detail);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
                //cek update
                $query = "UPDATE $tmp01 as a JOIN dbpurchasing.t_pr_barang_d as b on "
                        . " TRIM(REPLACE(REPLACE(REPLACE(a.spesifikasi, '\n', ''), '\r', ''), '\t', ''))=TRIM(REPLACE(REPLACE(REPLACE(b.spesifikasi1, '\n', ''), '\r', ''), '\t', '')) AND "
                        . " IFNULL(a.idbarang,'')=IFNULL(b.idbarang,'') "
                        . " SET a.idbarang_d=b.idbarang_d, b.harga=a.harga WHERE IFNULL(a.idbarang,'')<>''";
                mysqli_query($cnmy, $query); 
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }


                $query = "INSERT INTO dbpurchasing.t_pr_barang_d (idbarang, spesifikasi1, harga)"
                        . "SELECT DISTINCT idbarang, spesifikasi, harga FROM $tmp01 WHERE IFNULL(idbarang_d,'') IN ('', '0000000000')";
                mysqli_query($cnmy, $query); 
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                //update lagi (dibalik dengan yang diatas)
                $query = "UPDATE $tmp01 as a JOIN dbpurchasing.t_pr_barang_d as b on "
                        . " TRIM(REPLACE(REPLACE(REPLACE(a.spesifikasi, '\n', ''), '\r', ''), '\t', ''))=TRIM(REPLACE(REPLACE(REPLACE(b.spesifikasi1, '\n', ''), '\r', ''), '\t', '')) AND "
                        . " IFNULL(a.idbarang,'')=IFNULL(b.idbarang,'') "
                        . " SET a.idbarang_d=b.idbarang_d, b.harga=a.harga WHERE IFNULL(a.idbarang,'')<>''";
                mysqli_query($cnmy, $query); 
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
                $query = "UPDATE dbpurchasing.t_pr_transaksi_po as a JOIN $tmp01 as b on a.idpr=b.idpr AND a.idpr_d=b.idpr_d AND a.idbarang=b.idbarang "
                        . " AND TRIM(REPLACE(REPLACE(REPLACE(a.spesifikasi1, '\n', ''), '\r', ''), '\t', ''))=TRIM(REPLACE(REPLACE(REPLACE(b.spesifikasi, '\n', ''), '\r', ''), '\t', '')) "
                        . " SET "
                        . " a.userid='$pcardidlog', a.idbarang_d=b.idbarang_d WHERE a.idpr_d IN $pidprpdhanya";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                
            
            if ($act=="input") {

                $sql=  mysqli_query($cnmy, "select po as NOURUT from dbmaster.t_setup_tahun where tahun='$pthn'");
                $ketemu=  mysqli_num_rows($sql);
                $awal=5; $nurut=1; $kodenya=""; $padaurut=false;
                if ($ketemu>0){
                    $o=  mysqli_fetch_array($sql);
                    if (empty($o['NOURUT'])) $o['NOURUT']=0;
                    $nurut=$o['NOURUT']+1;
                    $padaurut=true;
                }else{
                    $nurut=1;
                }
                $jml=  strlen($nurut);
                $awal=$awal-$jml;
                $kodenya="PO".$pthnbln."".str_repeat("0", $awal).$nurut;

                if ($padaurut==false) {
                    mysqli_query($cnmy, "INSERT INTO dbmaster.t_setup_tahun (tahun, po)VALUES('$pthn', '0')");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                }

                mysqli_query($cnmy, "UPDATE dbmaster.t_setup_tahun SET po=IFNULL(po,0)+1 WHERE tahun='$pthn'");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            }else{
                $kodenya=$_POST['e_id'];
            }
            
            
            
            
            if (empty($kodenya)) {
                echo "ID KOSONG";
                mysqli_close($cnmy);
                exit;
            }
        
            if ($act=="input") {
                $query = "INSERT INTO dbttd.t_po_transaksi_ttd(idpo)VALUES('$kodenya')";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }


                $query = "INSERT INTO dbpurchasing.t_po_transaksi (idpo, tanggal, kdsupp, notes, idbayar, tglkirim, note_kirim, ppn, ppnrp, disc, discrp, "
                        . " pembulatan, totalrp, "
                        . " jnspph, pph, pphrp, userid, karyawanid)values"
                        . "('$kodenya', '$ptglpo', '$pkdsupp', '$pnote', '$pidbayar', '$ptglkirim', '$pnotekirim', '$pppn_h', '$pppn_hrp', '$pdisc_h', '$pdisc_hrp', "
                        . " '$pbulat_h', '$ptotbayar_h', "
                        . " '$jnspph_h', '$ppph_h', '$ppph_hrp', '$pcardidlog', '$pcardidlog')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            }
        
        
            $query = "UPDATE dbpurchasing.t_po_transaksi SET tanggal='$ptglpo', kdsupp='$pkdsupp', "
                    . " notes='$pnote', idbayar='$pidbayar', tglkirim='$ptglkirim', "
                    . " note_kirim='$pnotekirim', ppn='$pppn_h', ppnrp='$pppn_hrp', "
                    . " disc='$pdisc_h', discrp='$pdisc_hrp', pembulatan='$pbulat_h', totalrp='$ptotbayar_h', "
                    . " jnspph='$jnspph_h', pph='$ppph_h', pphrp='$ppph_hrp', userid='$pcardidlog', karyawanid='$pcardidlog' WHERE "
                    . " idpo='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
            if ($act=="input") {
                $pimgttd=$_POST['txtgambar'];
                $query = "update dbttd.t_po_transaksi_ttd set gambar='$pimgttd' WHERE idpo='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }
            
            $query = "UPDATE dbpurchasing.t_pr_transaksi_po as a JOIN $tmp01 as b on a.idpr_po=b.idpr_po AND a.kdsupp=b.kdsupp SET "
                    . " a.jumlah=b.jumlah, a.satuan=b.satuan, a.harga=b.harga, a.disc=b.disc, a.discrp=b.discrp, "
                    . " a.totalrp=b.totalrp, a.userid='$pcardidlog', a.idbarang_d=b.idbarang_d WHERE CONCAT(a.idpr_po, IFNULL(a.kdsupp,'')) IN $pidkdsup";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            
            $query = "DELETE from dbpurchasing.t_po_transaksi_d WHERE idpo='$kodenya'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            $query = "INSERT INTO dbpurchasing.t_po_transaksi_d (idpo, idpr_po)"
                    . "SELECT '$kodenya' as idpo, idpr_po FROM $tmp01";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            
            //echo "$pidkdsup - $kodenya : $ptglpo & $ptglkirim, supp : $pkdsupp, $pidbayar, $pnotekirim, note : $pnote, jml : $pjmlusul_h, ppn : $pppn_h, $pppn_hrp, disc : $pdisc_h, $pdisc_hrp, bulat: $pbulat_h, byr : $ptotbayar_h<br/>PPH $jnspph_h, $ppph_h, $ppph_hrp";
            
            
            mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
            
            mysqli_close($cnmy);
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        }else{
            echo "Tidak ada data yang disimpan...";
        }
        
    }
    
    
}