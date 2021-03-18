<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);

    session_start();

    $piduser="";
    if (isset($_SESSION['IDCARD'])) {
        $piduser=$_SESSION['IDCARD'];
    }

    include("../../config/koneksimysqli.php");

    $berhasil="Tidak ada data yang disimpan...";


                //iid, iidcard, ists, ibnkid, isdhcls, inodivisi, 
                //itglkeluar, inobukti, ijumlah, iket, inodiv_dari

    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    if ($pmodule=="brdanabank" AND ($pact=="updatedibank" OR $pact=="simpandatabankkeluar") ) {


        $pidinput=$_POST['uid'];
        $pidcard=$_POST['uidcard'];
        $psts=$_POST['usts'];
        $pbankid=$_POST['ubnkid'];
        $psdhcls=$_POST['usdhcls'];
        $pnodivisi=$_POST['unodivisi'];
        $ptgl=$_POST['utglkeluar'];
        $pnobukti=$_POST['unobukti'];
        $pjumlah=$_POST['ujumlah'];
        $pket=$_POST['uket'];
        $pdivdari=$_POST['unodiv_dari'];
        
        if (empty($pidcard)) {
            if (!empty($piduser)) $pidcard=$piduser;
        }
        
        if (empty($pidcard)) {
            echo "Anda harus login ulang..."; mysqli_close($cnmy); exit;
        }

        //$berhasil="id : $pidinput, idcard : $pidcard, sts input : $psts, id bank : $pbankid, 
        //    sts cls : $psdhcls, Nodivisi : $pnodivisi, tgl : $ptgl, Nobukti : $pnobukti, 
        //    Jml : $pjumlah, Ket : $pket, Div Dari : $pdivdari";
        //$berhasil = "module : $pmodule, menu : $pidmenu, act : $pact";

        $ptgl01 = str_replace('/', '-', $ptgl);
        $ptglkeluar= date("Y-m-d", strtotime($ptgl01));
        $pbulan= date("Ym", strtotime($ptgl01));
        $pjumlah=str_replace(",","", $pjumlah);

        $query = "select bulan from dbmaster.t_bank_saldo WHERE DATE_FORMAT(bulan,'%Y%m')='$pbulan'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {
            echo "Bulan tersebut sudah Closing... Tidak ada data yang diproses...";
            mysqli_close($cnmy);
            exit;
        }

        
        if ($pact=="simpandatabankkeluar") {

            $query = "";

            if (empty($pnobukti)) {
                $berhasil="No Bukti / BBK Kosong, tidak ada data yang diupdate...";
            }else{

                $query = "INSERT INTO dbmaster.t_suratdana_bank
                    ()VALUES
                    ()";

                $berhasil="Simpan berhasil...";
            }
        }elseif ($pact=="updatedibank") {

            if (empty($pbankid)) {
                $berhasil="Id Bank Kosong, tidak ada data yang diupdate...";
            }else{

                $query = "Update dbmaster.t_suratdana_bank SET tanggal='$ptglkeluar', 
                    jumlah='$pjumlah', keterangan='$pket' WHERE 
                    idinputbank='$pbankid' AND idinput='$pidinput' AND userid='$pidcard' LIMIT 1";

                $berhasil="Update berhasil...";
            }

        }
    
    }
    
    mysqli_close($cnmy);

    echo $berhasil;

    
?>