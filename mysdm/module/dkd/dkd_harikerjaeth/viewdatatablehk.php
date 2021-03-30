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

    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    $pkaryawanid=$_SESSION['IDCARD'];

    include "../../../config/koneksimysqli.php";

    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmphketh01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmphketh02_".$puserid."_$now ";

    $ptahun=$_POST['utahun'];

    $bulan_array=array(1=> "Januari", "Februari", "Maret", "April", "Mei", 
        "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember");
?>

<script src="js/inputmask.js"></script>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' 
    id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>

    <div class='x_content'>
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='50px'>No</th>
                    <th width='100px'>Bulan</th>
                    <th width='100px'>Jumlah Hari</th>
                    <th width='100px'>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
            $no=1;
            for($ix=1;$ix<=12;$ix++) {
                $pnmbulan=$bulan_array[(INT)$ix];

                $pbln=$ix;
                if (strlen($ix)<=1) $pbln="0".$ix;

                $query ="SELECT jumlah FROM hrd.hrkrj WHERE left(periode1,7)='$ptahun-$pbln'";
                $tampil=mysqli_query($cnmy, $query);
                $nx=mysqli_fetch_array($tampil);
                $pjml=$nx['jumlah'];

                $ptext_jumlah="<input type='text' id='m_jumlah[$no]' name='m_jumlah[$no]' value='$pjml' class='inputmaskrp2'>";
                $psimpan="<input type='button' value='Save' class='btn btn-warning btn-xs' onClick=\"SimpanDataHK('$ptahun', '$pbln', 'm_jumlah[$no]')\">";

                if ((INT)$ptahun<2021) {
                    $psimpan="";
                }

                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$pnmbulan $ptahun</td>";
                echo "<td nowrap align='right'>$ptext_jumlah</td>";
                echo "<td nowrap>$psimpan</td>";
                echo "</tr>";

                $no++;
            }
            ?>
            </tbody>
        </table>
    </div>

</form>

    <script>
        function SimpanDataHK(cTahun, cBulan, cJml) {
            var ejml=document.getElementById(cJml).value;

            //alert(cTahun+", "+cBulan+", "+ejml);

            ok_ = 1;
            if (ok_) {
                var r = confirm('Apakah akan melakukan simpan ...?');
                if (r==true) {
                    $.ajax({
                        type:"post",
                        url:"module/dkd/dkd_harikerjaeth/aksi_harikerjaeth.php?module=simpandatahketh",
                        data:"utahun="+cTahun+"&ubulan="+cBulan+"&ujml="+ejml,
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