<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];



if ($pmodule=="cekdatasudahada") {
    include "../../config/koneksimysqli.php";

    $pidinput=$_POST['uid'];
    $ptgl=$_POST['utgl'];
    $pkaryawanid=$_POST['ukaryawan'];

    $ptanggal= date("Y-m-d", strtotime($ptgl));

    $boleh="boleh";

    $query = "select tanggal from hrd.dkd_new0 where idinput<>'$pidinput' AND tanggal='$ptanggal' And karyawanid='$pkaryawanid'";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $boleh="Tanggal tersebut sudah ada..., silakan pilih tanggal yang lain";
    }

    mysqli_close($cnmy);

    echo $boleh;
}elseif ($pmodule=="cekdatasudahadabytgl") {
    include "../../config/koneksimysqli.php";

    $pidinput=$_POST['uid'];
    $pidinput=$_POST['uact'];
    $ptgl=$_POST['utgl'];
    $ptgledit=$_POST['utgledit'];
    $pkaryawanid=$_POST['ukaryawan'];

    $ptanggal= date("Y-m-d", strtotime($ptgl));
    $pedittgl= date("Y-m-d", strtotime($ptgledit));

    $boleh="boleh";
    
    $filteredit=" ";
    if ($pidinput=="editdata") {
        if ($ptanggal==$pedittgl) {
            mysqli_close($cnmy);

            echo $boleh; exit;
        }
    }
    
    $pbolehinputakv=true;
    $pbolehinputcal=true;
    
    $query = "select tanggal from hrd.dkd_new0 where tanggal='$ptanggal' And karyawanid='$pkaryawanid' $filteredit";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $pbolehinputakv=false;
        $boleh="Tanggal tersebut sudah ada..., silakan pilih tanggal yang lain";
    }
    
    $query = "select tanggal from hrd.dkd_new1 where tanggal='$ptanggal' And karyawanid='$pkaryawanid' $filteredit";
    $tampil1=mysqli_query($cnmy, $query);
    $ketemu1=mysqli_num_rows($tampil1);
    if ((INT)$ketemu1>0) {
        $pbolehinputcal=false;
        $boleh="Tanggal tersebut sudah ada..., silakan pilih tanggal yang lain";
    }
    

    mysqli_close($cnmy);
    
    if ($pidinput=="editdata") {
    }else{
        
        if ($pbolehinputakv == false AND $pbolehinputcal == false) {
        }else{
            if ($pbolehinputakv == true AND $pbolehinputcal == true) {
            }else{
                if ($pbolehinputakv == true) $boleh="aktivitas";
                if ($pbolehinputcal == true) $boleh="call";
            }
        }
        
    }
    
    echo $boleh; exit;
}elseif ($pmodule=="cekdatasudahadareal") {
    include "../../config/koneksimysqli.php";

    $pidinput=$_POST['uid'];
    $ptgl=$_POST['utgl'];
    $pkaryawanid=$_POST['ukaryawan'];

    $ptanggal= date("Y-m-d", strtotime($ptgl));

    $boleh="boleh";

    $query = "select tanggal from hrd.dkd_new_real0 where idinput<>'$pidinput' AND tanggal='$ptanggal' And karyawanid='$pkaryawanid'";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $boleh="Tanggal tersebut sudah ada..., silakan pilih tanggal yang lain";
    }

    mysqli_close($cnmy);

    echo $boleh; exit;
}elseif ($pmodule=="cekdatasudahadarealvisit") {
    include "../../config/koneksimysqli.php";

    $pidinput=$_POST['uid'];
    $ptgl=$_POST['utgl'];
    $pkaryawanid=$_POST['ukaryawan'];
    $pdokterid=$_POST['uidoktid'];

    $ptanggal= date("Y-m-d", strtotime($ptgl));

    $boleh="boleh";

    $query = "select tanggal from hrd.dkd_new_real1 where tanggal='$ptanggal' And karyawanid='$pkaryawanid' AND dokterid='$pdokterid'";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $boleh="Dokter dan Tanggal tersebut sudah ada..., silakan pilih tanggal yang lain";
    }

    mysqli_close($cnmy);

    echo $boleh; exit;
}elseif ($pmodule=="cekdatasudahadarealactivity") {
    include "../../config/koneksimysqli.php";

    $pidinput=$_POST['uid'];
    $ptgl=$_POST['utgl'];
    $pkaryawanid=$_POST['ukaryawan'];
    $pketid=$_POST['uidket'];

    $ptanggal= date("Y-m-d", strtotime($ptgl));

    $boleh="boleh";

    $query = "select tanggal from hrd.dkd_new_real0 where tanggal='$ptanggal' And karyawanid='$pkaryawanid'";// AND ketid='$pketid'
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $boleh="Tanggal tersebut sudah ada..., silakan pilih tanggal yang lain";
    }

    mysqli_close($cnmy);

    echo $boleh; exit;
}elseif ($pmodule=="viewdatadoktercabang") {
    include "../../config/koneksimysqli.php";
    $pidcab=$_POST['uidcab'];
    $pkodeid=$_POST['skode'];
    $pcabpilih=$_POST['ukdcab'];
    $pdoktpilih=$_POST['ukddokt'];

    $pkodecabang=$pidcab;
    if ((INT)$pkodeid==2) $pkodecabang=$pcabpilih;

    $query = "select `id` as iddokter, namalengkap, gelar, spesialis from dr.masterdokter WHERE 1=1 ";
    $query .=" AND icabangid='$pkodecabang' ";
    $query .=" order by namalengkap, `id`";
    $tampilket= mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampilket);
    //if ((INT)$ketemu<=0) 
        echo "<option value='' selected>-- Pilih --</option>";
    while ($du= mysqli_fetch_array($tampilket)) {
        $niddokt=$du['iddokter'];
        $nnmdokt=$du['namalengkap'];
        $ngelar=$du['gelar'];
        $nspesial=$du['spesialis'];
        
        if (!empty($pnmdokt)) $pnmdokt=rtrim($pnmdokt, ',');
        
        if ($niddokt==$pdoktpilih)
            echo "<option value='$niddokt' selected>$nnmdokt ($ngelar), $nspesial - $niddokt</option>";
        else
            echo "<option value='$niddokt'>$nnmdokt ($ngelar), $nspesial - $niddokt</option>";

    }
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdatatanggal") {
    $tgl_pertama=$_POST['utgl'];
    $ptglpilih = date('Y-m-d', strtotime($tgl_pertama));
    $p_tgl = date('d', strtotime($ptglpilih));
    echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih' checked> $p_tgl &nbsp; &nbsp; ";
    for ($ix=1;$ix<5;$ix++) {
        $ptglpilih = date('Y-m-d', strtotime('+1 days', strtotime($ptglpilih)));
        $p_tgl = date('d', strtotime($ptglpilih));
        
        echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih' checked> $p_tgl &nbsp; &nbsp; ";
    }
}elseif ($pmodule=="viewdatakomentar") {
    
    include "../../config/koneksimysqli.php";
    $pidinput=$_POST['unoid'];
    $psts=$_POST['usts'];
    
    $query = "select a.*, b.nama as namakoentar, b.jabatanId as jabatanid, c.nama as namajabatan "
            . " from hrd.dkd_new_real1_komen as a "
            . " JOIN hrd.karyawan as b on a.komen_user=b.karyawanId "
            . " LEFT JOIN hrd.jabatan as c on b.jabatanId=c.jabatanId "
            . " WHERE a.nourut='$pidinput' AND a.`sts`='$psts' order by komen_date DESC";
    $tampil1=mysqli_query($cnmy, $query);
    while ($row1= mysqli_fetch_array($tampil1)) {
        $pjbkomen=$row1['jabatanid'];
        $pikomen=$row1['komentar'];
        $pikoentgl=$row1['komen_date'];
        $pikoenuser=$row1['komen_user'];
        $pikoenusernm=$row1['namakoentar'];
        $pikoenuserjbt=$row1['namajabatan'];

        echo "<li>";
            echo "<div class='block'>";
                echo "<div class='block_content'>";

                    echo "<h2 class='title' style='font-size:11px; font-weight:bold;'>";
                        echo "$pikoenusernm <span class='byline'>$pikoentgl</span> ";
                    echo "</h2>";
                    /*
                    echo "<div class='byline'>";
                        echo "<span>$pikoentgl</span> ";
                    echo "</div>";
                    */
                    echo "<p class=excerpt'>";
                        echo "$pikomen";
                    echo "</p>";

                echo "</div>";
            echo "</div>";

        echo "</li>";
    }
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdatakaryawancabjbt") {
    include "../../config/koneksimysqli.php";
    $pidcab=$_POST['uidcab'];
    $pidjbt=$_POST['uidjbt'];

    $pidkaryawan=$_SESSION['IDCARD'];
    $pidjabatan=$_SESSION['JABATANID'];
    $pidgroup=$_SESSION['GROUP'];
    $query_kry="";

    if (empty($pidcab)) {
        include "../../config/fungsi_sql.php";

        $pfilterkaryawan="";
        $pfilterkaryawan2="";
        $pfilterkry="";
        if ($pidjabatan=="38" OR $pidjabatan=="33" OR $pidjabatan=="05" OR $pidjabatan=="20" OR $pidjabatan=="08" OR $pidjabatan=="10" OR $pidjabatan=="18" OR $pidjabatan=="15") {

            $pnregion="";
            if ($pidkaryawan=="0000000159") $pnregion="T";
            elseif ($pidkaryawan=="0000000158") $pnregion="B";
            $pfilterkry=CariDataKaryawanByCabJbt2($pidkaryawan, $pidjabatan, $pnregion);

            if (!empty($pfilterkry)) {
                $parry_kry= explode(" | ", $pfilterkry);
                if (isset($parry_kry[0])) $pfilterkaryawan=TRIM($parry_kry[0]);
                if (isset($parry_kry[1])) $pfilterkaryawan2=TRIM($parry_kry[1]);
            }

        }


        if ($pidgroup=="1" OR $pidgroup=="24") {
            $query_kry = "select karyawanId as karyawanid, nama as nama 
                FROM hrd.karyawan WHERE 1=1 ";
            $query_kry .= " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
            $query_kry .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                    . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                    . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                    . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                    . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
            
        }else{
            $query_kry = "select karyawanId as karyawanid, nama as nama 
                FROM hrd.karyawan WHERE karyawanId IN $pfilterkaryawan ";
        }
        
        if (!empty($pidjbt)) {
            if ($pidjbt=="10")
                $query_kry .=" And jabatanId IN ('10', '18')";
            else
                $query_kry .=" And jabatanId='$pidjbt'";
        }

        $query_kry .=" ORDER BY nama";
        //echo "<option value='' selected>$pidjabatan | $pfilterkaryawan</option>";
    }else{

        if ($pidjabatan=="15") {
            $query_kry = "select karyawanId as karyawanid, nama as nama from hrd.karyawan WHERE karyawanid='$pidkaryawan'";
        }elseif ($pidjabatan=="10" OR $pidjabatan=="18") {
            if ($pidjbt=="10") {
                $query_kry = "select karyawanId as karyawanid, nama as nama from hrd.karyawan WHERE karyawanid='$pidkaryawan'";
            }elseif ($pidjbt=="15") {
                $query_kry = "select distinct a.karyawanid as karyawanid, c.nama as nama 
                    from mkt.imr0 as a JOIN mkt.ispv0 as b on a.icabangid=b.icabangid 
                    AND a.areaid=b.areaid and a.divisiid=b.divisiid 
                    JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
                    WHERE b.karyawanid='$pidkaryawan' AND a.icabangid='$pidcab'";
                $query_kry .=" Order by c.nama";
            }else{
                $query_kry = "select distinct a.karyawanid FROM mkt.imr0 as a 
                    LEFT JOIN mkt.ispv0 as b on a.icabangid=b.icabangid 
                    AND a.areaid=b.areaid and a.divisiid=b.divisiid 
                    WHERE b.karyawanid='$pidkaryawan' AND a.icabangid='$pidcab'
                    UNION
                    select distinct karyawanid FROm mkt.ispv0 WHERE 
                    karyawanid='$pidkaryawan' AND icabangid='$pidcab'";
                $query_kry = "select a.karyawanid as karyawanid, b.nama as nama 
                    from ($query_kry) as a
                    JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId";
                $query_kry .=" Order by b.nama";
            }
            
        }elseif ($pidjabatan=="08") {
            if ($pidjbt=="08") {
                $query_kry = "select karyawanId as karyawanid, nama as nama from hrd.karyawan WHERE karyawanid='$pidkaryawan'";
            }elseif ($pidjbt=="15") {
                $query_kry = "select distinct a.karyawanid as karyawanid, c.nama as nama 
                    from mkt.imr0 as a JOIN mkt.idm0 as b on a.icabangid=b.icabangid 
                    JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
                    WHERE b.karyawanid='$pidkaryawan' AND a.icabangid='$pidcab'";
                $query_kry .=" Order by c.nama";
            }elseif ($pidjbt=="10") {
                $query_kry = "select distinct a.karyawanid as karyawanid, c.nama as nama 
                    from mkt.ispv0 as a JOIN mkt.idm0 as b on a.icabangid=b.icabangid 
                    JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
                    WHERE b.karyawanid='$pidkaryawan' AND a.icabangid='$pidcab'";
                $query_kry .=" Order by c.nama";
            }else{
                $query_kry = "select distinct a.karyawanid FROM mkt.imr0 as a 
                    LEFT JOIN mkt.idm0 as b on a.icabangid=b.icabangid 
                    WHERE b.karyawanid='$pidkaryawan' AND a.icabangid='$pidcab'
                    UNION
                    select distinct a.karyawanid FROM mkt.ispv0 as a 
                    LEFT JOIN mkt.idm0 as b on a.icabangid=b.icabangid 
                    WHERE b.karyawanid='$pidkaryawan' AND a.icabangid='$pidcab'
                    UNION
                    select distinct karyawanid FROm mkt.idm0 WHERE 
                    karyawanid='$pidkaryawan' AND icabangid='$pidcab'";
                $query_kry = "select a.karyawanid as karyawanid, b.nama as nama 
                    from ($query_kry) as a
                    JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId";
                $query_kry .=" Order by b.nama";
            }
        }elseif ($pidjabatan=="20") {
            if ($pidjbt=="20") {
                $query_kry = "select karyawanId as karyawanid, nama as nama from hrd.karyawan WHERE karyawanid='$pidkaryawan'";
            }elseif ($pidjbt=="15") {
                $query_kry = "select distinct a.karyawanid as karyawanid, c.nama as nama 
                    from mkt.imr0 as a JOIN mkt.ism0 as b on a.icabangid=b.icabangid 
                    JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
                    WHERE b.karyawanid='$pidkaryawan' AND a.icabangid='$pidcab'";
                $query_kry .=" Order by c.nama";
            }elseif ($pidjbt=="10") {
                $query_kry = "select distinct a.karyawanid as karyawanid, c.nama as nama 
                    from mkt.ispv0 as a JOIN mkt.ism0 as b on a.icabangid=b.icabangid 
                    JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
                    WHERE b.karyawanid='$pidkaryawan' AND a.icabangid='$pidcab'";
                $query_kry .=" Order by c.nama";
            }elseif ($pidjbt=="08") {
                $query_kry = "select distinct a.karyawanid as karyawanid, c.nama as nama 
                    from mkt.idm0 as a JOIN mkt.ism0 as b on a.icabangid=b.icabangid 
                    JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
                    WHERE b.karyawanid='$pidkaryawan' AND a.icabangid='$pidcab'";
                $query_kry .=" Order by c.nama";
            }else{
                $query_kry = "select distinct a.karyawanid FROM mkt.imr0 as a 
                    LEFT JOIN mkt.ism0 as b on a.icabangid=b.icabangid 
                    WHERE b.karyawanid='$pidkaryawan' AND a.icabangid='$pidcab'
                    UNION
                    select distinct a.karyawanid FROM mkt.ispv0 as a 
                    LEFT JOIN mkt.ism0 as b on a.icabangid=b.icabangid 
                    WHERE b.karyawanid='$pidkaryawan' AND a.icabangid='$pidcab'
                    UNION
                    select distinct karyawanid FROm mkt.ism0 WHERE 
                    karyawanid='$pidkaryawan' AND icabangid='$pidcab'";
                $query_kry = "select a.karyawanid as karyawanid, b.nama as nama 
                    from ($query_kry) as a
                    JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId";
                $query_kry .=" Order by b.nama";
            }
        }elseif ($pidjabatan=="05") {
            $pfregion="XXX";
            if ((INT)$pidkaryawan==158) $pfregion="B";
            elseif ((INT)$pidkaryawan==159) $pfregion="T";

            if ($pidjbt=="05") {
                $query_kry = "select karyawanId as karyawanid, nama as nama from hrd.karyawan WHERE karyawanid='$pidkaryawan'";
            }elseif ($pidjbt=="15") {
                $query_kry = "select distinct a.karyawanid as karyawanid, c.nama as nama 
                    from mkt.imr0 as a JOIN mkt.icabang as b on a.icabangid=b.icabangid 
                    JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
                    WHERE b.region='$pfregion' AND a.icabangid='$pidcab'";
                $query_kry .=" Order by c.nama";
            }elseif ($pidjbt=="10") {
                $query_kry = "select distinct a.karyawanid as karyawanid, c.nama as nama 
                    from mkt.ispv0 as a JOIN mkt.icabang as b on a.icabangid=b.icabangid 
                    JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
                    WHERE b.region='$pfregion' AND a.icabangid='$pidcab'";
                $query_kry .=" Order by c.nama";
            }elseif ($pidjbt=="08") {
                $query_kry = "select distinct a.karyawanid as karyawanid, c.nama as nama 
                    from mkt.idm0 as a JOIN mkt.icabang as b on a.icabangid=b.icabangid 
                    JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
                    WHERE b.region='$pfregion' AND a.icabangid='$pidcab'";
                $query_kry .=" Order by c.nama";
            }elseif ($pidjbt=="20") {
                $query_kry = "select distinct a.karyawanid as karyawanid, c.nama as nama 
                    from mkt.ism0 as a JOIN mkt.icabang as b on a.icabangid=b.icabangid 
                    JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
                    WHERE b.region='$pfregion' AND a.icabangid='$pidcab'";
                $query_kry .=" Order by c.nama";
            }else{
                $query_kry = "select distinct a.karyawanid FROM mkt.imr0 as a 
                    LEFT JOIN mkt.icabang as b on a.icabangid=b.icabangid 
                    WHERE b.region='$pfregion' AND a.icabangid='$pidcab'
                    UNION
                    select distinct a.karyawanid FROM mkt.ispv0 as a 
                    LEFT JOIN mkt.icabang as b on a.icabangid=b.icabangid 
                    WHERE b.region='$pfregion' AND a.icabangid='$pidcab'
                    UNION
                    select distinct a.karyawanid FROM mkt.idm0 as a 
                    LEFT JOIN mkt.icabang as b on a.icabangid=b.icabangid 
                    WHERE b.region='$pfregion' AND a.icabangid='$pidcab'
                    UNION
                    select distinct a.karyawanid FROm mkt.ism0 as a 
                    LEFT JOIN mkt.icabang as b on a.icabangid=b.icabangid WHERE 
                    b.region='$pfregion' AND a.icabangid='$pidcab'";
                $query_kry = "select a.karyawanid as karyawanid, b.nama as nama 
                    from ($query_kry) as a
                    JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId";
                $query_kry .=" Order by b.nama";
            }
        }else{
            if ($pidjbt=="05") {
                $query_kry = "select karyawanId as karyawanid, nama as nama from hrd.karyawan WHERE karyawanid='$pidkaryawan'";
            }elseif ($pidjbt=="15") {
                $query_kry = "select distinct a.karyawanid as karyawanid, c.nama as nama 
                    from mkt.imr0 as a JOIN mkt.icabang as b on a.icabangid=b.icabangid 
                    JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
                    WHERE a.icabangid='$pidcab'";
                $query_kry .=" Order by c.nama";
            }elseif ($pidjbt=="10") {
                $query_kry = "select distinct a.karyawanid as karyawanid, c.nama as nama 
                    from mkt.ispv0 as a JOIN mkt.icabang as b on a.icabangid=b.icabangid 
                    JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
                    WHERE a.icabangid='$pidcab'";
                $query_kry .=" Order by c.nama";
            }elseif ($pidjbt=="08") {
                $query_kry = "select distinct a.karyawanid as karyawanid, c.nama as nama 
                    from mkt.idm0 as a JOIN mkt.icabang as b on a.icabangid=b.icabangid 
                    JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
                    WHERE a.icabangid='$pidcab'";
                $query_kry .=" Order by c.nama";
            }elseif ($pidjbt=="20") {
                $query_kry = "select distinct a.karyawanid as karyawanid, c.nama as nama 
                    from mkt.ism0 as a JOIN mkt.icabang as b on a.icabangid=b.icabangid 
                    JOIN hrd.karyawan as c on a.karyawanid=c.karyawanId 
                    WHERE a.icabangid='$pidcab'";
                $query_kry .=" Order by c.nama";
            }else{
                $query_kry = "select distinct a.karyawanid FROM mkt.imr0 as a 
                    LEFT JOIN mkt.icabang as b on a.icabangid=b.icabangid 
                    WHERE a.icabangid='$pidcab'
                    UNION
                    select distinct a.karyawanid FROM mkt.ispv0 as a 
                    LEFT JOIN mkt.icabang as b on a.icabangid=b.icabangid 
                    WHERE a.icabangid='$pidcab'
                    UNION
                    select distinct a.karyawanid FROM mkt.idm0 as a 
                    LEFT JOIN mkt.icabang as b on a.icabangid=b.icabangid 
                    WHERE a.icabangid='$pidcab'
                    UNION
                    select distinct karyawanid FROm mkt.ism0 WHERE 
                    icabangid='$pidcab'";
                $query_kry = "select a.karyawanid as karyawanid, b.nama as nama 
                    from ($query_kry) as a
                    JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId";
                $query_kry .=" Order by b.nama";
            }
        }

    }

    if (!empty($query_kry)) {
    $tampilket= mysqli_query($cnmy, $query_kry);
    $ketemu=mysqli_num_rows($tampilket);
    if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";
    
    
        while ($du= mysqli_fetch_array($tampilket)) {
            $pkaryid=$du['karyawanid'];
            $pkarynm=$du['nama'];
            $pkryid=(INT)$pkaryid;
            
            if ($pkaryid==$pidkaryawan)
                echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
            else
                echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";

        }

    }else{
        echo "<option value='' selected>-- Pilih --</option>";
    }

    mysqli_close($cnmy);
}elseif ($pmodule=="viewdataspesial") {
    include "../../config/koneksimysqli_ms.php";
    $pprofesi=$_POST['uprofesi'];
    $pidspesial="";
    echo "<option value='' selected></option>";
    if ($pprofesi == "Dokter" OR $pprofesi == "Profesor") {

        //$query = "select `id`, `spesialis` from dr.`spesialis_dokter` WHERE IFNULL(aktif,'')<>'N' Order By spesialis";
        $query = "select `id`, `nama` as spesialis from ms2.`lookup` WHERE IFNULL(`type`,'')='spesialis' Order By nama";
        $tampil=mysqli_query($cnms, $query);
        while ($row=mysqli_fetch_array($tampil)) {
            $pnidsp=$row['id'];
            $pnnmsp=$row['spesialis'];
            if ($pnnmsp==$pidspesial)
                echo "<option value='$pnnmsp' selected>$pnnmsp</option>";
            else
                echo "<option value='$pnnmsp' >$pnnmsp</option>";
        }
    }
    
    mysqli_close($cnms);
}elseif ($pmodule=="viewfotocamera") {
    
    ?>
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
        <div class='col-md-12 col-sm-12 col-xs-12'>

            <div class="column is-four-fifths">

                <center><video autoplay id="video" class='i_video' style='height:500px;'></video></center>

                <!--
                <button class="button is-hidden" id="btnPlay">
                        <span class="icon is-small"><i class="fas fa-play"></i></span>
                </button>

                <button class="button" id="btnPause">
                        <span class="icon is-small"><i class="fas fa-pause"></i></span>
                </button>
                -->
                <br/>
                <span hidden>
                <a class='btn btn-dark btn-xs' id='btnPlay'>Play</a>
                <a class='btn btn-warning btn-xs' id='btnPause'>Pause</a>
                </span>
                <a class='btn btn-info' id='btnScreenshot'>screenshot</a> &nbsp;
                <a class='btn btn-success' id='btnChangeCamera'>Switch camera</a>

            </div>
            <br/>&nbsp;
            <div>*) klik tombol <b style="color:blue; font-size:18px;">screenshot</b> untuk mengambil foto sebelum klik button save</div>

        </div>
    </div>

    
    <span hidden><textarea id='txt_arss' name='txt_arss'></textarea></span>
    <div class="column"><div id="screenshots" style='width:100px;'></div></div>
    <canvas hidden class="is-hidden" id="canvas"></canvas>
    
    <!--
    <br/>&nbsp;
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
        <div class='col-md-9 col-sm-9 col-xs-12'>
            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "dailyinput", "txt_arss")'>Save</button>
        </div>
    </div>
    <br/>&nbsp;   
    -->
    
    <script src="module/dkd/script_cm.js"></script>
    <?PHP
    
}elseif ($pmodule=="viewfotocamera2") {
    
?>
    <!--<div>*) Sebelum klik tombol save, pastikan foto sudah di <b>screenshot</b></div>-->
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
        <div class='col-md-9 col-sm-9 col-xs-12'>
            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "dailyinput", "txt_arss")'>Save</button> &nbsp; &nbsp;
            <button type='button' class='btn btn-default' onclick='reset_foto()'>Reset Foto</button>
        </div>
    </div>
    
    <br/>&nbsp;
    
<?PHP
    
}elseif ($pmodule=="showimagespotottd") {
    

    $fttd=$_POST['uttd'];
    $fimage=$_POST['uimg'];
    if ($fttd=="Y") {
        $ffolderfile="images/user_ttd/";
    }else{
        $ffolderfile="images/user_foto/";
    }

    ?>


    <div class='modal-dialog modal-lg'>
        <!-- Modal content-->
        <div class='modal-content'>

            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
                <h4 class='modal-title'>TTD / Foto</h4>
            </div>
            <br/>
            <div class="">

                <div class="row">

                    <div class="col-md-12 col-sm-12 col-xs-12">

                        <div class="x_panel">

                            <div class="x_content">
                                <div class="dashboard-widget-content">
                                    <form id="form">

                                        <div class='form-group'>
                                            <center>
                                            <?PHP
                                            //echo "$ffolderfile/$fimage";
                                            echo "<img src='$ffolderfile/$fimage' width='310px' height='390px' />";
                                            ?>
                                            </center>
                                        </div>

                                    </form>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
    <?PHP
    
}elseif ($pmodule=="xxxx") {
}
?>