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


    $_SESSION['FCUTICAB']=$pcabid;
    $_SESSION['FCUTIJBT']=$pjbtidpl;
    $_SESSION['FCUTIKRY']=$pkryid;
    $_SESSION['FCUTITGL01']=$ptgl1;
    $_SESSION['FCUTITGL02']=$ptgl2;

    $bukanuser=false;
    if ($pkaryawanid==$pkryid) $bukanuser=true;

    $ptgl1 = date('Ym', strtotime($ptgl1));
    $ptgl2 = date('Ym', strtotime($ptgl2));

    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_ubahget_id.php";

    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpfrmcuti01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpfrmcuti02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpfrmcuti03_".$puserid."_$now ";
    
    
    $sql = "select a.idcuti, a.tglinput, a.karyawanid, d.nama as nama_karyawan, a.jabatanid, a.id_jenis, c.nama_jenis, "
            . " a.keperluan, a.bulan1, a.bulan2, "
            . " a.atasan1, a.tgl_atasan1, a.atasan2, a.tgl_atasan2, a.atasan3, a.tgl_atasan3, "
            . " a.atasan4, a.tgl_atasan4, a.atasan5, a.tgl_atasan5, a.stsnonaktif, "
            . " b.tanggal FROM hrd.t_cuti0 as a LEFT JOIN hrd.t_cuti1 as b "
            . " on a.idcuti=b.idcuti "
            . " LEFT JOIN hrd.jenis_cuti as c on a.id_jenis=c.id_jenis "
            . " JOIN hrd.karyawan as d on a.karyawanid=d.karyawanid "
            . " WHERE a.karyawanid='$pkryid'";
    $sql .=" AND ( (DATE_FORMAT(b.tanggal,'%Y%m') BETWEEN '$ptgl1' AND '$ptgl2') "
            . " OR ( (DATE_FORMAT(a.bulan1,'%Y%m') BETWEEN '$ptgl1' AND '$ptgl2') OR (DATE_FORMAT(a.bulan2,'%Y%m') BETWEEN '$ptgl1' AND '$ptgl2') ) "
            . " OR (DATE_FORMAT(a.tglinput,'%Y%m') BETWEEN '$ptgl1' AND '$ptgl2') "
            . " )";
    $query = "create TEMPORARY table $tmp01 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>


<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' 
    id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>

    <div class='x_content'>
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='50px'>No</th>
                    <th width='100px'>Jenis</th>
                    <th width='50px'>Keperluan</th>
                    <th width='50px'>Periode</th>
                    <th width='50px'>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
            $no=1;
            $query = "select distinct idcuti, karyawanid, nama_karyawan, jabatanid, id_jenis, nama_jenis, keperluan, bulan1, bulan2, "
                    . " atasan1, tgl_atasan1, atasan2, tgl_atasan2, atasan3, tgl_atasan3,"
                    . " atasan4, tgl_atasan4, atasan5, tgl_atasan5, stsnonaktif from "
                    . " $tmp01 order by nama_karyawan, idcuti, nama_jenis";
            $tampil0=mysqli_query($cnmy, $query);
            while ($row0=mysqli_fetch_array($tampil0)) {
                $cidinput=$row0['idcuti'];
                $cidkry=$row0['karyawanid'];
                $nnmkry=$row0['nama_karyawan'];
                $nidjenis=$row0['id_jenis'];
                $nnmjenis=$row0['nama_jenis'];
                $nkeperluan=$row0['keperluan'];
                $nbln1=$row0['bulan1'];
                $nbln2=$row0['bulan2'];
                $nnonaktif=$row0['stsnonaktif'];
                
                $njbt=$row0['jabatanid'];
                $nats1=$row0['atasan1'];
                $ntglats1=$row0['tgl_atasan1'];
                $nats2=$row0['atasan2'];
                $ntglats2=$row0['tgl_atasan2'];
                $nats3=$row0['atasan3'];
                $ntglats3=$row0['tgl_atasan3'];
                $nats4=$row0['atasan4'];
                $ntglats4=$row0['tgl_atasan4'];
                $nats5=$row0['atasan5'];
                $ntglats5=$row0['tgl_atasan5'];
                
                if ($ntglats1=="0000-00-00 00:00:00") $ntglats1="";
                if ($ntglats2=="0000-00-00 00:00:00") $ntglats2="";
                if ($ntglats3=="0000-00-00 00:00:00") $ntglats3="";
                if ($ntglats4=="0000-00-00 00:00:00") $ntglats4="";
                if ($ntglats5=="0000-00-00 00:00:00") $ntglats5="";
                
                $nsudahapprove=false;
                
                if ( ($njbt=="15" OR $njbt=="38") AND !empty($nats1) AND !empty($ntglats1)) $nsudahapprove=true;
                if ( ($njbt=="15" OR $njbt=="38" OR $njbt=="10" OR $njbt=="18") AND !empty($nats2) AND !empty($ntglats2)) $nsudahapprove=true;
                if ( ($njbt=="15" OR $njbt=="38" OR $njbt=="10" OR $njbt=="18" OR $njbt=="08") AND !empty($nats3) AND !empty($ntglats3)) $nsudahapprove=true;
                if ( ($njbt=="15" OR $njbt=="38" OR $njbt=="10" OR $njbt=="18" OR $njbt=="08" OR $njbt=="20") AND !empty($nats4) AND !empty($ntglats4)) $nsudahapprove=true;
                if ( ($njbt=="15" OR $njbt=="38" OR $njbt=="10" OR $njbt=="18" OR $njbt=="08" OR $njbt=="05") AND !empty($nats5) AND !empty($ntglats5)) $nsudahapprove=true;
               
                
                
                
                $pidget=encodeString($cidinput);
                
                if ($nidjenis=="02") {
                    $nbln1 = date('d F Y', strtotime($nbln1));
                    $nbln2 = date('d F Y', strtotime($nbln2));
                }else{
                    $nbln1 = date('F Y', strtotime($nbln1));
                    $nbln2 = date('F Y', strtotime($nbln2));
                }


                $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidget'>Edit</a>";
                $print="<a title='detail' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                    . "onClick=\"window.open('eksekusi3.php?module=$pmodule&brid=$cidinput&iprint=detail',"
                    . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                    . "View</a>";
                
                $plewattgl=false; $ctglpl=""; $ctglpl1=""; $ctglpl2="";
                $query = "select distinct tanggal from $tmp01 WHERE idcuti='$cidinput' order by tanggal";
                $tampil1=mysqli_query($cnmy, $query);
                $ketemu1=mysqli_num_rows($tampil1);
                if ((INT)$ketemu1>0) {
                    $pawal=false;
                    while ($row1=mysqli_fetch_array($tampil1)) {
                        $tgl_p=$row1['tanggal'];
                        if (!empty($tgl_p)) {
                            $tgl_p = date('d F Y', strtotime($tgl_p));

                            $ctglpl .=$tgl_p.", ";

                            if ($pawal==false) {
                                $ctglpl1=$tgl_p;
                                $pawal=true;
                            }
                            $ctglpl2=$tgl_p;

                            $plewattgl=true;
                        }
                    }
                }
                
                if (!empty($ctglpl)) $ctglpl=substr($ctglpl, 0, -2);
                
                $ntglpilih="";
                if ($nidjenis=="02") {
                    if ($plewattgl==true)
                        $ntglpilih=$nbln1." s/d. ".$nbln2." (".$ctglpl1." - ".$ctglpl2.")";
                    else
                        $ntglpilih=$nbln1." s/d. ".$nbln2;
                }else{
                    $ntglpilih=$ctglpl;
                }
                
                
                if ($bukanuser==false) {
                    $pedit="";
                }
                
                if ($nsudahapprove==true) {
                    $pedit="";
                }
                
                if ($nnonaktif=="Y") {
                    $pedit="reject";
                    $print="";
                }
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$nnmjenis</td>";
                echo "<td >$nkeperluan</td>";
                echo "<td >$ntglpilih</td>";
                echo "<td nowrap>$pedit &nbsp; &nbsp; $print</td>";
                echo "</tr>";

                $no++;

            }

            ?>
            </tbody>
        </table>
    </div>

</form>

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
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp03");
    mysqli_close($cnmy);
?>