<?php
    session_start();
    include "../../config/koneksimysqli.php";
    $cnit=$cnmy;
    $dbname="dbmaster";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
   
    $userid=$_SESSION['IDCARD'];
    if (empty($userid)) {
        echo "tidak ada data yang diproses... SILAKAN LOGIN ULANG";
        exit;
    }
    
    $pigroup_pillih = $_POST['e_idgroup'];
    $ptgl_pillih = $_POST['e_per1'];
    $ptgl_pillih= date("Ym", strtotime($ptgl_pillih));
    
    
    $stsreport = $_POST['e_sts'];
    $scaperiode1 = $_POST['e_periodeca1'];
    $scaperiode2 = $_POST['e_periodeca2'];
    
    $psaldo = $_POST['e_saldo'];
    $pjmltransf = $_POST['e_jmltrsf'];
    
    $psaldo=str_replace(",","", $psaldo);// jumlah
    $pjmltransf=str_replace(",","", $pjmltransf);// input ke field jumlah3
    
    //
    if ($act=='simpanbankkeluar' OR $act=='hapubankkeluar') {
        
        
        if ($module=='closingbrlkca2' AND $act=='simpanbankkeluar')
        {
            
            $pnobukti=$_POST['e_bukti'];
            $pnobukti2=$_POST['e_bukti2'];
            $pbuktiperiode=$_POST['e_bukti_periode'];
            $pbuktithnbln=$_POST['e_bukti_blnthn'];

            if (empty($pnobukti)) $pnobukti=0;
            if (empty($pnobukti2)) $pnobukti2=0;
           
            $ptgl01 = $_POST['e_periode01'];
            $ptglkeluar= date("Y-m-d", strtotime($ptgl01));
            
            $pjenis="2";
            $psubkode="21";
            $pcoa="000-0";//intransit jkt 
            //$pcoa="000";//intransit sby
            $pdivisi="HO";
            $pstatus="1";

            $pnobrid="";
            $pnoslip="";
            $pket="";
            
            $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idinputbank,8)) as NOURUT from $dbname.t_suratdana_bank");
            $ketemu=  mysqli_num_rows($sql);
            $awal=8; $urut=1; $kodenya=""; $periode=date('Ymd');
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                if (empty($o['NOURUT'])) $o['NOURUT']=0;
                $urut=$o['NOURUT']+1;
                $jml=  strlen($urut);
                $awal=$awal-$jml;
                $kodenya="BN".str_repeat("0", $awal).$urut;
            }else{
                $kodenya="BN00000001";
            }
            
            $pidinputspd = $_POST['e_idinputpd'];//idinput spd
            
            $query = "select kodeid, subkode, nomor, nodivisi from dbmaster.t_suratdana_br WHERE idinput='$pidinputspd'";
            $tampil= mysqli_query($cnit, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0){
                $sc= mysqli_fetch_array($tampil);
                $pnospd=$sc['nomor'];
                $pnodivisi=$sc['nodivisi'];
                $pjenis=$sc['kodeid'];
                $psubkode=$sc['subkode'];
            }
            
            
            
            if (!empty($kodenya)) {
                
                $query = "SELECT nobbk FROM dbmaster.t_setup_bukti WHERE bulantahun='$pbuktiperiode'";
                $showkan= mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($showkan);
                if ($ketemu==0){
                    mysqli_query($cnmy, "INSERT INTO dbmaster.t_setup_bukti (bulantahun, nobbk)VALUES('$pbuktiperiode', '$pnobukti')");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                }else{

                    $nox= mysqli_fetch_array($showkan);
                    $pno_asli_bukti=$nox['nobbk'];
                    if (empty($pno_asli_bukti)) $pno_asli_bukti="1500";
                    $pno_asli_bukti=(double)$pno_asli_bukti+1;


                    $isimpan_bukti=true;
                    if ((double)$pnobukti==(double)$pnobukti2){
                        $pnobukti=$pno_asli_bukti;//dibuat sama karena tidak ada perubahan
                    }elseif ((double)$pnobukti<(double)$pnobukti2){
                        $isimpan_bukti=false;
                    }else{
                    }


                    if ($isimpan_bukti==true) {
                        mysqli_query($cnmy, "UPDATE dbmaster.t_setup_bukti SET nobbk='$pnobukti' WHERE bulantahun='$pbuktiperiode'");
                        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    }

                }
                
                $pnobukti="BBK".$pnobukti.$pbuktithnbln;
            
                $query = "INSERT INTO $dbname.t_suratdana_bank (stsinput, idinputbank, tanggal, coa4, kodeid, subkode, idinput, nomor, nodivisi, "
                        . " nobukti, divisi, sts, jumlah, keterangan, brid, noslip, userid)values"
                        . "('K', '$kodenya', '$ptglkeluar', '$pcoa', '$pjenis', '$psubkode', '$pidinputspd', '$pnospd', '$pnodivisi', "
                        . " '$pnobukti', '', '$pstatus', '$pjmltransf', '$pket', '$pnobrid', '$pnoslip', '$userid')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
                $query = "UPDATE $dbname.t_brrutin_ca_close_head SET idinputbank='$kodenya', jmltrans='$pjmltransf' WHERE "
                        . " DATE_FORMAT(bulan,'%Y%m')='$ptgl_pillih' and IFNULL(igroup,'')='$pigroup_pillih'";
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit);
                if (!empty($erropesan)) { 
                    echo "Error..."; 
                    $query = "UPDATE $dbname.t_suratdana_bank SET stsnonaktif='Y' WHERE idinputbank='$kodenya' AND nodivisi='$pnodivisi'";
                    mysqli_query($cnit, $query);
                    exit;
                }
                
                $pkode="2";
                $psubkode="21";
                $query = "UPDATE $dbname.t_suratdana_br SET jumlah3='$pjmltransf' WHERE idinput='$pidinputspd' and kodeid='$pkode' and subkode='$psubkode' AND karyawanid='$userid'";
                mysqli_query($cnit, $query);
                
                
            }
            
            //echo "$pnobukti, $pnobukti2, $pbuktiperiode, $pbuktithnbln"; exit;
            
        }elseif ($module=='closingbrlkca2' AND $act=='hapubankkeluar')
        {
            $kodenya = $_POST['e_idinputbank'];//idinput spd
            if (empty($kodenya)) {
                echo "kosong..."; exit;
            }
            
            
            $pkode="2";
            $psubkode="21";

            $query = "UPDATE $dbname.t_brrutin_ca_close_head SET idinputbank='' WHERE idinputbank='$kodenya' AND "
                    . " DATE_FORMAT(bulan,'%Y%m')='$ptgl_pillih' and IFNULL(igroup,'')='$pigroup_pillih'";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "Error..."; exit; }

            $query = "UPDATE $dbname.t_suratdana_bank SET stsnonaktif='Y' WHERE idinputbank='$kodenya' and kodeid='$pkode' and subkode='$psubkode'";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "Error..."; exit; }
            
            
        }
        
        
        
    }else{
        
        
        if ($module=='closingbrlkca2' AND $act=='simpandana')
        {


            $kodenya = $_POST['e_idinputpd'];//idinput spd
            $pnodivisi = $_POST['e_nomordiv'];//no br/divisi

            $ncari="select MAX(idinput) as NOURUT from $dbname.t_suratdana_br";
            $sql=  mysqli_query($cnit, $ncari);
            $ketemu=  mysqli_num_rows($sql);
            $awal=7; $urut=1; $kodenya="";
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $urut=$o['NOURUT']+1;
                $kodenya=$urut;
            }

            $pcoa="101-02-002";
            $pkode="2";
            $psubkode="21";
            $kodeinput="I";

            $myinpperiode1= date("Y-m-01", strtotime($ptgl_pillih));
            $myinpperiode2= date("Y-m-t", strtotime($ptgl_pillih));

            if (!empty($kodenya)) {

                $query = "UPDATE $dbname.t_brrutin_ca_close_head SET idinput='$kodenya' WHERE "
                        . " DATE_FORMAT(bulan,'%Y%m')='$ptgl_pillih' and IFNULL(igroup,'')='$pigroup_pillih'";
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "Error..."; exit; }


                $query = "INSERT INTO $dbname.t_suratdana_br (idinput, divisi, kodeid, subkode, tgl, nodivisi, jumlah, jumlah3, userid, coa4, "
                        . " tglf, tglt, sts, karyawanid, jenis_rpt)values"
                        . "('$kodenya', '', '$pkode', '$psubkode', CURRENT_DATE(), '$pnodivisi', '$psaldo', '$pjmltransf', '$userid', '$pcoa', "
                        . " '$myinpperiode1', '$myinpperiode2', '$stsreport', '$userid', 'K')";
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); 
                if (!empty($erropesan)) { 
                    echo "Error SIMPAN MASTER SPD...";

                    $query = "UPDATE $dbname.t_brrutin_ca_close_head SET idinput=NULL WHERE idinput='$kodenya' AND "
                            . " DATE_FORMAT(bulan,'%Y%m')='$ptgl_pillih' and IFNULL(igroup,'')='$pigroup_pillih'";
                    mysqli_query($cnit, $query);

                    mysqli_close($cnit);
                    exit;
                }

                $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput, bridinput, kodeinput, amount)
                    select distinct '$kodenya' as idinput, idrutin as bridinput, '$kodeinput' as kodeinput, credit as amount 
                    from dbmaster.t_brrutin_ca_close 
                    WHERE IFNULL(idrutin,'')<>'' AND DATE_FORMAT(bulan,'%Y%m')='$ptgl_pillih' and IFNULL(igroup,'')='$pigroup_pillih'";
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); 

                if (!empty($erropesan)) { 

                    $query = "UPDATE $dbname.t_brrutin_ca_close_head SET idinput=NULL WHERE idinput='$kodenya' AND "
                            . " DATE_FORMAT(bulan,'%Y%m')='$ptgl_pillih' and IFNULL(igroup,'')='$pigroup_pillih'";
                    mysqli_query($cnit, $query);

                    $query = "UPDATE $dbname.t_suratdana_br SET stsnonaktif='Y' WHERE idinput='$kodenya' and kodeid='$pkode' and subkode='$psubkode'";
                    mysqli_query($cnit, $query);


                    mysqli_close($cnit);
                    echo "Error DETAIL SPD..."; 
                    exit; 
                }




            }

            //echo "$psaldo, $pjmltransf. idinput : $kodenya, $pnodivisi, <br/>";
        }elseif ($module=='closingbrlkca2' AND $act=='hapuspd')
        {
            $kodenya = $_POST['e_idinputpd'];//idinput spd
            if (empty($kodenya)) {
                echo "kosong..."; exit;
            }

            $pkode="2";
            $psubkode="21";

            $query = "UPDATE $dbname.t_brrutin_ca_close_head SET idinput=NULL WHERE idinput='$kodenya' AND "
                    . " DATE_FORMAT(bulan,'%Y%m')='$ptgl_pillih' and IFNULL(igroup,'')='$pigroup_pillih'";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "Error..."; exit; }

            $query = "UPDATE $dbname.t_suratdana_br SET stsnonaktif='Y' WHERE idinput='$kodenya' and kodeid='$pkode' and subkode='$psubkode'";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "Error..."; exit; }


        }
        
    }
    
    mysqli_close($cnit);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
?>
