<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }

    $pkaryawanid=$_SESSION['IDCARD'];

    $ptgl1=$_POST['utgl1'];
    $ptgl2=$_POST['utgl2'];
    $pkryid=$_POST['ukryid'];
    $pcabid=$_POST['ucabid'];
    $pjbtidpl=$_POST['ujbtid'];


    $_SESSION['WEKPLNRLCAB']=$pcabid;
    $_SESSION['WEKPLNRLJBT']=$pjbtidpl;
    $_SESSION['WEKPLNRLKRY']=$pkryid;
    $_SESSION['WEKPLNRLTGL']=$ptgl1;

    $bukanuser=false;
    if ($pkaryawanid==$pkryid) $bukanuser=true;

    $ptgl1 = date('Y-m-d', strtotime($ptgl1));
    //$ptgl2 = date('Y-m-d', strtotime($ptgl2));
    $ptgl2 = date('Y-m-d', strtotime('+4 days', strtotime($ptgl1)));

    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_ubahget_id.php";

    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpdkdrlpln01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpdkdrlpln02_".$puserid."_$now ";

    $sql = "select a.idinput, a.tanggal, a.karyawanid, a.ketid, c.nama as nama_ket,
        a.compl, a.aktivitas, b.jenis, b.dokterid, d.namalengkap as nama_dokter, b.notes, b.saran,
        a.real_user1 as realisasi, a.real_date1 as realisasidate, b.real_user, b.real_date   
        from hrd.dkd_new0 as a left join hrd.dkd_new1 as b on a.idinput=b.idinput 
        LEFT JOIN hrd.ket as c on a.ketid=c.ketId
        LEFT JOIN dr.masterdokter as d on b.dokterid=d.id WHERE a.karyawanid='$pkryid' ";
    $sql .=" AND a.tanggal between '$ptgl1' AND '$ptgl2'";

    $query = "create TEMPORARY table $tmp01 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    $query = "Alter table $tmp01 ADD totakv INT(4), ADD totvisit INT(4)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp01 SET totakv=1";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp01 as a JOIN (select idinput, count(distinct dokterid) as jml FROM 
        hrd.dkd_new1 GROUP BY 1) as b on a.idinput=b.idinput SET a.totvisit=b.jml";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    $bulan_array=array(1=> "Januari", "Februari", "Maret", "April", "Mei", 
        "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember");

    $hari_array = array(
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    );
    
?>


<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' 
    id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>

    <div class='x_content'>
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='50px'>No</th>
                    <th width='100px'>Tanggal / Jenis</th>
                    <th width='100px'>Keperluan / Dokter</th>
                    <th width='100px'>Compl. / Notes</th>
                    <th width='50px'>Aktivitas / Saran</th>
                    <th width='50px'>&nbsp;</th>
                    
                </tr>
            </thead>
            <tbody>
            <?PHP
            $no=1; $nnjmlrc=1;
            $query = "select distinct idinput, tanggal, totakv, totvisit, nama_ket, compl, aktivitas, realisasi, realisasidate from $tmp01 order by tanggal";
            $tampil0=mysqli_query($cnmy, $query);
            while ($row0=mysqli_fetch_array($tampil0)) {
                $cidinput=$row0['idinput'];
                $ntgl=$row0['tanggal'];
                $ntotakv=$row0['totakv'];
                $ntotvisit=$row0['totvisit'];
                $nnamaket=$row0['nama_ket'];
                $ncompl=$row0['compl'];
                $naktivitas=$row0['aktivitas'];
                $nrealisasi=$row0['realisasi'];
                $nrealdate=$row0['realisasidate'];

                $pidget=encryptForId($cidinput);

                if (empty($ntotakv)) $ntotakv=1;
                if (empty($ntotvisit)) $ntotvisit=0;

                $ntanggal = date('l d F Y', strtotime($ntgl));

                $xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
                $xtgl= date('d', strtotime($ntgl));
                $xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
                $xthn= date('Y', strtotime($ntgl));
                
                $ptext_idinput="<input type='hidden' id='m_idinput[$nnjmlrc]' name='m_idinput[$nnjmlrc]' value='$cidinput'>";
                $ptext_tgl="<input type='hidden' id='m_tgl[$nnjmlrc]' name='m_tgl[$nnjmlrc]' value='$ntgl'>";
                $ptext_dokter="<input type='hidden' id='m_dokterid[$nnjmlrc]' name='m_dokterid[$nnjmlrc]' value=''>";
                $ptext_saran="<input type='hidden' id='m_saran[$nnjmlrc]' name='m_saran[$nnjmlrc]' value=''>";

                $prealisasi="";
                if ((INT)$ntotvisit<=0) {
                    $prealisasi="<input type='button' value='Realisasi' class='btn btn-warning btn-xs' onClick=\"ProsesDataReal('0', 'm_idinput[$nnjmlrc]', 'm_tgl[$nnjmlrc]', 'm_dokterid[$nnjmlrc]', 'm_saran[$nnjmlrc]')\">";
                }

                $pextracall="<a class='btn btn-default btn-xs' href='?module=$pmodule&act=tambahbaru&idmenu=$pidmenu&nmun=$pidmenu&id=$pidget'>Extra Call</a>";

                $nnjmlrc++;

                if ($bukanuser==false) {
                    $prealisasi="";
                    $pextracall="";
                }

                //sudah realisasi
                if (!empty($nrealisasi)) {
                    $prealisasi="$nrealdate";
                    $pextracall="";
                }

                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap>$no $ptext_idinput $ptext_tgl $ptext_dokter $ptext_saran</td>";
                echo "<td nowrap>$xhari, $xtgl $xbulan $xthn</td>";
                echo "<td nowrap>$nnamaket</td>";
                echo "<td nowrap>$ncompl</td>";
                echo "<td nowrap>$naktivitas</td>";
                echo "<td nowrap>$pextracall &nbsp; &nbsp; $prealisasi</td>";
                echo "</tr>";

                if ((INT)$ntotvisit>0) {

                    $query = "select * from $tmp01 where idinput='$cidinput' order by tanggal, jenis, nama_dokter";
                    $tampil=mysqli_query($cnmy, $query);
                    while ($row=mysqli_fetch_array($tampil)) {
                        $njenis=$row['jenis'];
                        $ndoktid=$row['dokterid'];
                        $nnamadokt=$row['nama_dokter'];
                        $nnotes=$row['notes'];
                        $nsaran=$row['saran'];
                        $nrealisasi=$row['real_user'];
                        $nrealdate=$row['real_date'];


                        $ptext_idinput="<input type='hidden' id='m_idinput[$nnjmlrc]' name='m_idinput[$nnjmlrc]' value='$cidinput'>";
                        $ptext_tgl="<input type='hidden' id='m_tgl[$nnjmlrc]' name='m_tgl[$nnjmlrc]' value='$ntgl'>";
                        $ptext_dokter="<input type='hidden' id='m_dokterid[$nnjmlrc]' name='m_dokterid[$nnjmlrc]' value='$ndoktid'>";
                        $ptext_saran="<input type='text' id='m_saran[$nnjmlrc]' name='m_saran[$nnjmlrc]' value='$nsaran' size='50px'>";

                        $prealisasi="<input type='button' value='Realisasi' class='btn btn-warning btn-xs' onClick=\"ProsesDataReal('1', 'm_idinput[$nnjmlrc]', 'm_tgl[$nnjmlrc]', 'm_dokterid[$nnjmlrc]', 'm_saran[$nnjmlrc]')\">";

                        if ($bukanuser==false OR $njenis=="EC") {
                            $prealisasi="";
                        }

                        //sudah realisasi
                        if (!empty($nrealisasi)) {
                            $prealisasi="$nrealdate";
                        }

                        echo "<tr>";
                        echo "<td nowrap>$ptext_idinput $ptext_tgl $ptext_dokter</td>";
                        echo "<td nowrap>$njenis</td>";
                        echo "<td nowrap>$nnamadokt</td>";
                        echo "<td nowrap>$nnotes</td>";
                        echo "<td nowrap>$ptext_saran</td>";
                        echo "<td nowrap>$prealisasi</td>";
                        echo "</tr>";

                        $nnjmlrc++;

                    }

                }

                $no++;
                
            }

            ?>
            </tbody>
        </table>
    </div>

</form>

<script>

    function ProsesDataReal(sKey, sInput, sTgl, sDokt, sSaran) {
        var eidinput=document.getElementById(sInput).value;
        var etgl=document.getElementById(sTgl).value;
        var edoktid=document.getElementById(sDokt).value;
        var esaran=document.getElementById(sSaran).value;
        //alert(eidinput+", "+etgl+", "+edoktid+", "+esaran);

        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses Realisasi ...?');
            if (r==true) {
                $.ajax({
                    type:"post",
                    url:"module/dkd/dkd_realwekplan/simpanrealisasiwek.php?module=simpanrealisasiwekly",
                    data:"ukey="+sKey+"&uidinput="+eidinput+"&utgl="+etgl+"&udoktid="+edoktid+"&usaran="+esaran,
                    success:function(data){
                        alert(data);
                    }
                });
                
                return 1;
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

        th {
            background: white;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            z-index:1;
        }
		
    </style>



<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp02");
    mysqli_close($cnmy);
?>