<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('Y', strtotime($hari_ini));
    
    include("config/koneksimysqli_it.php");
    
    $aksi="eksekusi3.php";
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>History Cuti</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    <?PHP
                    $pKaryawanId=$_SESSION['IDCARD'];
                    $pTahun= date('Y');
                    $query = "select * from hrd.cuti where karyawanid='$pKaryawanId' and tahun='$pTahun'"; 
                    $result = mysqli_query($cnit, $query);
                    $num_results = mysqli_num_rows($result);
                    $row = mysqli_fetch_array($result);
                    $hak_ = $row['hak'];
                    if ($hak_ == '') {
                           $hak_ = 12;
                    }
                    echo "<h2>Hak Cuti : $hak_</h2>";
                    ?>
                    <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
                        <thead>
                            <tr>
                                <th width='50px' align="center">Hari</th>
                                <th width='100px' align="center" nowrap>Mulai</th>
                                <th width='100px' align="center" nowrap>s/d.</th>
                                <th align="center" nowrap>Alasan Cuti</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?PHP
                            $query ="select * from hrd.absen0 where (karyawanid='$pKaryawanId') and (left(tgl1,4)='2020') 
                                and (kdabs='02' or kdabs='07' or kdabs='04') order by tgl1";
                            $tampil= mysqli_query($cnit, $query);
                            while ($a= mysqli_fetch_array($tampil)) {
                                $ptgl1=$a['tgl1'];
                                $ptgl2=$a['tgl2'];
                                $palasan=$a['ket'];
                                
                                $tanggal1 = new DateTime($ptgl1);
                                $tanggal2 = new DateTime($ptgl2);
                                $pjmlhjt = $tanggal2->diff($tanggal1)->format("%a");
                                $pjmlhjt=(double)$pjmlhjt+1; 
                                
                                echo "<tr>";
                                echo "<td>$pjmlhjt</td>";
                                echo "<td>$ptgl1</td>";
                                echo "<td>$ptgl2</td>";
                                echo "<td>$palasan</td>";
                                echo "</tr>";
                                
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

    </div>
    <!--end row-->
</div>

<script>
    function disp_confirm(pText)  {
        if (pText == "excel") {
            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
            document.getElementById("demo-form2").submit();
            return 1;
        }else{
            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
            document.getElementById("demo-form2").submit();
            return 1;
        }
    }
</script>