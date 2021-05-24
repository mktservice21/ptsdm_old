<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);

    session_start();

    $piduser="";
    if (isset($_SESSION['IDCARD'])) {
        $piduser=$_SESSION['IDCARD'];
    }

    include("../../../config/koneksimysqli.php");
    include "../../../config/fungsi_combo.php";

    $berhasil="Tidak ada data yang disimpan...";


                //iid, iidcard, ists, ibnkid, isdhcls, inodivisi, 
                //itglkeluar, inobukti, ijumlah, iket, inodiv_dari

    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    if ($pmodule=="brdanabankbyfin" AND ($pact=="updatedibank" OR $pact=="simpandatabankkeluar") ) {

        $pno_asli_bukti=1500;
        $pstatus="1";
        $pidcard=$_POST['uidcard'];
        $psts=$_POST['usts'];
        $kodenya=$_POST['ubnkid'];
        $psdhcls=$_POST['usdhcls'];
        $pasltgl=$_POST['utglaslkeluar'];
        $ptgl=$_POST['utglkeluar'];
        $pnobukti=$_POST['unobukti'];
        $pket=$_POST['uket'];
        $pdivdari=$_POST['unodiv_dari'];
        
        $pidinputspd=$_POST['uid'];
        $pspdnomor=$_POST['unospd'];
        $pnodivisi=$_POST['unodivisi'];
        $pjumlah=$_POST['ujumlah'];
        $pkodeid=$_POST['ukode'];
        $psubkode=$_POST['usubkode'];
        $pdivisi=$_POST['udivisi'];
        $pcoabkeluar=$_POST['ucoa'];
        
        //echo "$pidinputspd, $pspdnomor, $pnodivisi, $pjumlah, $pkodeid, $psubkode, $pdivisi, COA : $pcoabkeluar"; exit;
        
        if (empty($pidcard)) {
            if (!empty($piduser)) $pidcard=$piduser;
        }
        
        if (empty($pidcard)) {
            echo "Anda harus login ulang..."; mysqli_close($cnmy); exit;
        }

        //$berhasil="id : $pidinputspd, idcard : $pidcard, sts input : $psts, id bank : $kodenya, 
        //    sts cls : $psdhcls, Nodivisi : $pnodivisi, tgl : $ptgl, Nobukti : $pnobukti, 
        //    Jml : $pjumlah, Ket : $pket, Div Dari : $pdivdari";
        //$berhasil = "module : $pmodule, menu : $pidmenu, act : $pact";

        $ptgl01 = str_replace('/', '-', $ptgl);
        $ptglkeluar= date("Y-m-d", strtotime($ptgl01));
        $pbulan= date("Ym", strtotime($ptgl01));
        
        $pblnini = date('m', strtotime($ptgl01));
        $pthnini = date('Y', strtotime($ptgl01));
        $pthnini_bln = date('Ym', strtotime($ptgl01));
        
        $paslbulan="";
        if (!empty($pasltgl)) {
            $pasltgl01 = str_replace('/', '-', $pasltgl);
            $paslbulan= date("Ym", strtotime($pasltgl01));
        }
        
        $pjumlah=str_replace(",","", $pjumlah);

        $query = "select bulan from dbmaster.t_bank_saldo WHERE DATE_FORMAT(bulan,'%Y%m')='$pbulan'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {
            echo "Bulan tersebut sudah Closing... Tidak ada data yang diproses...";
            mysqli_close($cnmy);
            exit;
        }

        //echo "$pbulan & $paslbulan"; exit;
        
        $bolehpilihnobukti=false;
        if ($pact=="simpandatabankkeluar") {
            $bolehpilihnobukti=true;
        }else{
            if ($pbulan<>$paslbulan) $bolehpilihnobukti=true;
        }
        
        
        
        if ($bolehpilihnobukti==true) {

            $query = "SELECT nobbk FROM dbmaster.t_setup_bukti WHERE bulantahun='$pbulan'";
            $showkan= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($showkan);
            if ((INT)$ketemu==0){
                
                mysqli_query($cnmy, "INSERT INTO dbmaster.t_setup_bukti (bulantahun, nobbk)VALUES('$pbulan', '$pno_asli_bukti')");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysql_close($cnmy); exit; }
                
            }else{
                $nox= mysqli_fetch_array($showkan);
                $pno_asli_bukti=$nox['nobbk'];
                
                if (empty($pno_asli_bukti) OR (INT)$pno_asli_bukti==0) $pno_asli_bukti=1500;
            }
            $pno_asli_bukti++;
            $mbulan=CariBulanHuruf($pblnini);
            $pnobukti = "BBK".$pno_asli_bukti."/".$mbulan."/".$pthnini;
            
            $query=  mysqli_query($cnmy, "select idinputbank from dbmaster.t_suratdana_bank WHERE nobukti='$pnobukti' AND IFNULL(stsnonaktif,'')<>'Y'");
            $ketemub=  mysqli_num_rows($query);
            if ((INT)$ketemub>0){
                echo "No Bukti sudah ada...";
                mysqli_close($cnmy);
                exit;
            }
            
            
            if (!empty($pnobukti)) {
                mysqli_query($cnmy, "UPDATE dbmaster.t_setup_bukti SET nobbk='$pno_asli_bukti' WHERE bulantahun='$pbulan'");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            }
            
        }
        
        
        //echo "$pbulan & $paslbulan, ($bolehpilihnobukti) nobukti : $pnobukti"; exit;
        
        if ($pact=="simpandatabankkeluar") {

            
            //echo "$pnobukti"; exit;

            if (empty($pnobukti)) {
                $berhasil="No Bukti / BBK Kosong, tidak ada data yang diupdate...";
            }else{
                
        
                $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idinputbank,8)) as NOURUT from dbmaster.t_suratdana_bank");
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
        
                if (!empty($kodenya)) {
                    
                    $query = "INSERT INTO dbmaster.t_suratdana_bank (stsinput, idinputbank, tanggal, coa4, kodeid, subkode, idinput, nomor, nodivisi, "
                            . " nobukti, divisi, sts, jumlah, keterangan, userid)values"
                            . "('K', '$kodenya', '$ptglkeluar', '$pcoabkeluar', '$pkodeid', '$psubkode', '$pidinputspd', '$pspdnomor', '$pnodivisi', "
                            . " '$pnobukti', '$pdivisi', '$pstatus', '$pjumlah', '$pket', '$pidcard')";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                    
                    $berhasil="Simpan berhasil...";
                    
                }
                
            }
        }elseif ($pact=="updatedibank") {

            if (empty($kodenya)) {
                $berhasil="Id Bank Kosong, tidak ada data yang diupdate...";
            }else{

                $query = "Update dbmaster.t_suratdana_bank SET tanggal='$ptglkeluar', 
                    jumlah='$pjumlah', keterangan='$pket' WHERE 
                    idinputbank='$kodenya' AND idinput='$pidinputspd' LIMIT 1";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysql_close($cnmy); exit; }
                
                if ($bolehpilihnobukti==true AND !empty($pnobukti)) {
                    $query = "UPDATE dbmaster.t_suratdana_bank SET nobukti='$pnobukti' WHERE idinputbank='$kodenya' AND idinput='$pidinputspd' LIMIT 1";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysql_close($cnmy); exit; }
                }
                $berhasil="Update berhasil...";
                
            }

        }
    
    }
    
    mysqli_close($cnmy);

    echo $berhasil;

    
?>