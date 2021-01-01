<?PHP
    ini_set("memory_limit","500M");
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
    $pidmenu=437;//$_GET['idmenu'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupidcard=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    
    $ptahunpilih=$_POST['utahun'];
    $pdivpilih=$_POST['udivpl'];
    $pkryawanid=$_POST['ukryid'];
    $pdeptid=$_POST['udptid'];
    $pcabid=$_POST['ucabid'];
    
    $_SESSION['BGTUPDTHN']=$ptahunpilih;
    $_SESSION['BGTUPDDVL']=$pdivpilih;
    $_SESSION['BGTUPDKRY']=$pkryawanid;
    $_SESSION['BGTUPDDPT']=$pdeptid;
    $_SESSION['BGTUPDCAB']=$pcabid;
    
    include "../../config/koneksimysqli.php";
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPUPSBGTDIVI01_".$puserid."_$now ";
    $tmp02 =" dbtemp.TMPUPSBGTDIVI02_".$puserid."_$now ";
    
    
    
    $query  = "SELECT a.bulan, a.div_pilih, a.departemen, a.karyawanid, a.icabangid, a.icabangid_o, a.kodeid, a.nm_id,  "
            . " a.coa4, b.NAMA4 as nama_coa4, a.jumlah "
            . " FROM dbmaster.t_budget_divisi as a "
            . " LEFT JOIN dbmaster.coa_level4 as b on a.coa4=b.COA4 "
            . " WHERE YEAR(a.bulan)='$ptahunpilih' AND a.div_pilih='$pdivpilih' AND a.karyawanid='$pkryawanid' AND "
            . " IFNULL(a.departemen,'')='$pdeptid' ";
    if (!empty($pcabid)) {
        if ($pdivpilih=="OTC" OR $pdivpilih=="OT" OR $pdivpilih=="CHC") {
            $query .=" AND icabangid_o='$pcabid'";
        }else{
            $query .=" AND icabangid='$pcabid'";
        }
    }
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>
<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<div class='col-md-12 col-sm-12 col-xs-12'>
    <div class='x_panel'>
        <form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' 
              id='demo_data2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
            <div class='x_content'>

                <!--<table id='dtablepiluptgt' class='table table-striped table-bordered' width='100%'>-->
                <table id='dtablepiluptgt' class='table table-striped table-bordered' width="100%" border="1px solid black">
                    <thead>
                        <tr>
                            <th width='10px'>No</th>
                            <th align="center" nowrap>COA</th>
                            <th align="center" nowrap>Nama Perkiranan</th>
                            <th align="center" nowrap>Bulan</th>
                            <th align="center" nowrap>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?PHP
                        $no=1;
                        $ptotal=0;
                        $ptotalcoa=0;
                        $query = "select DISTINCT coa4, nama_coa4 from $tmp01 order by coa4, nama_coa4";
                        $tampil1= mysqli_query($cnmy, $query);
                        $ketemu1= mysqli_num_rows($tampil1);
                        if ($ketemu1>0) {
                            while ($row1= mysqli_fetch_array($tampil1)) {
                                $ncoa4=$row1['coa4'];
                                $nnamacoa=$row1['nama_coa4'];

                                $ptotalcoa=0;
                                $query = "select * from $tmp01 WHERE coa4='$ncoa4' order by coa4, nama_coa4, bulan";
                                $tampil= mysqli_query($cnmy, $query);
                                $ketemu= mysqli_num_rows($tampil);
                                while ($row= mysqli_fetch_array($tampil)) {
                                    $nbulan=$row['bulan'];
                                    $njml=$row['jumlah'];

                                    $nbulan = date("F Y", strtotime($nbulan));

                                    $ptotalcoa=(double)$ptotalcoa+(double)$njml;
                                    $ptotal=(double)$ptotal+(double)$njml;
                                    $njml=number_format($njml,0,",",",");


                                    echo "<tr>";
                                    echo "<td nowrap>$no</td>";
                                    echo "<td nowrap>$ncoa4</td>";
                                    echo "<td nowrap>$nnamacoa</td>";
                                    echo "<td nowrap>$nbulan</td>";
                                    echo "<td nowrap align='right'>$njml</td>";
                                    echo "</tr>";

                                    $no++;
                                }

                                $ptotalcoa=number_format($ptotalcoa,0,",",",");

                                echo "<tr style='font-weight:bold;'>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap>TOTAL $ncoa4 - $nnamacoa : </td>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap align='right'>$ptotalcoa</td>";
                                echo "</tr>";

                            }

                            $ptotal=number_format($ptotal,0,",",",");

                            echo "<tr style='font-weight:bold;'>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap>GRAND TOTAL : </td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap align='right'>$ptotal</td>";
                            echo "</tr>";

                        }

                        ?>
                    </tbody>

                </table>


                <script>

                    $(document).ready(function() {
                        var dataTable = $('#dtablepiluptgt').DataTable( {
                            "bPaginate": false,
                            "bLengthChange": false,
                            //"bFilter": true,
                            "bInfo": false,
                            "ordering": false,
                            "order": [[ 0, "desc" ]],
                            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                            "displayLength": -1,
                            "columnDefs": [
                                { "visible": false },
                                { "orderable": false, "targets": 0 },
                                { "orderable": false, "targets": 1 },
                                { className: "text-right", "targets": [4] },//right
                                { className: "text-nowrap", "targets": [0, 1,2,3,4] }//nowrap

                            ],
                            "language": {
                                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
                            },
                            //"scrollY": 460,
                            "scrollX": true
                        } );
                        $('div.dataTables_filter input', dataTable.table().container()).focus();
                    } );

                </script>


                <style>
                    .divnone {
                        display: none;
                    }
                    #dtablepiluptgt th {
                        font-size: 13px;
                    }
                    #dtablepiluptgt td { 
                        font-size: 11px;
                    }
                    .imgzoom:hover {
                        -ms-transform: scale(3.5); /* IE 9 */
                        -webkit-transform: scale(3.5); /* Safari 3-8 */
                        transform: scale(3.5);

                    }
                </style>

            </div>
        </form>
    </div>
</div>
<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    
    mysqli_close($cnmy);
?>


<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }
</style>


<script>
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("myBtn").style.display = "block";
      } else {
        document.getElementById("myBtn").style.display = "none";
      }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    }
</script>