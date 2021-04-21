<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="viewdatatanggal") {
    $tgl_pertama=$_POST['utgl'];
    $ptglpilih = date('Y-m-01', strtotime($tgl_pertama));
    $p_tgl = date('d', strtotime($ptglpilih));
    $p_akh = date('t', strtotime($ptglpilih));
    
    
    echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih' > $p_tgl &nbsp; &nbsp; ";
    $nom=2;
    for ($ix=1;$ix<(INT)$p_akh;$ix++) {
        $ptglpilih = date('Y-m-d', strtotime('+1 days', strtotime($ptglpilih)));
        $p_tgl = date('d', strtotime($ptglpilih));
        echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih'> $p_tgl &nbsp; &nbsp; ";
        if ($nom>5) {echo "<br/>"; $nom=0;}
        $nom++;
    }
}elseif ($pmodule=="cekdatasudahada") {
    include "../../config/koneksimysqli.php";

    $pidinput=$_POST['uid'];
    $ppilihtgl=$_POST['utglpilih'];
    $pkaryawanid=$_POST['ukry'];
    $pjenis=$_POST['ujenis'];
    $pbln1=$_POST['ubln1'];
    $pbln2=$_POST['ubln2'];
    
    $pbln1= date("Ym", strtotime($pbln1));
    $pbln2= date("Ym", strtotime($pbln2));
    
    $itgl=explode(',',$ppilihtgl);
    $pilihantgl="";
    foreach($itgl as $ptgl)
    {
        $pilihantgl .="'".$ptgl."',";
    }
    if (!empty($pilihantgl)) $pilihantgl="(".substr($pilihantgl, 0, -1).")";
    else $pilihantgl="('')";

    
    $boleh="boleh";
    
    //echo "$boleh"; exit;
    
    if ($pjenis=="02") {
        if ($pbln1>$pbln2) {
            mysqli_close($cnmy); echo "Bulan tidak sesuai..."; exit;
        }
        
        $query = "select distinct b.tanggal from hrd.t_cuti0 as a JOIN hrd.t_cuti1 as b "
                . " on a.idcuti=b.idcuti WHERE a.idcuti<>'$pidinput' AND "
                . " (b.tanggal in $pilihantgl OR (DATE_FORMAT(a.bulan1,'%Y%m') BETWEEN '$pbln1' AND '$pbln2') OR (DATE_FORMAT(a.bulan2,'%Y%m') BETWEEN '$pbln1' AND '$pbln2') ) "
                . " AND a.karyawanid='$pkaryawanid'";
    }else{
        $query = "select distinct b.tanggal from hrd.t_cuti0 as a JOIN hrd.t_cuti1 as b "
                . " on a.idcuti=b.idcuti WHERE a.idcuti<>'$pidinput' AND b.tanggal in $pilihantgl AND a.karyawanid='$pkaryawanid'";
    }
    
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $boleh="Tanggal tersebut sudah ada..., silakan pilih tanggal yang lain";
    }

    mysqli_close($cnmy);

    echo $boleh; exit;
}elseif ($pmodule=="viewdatabulan2") {
    date_default_timezone_set('Asia/Jakarta');
    $ptgl1=$_POST['utgl'];
    
    $tgl_kedua = date('F Y', strtotime('+1 month', strtotime($ptgl1)));
    $ptglpilih = date('Y-m-d', strtotime($ptgl1));
    $ptglpilih02 = date('Y-m-d', strtotime($tgl_kedua));
    $ctglpilih="";
    ?>
        <div id="div_bulan2">
            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>s/d. Bulan <span class='required'></span></label>
                <div class='col-md-6'>
                    <div class='input-group date' id='cbln02'>
                        <input type="text" class="form-control" id='e_bulan02' name='e_bulan02' autocomplete='off' required='required' placeholder='F Y' value='<?PHP echo $tgl_kedua; ?>' Readonly>
                        <span class='input-group-addon'>
                            <span class='glyphicon glyphicon-calendar'></span>
                        </span>

                    </div>
                </div>
            </div>


            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal <span class='required'></span></label>
                <div class='col-xs-9'>
                    <div id="div_tgl2">
                        <?PHP
                            $p_tgl = date('d', strtotime($ptglpilih02));
                            $p_akh = date('t', strtotime($ptglpilih02));

                            $p_b01 = date('Ym', strtotime($ptglpilih));
                            $p_b02 = date('Ym', strtotime($ptglpilih02));

                            $pchkpilih="";
                            if (strpos($ctglpilih, $ptglpilih02)==true AND $p_b01<>$p_b02) $pchkpilih="checked";
                            echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih02' $pchkpilih> $p_tgl &nbsp; &nbsp; ";

                            $nom=2;
                            for ($ix=1;$ix<(INT)$p_akh;$ix++) {
                                $ptglpilih02 = date('Y-m-d', strtotime('+1 days', strtotime($ptglpilih02)));

                                $pchkpilih="";
                                if (strpos($ctglpilih, $ptglpilih02)==true AND $p_b01<>$p_b02) $pchkpilih="checked";

                                $p_tgl = date('d', strtotime($ptglpilih02));
                                echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih02' $pchkpilih> $p_tgl &nbsp; &nbsp; ";
                                if ($nom>5) {echo "<br/>"; $nom=0;}
                                $nom++;
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?PHP
}elseif ($pmodule=="tampilperiodepilih") {
    date_default_timezone_set('Asia/Jakarta');
    
    $pjenis=$_POST['ujenis'];
    $pidinput=$_POST['uid'];
    $ctglpilih="";
    $hari_ini = date("Y-m-d");
    if ($pjenis=="02") {
        $tgl_pertama = date('d F Y', strtotime($hari_ini));
        $tgl_kedua = date('t F Y', strtotime('+2 month', strtotime($hari_ini)));
    }else{
        $tgl_pertama = date('F Y', strtotime($hari_ini));
        $tgl_kedua = date('F Y', strtotime('+1 month', strtotime($hari_ini)));
    }
    
    if (!empty($pidinput) AND $pidinput<>"0") {
        include "../../config/koneksimysqli.php";
        $edit = mysqli_query($cnmy, "SELECT * FROM hrd.t_cuti0 WHERE idcuti='$pidinput'");
        $r    = mysqli_fetch_array($edit);
        
        $pbln1=$r['bulan1'];
        $pbln2=$r['bulan2'];
        
        if ($pjenis=="02") {
            $tgl_pertama = date('d F Y', strtotime($pbln1));
            $tgl_kedua = date('d F Y', strtotime($pbln2));
        }else {
            $tgl_pertama = date('F Y', strtotime($pbln1));
            $tgl_kedua = date('F Y', strtotime($pbln2));
        }
        
        $query = "select distinct tanggal from hrd.t_cuti1 WHERE idcuti='$pidinput' order by tanggal";
        $tampil1=mysqli_query($cnmy, $query);
        $ketemu1=mysqli_num_rows($tampil1);
        if ((INT)$ketemu1>0) {
            while ($row1=mysqli_fetch_array($tampil1)) {
                $tgl_p=$row1['tanggal'];
                if (!empty($tgl_p)) {
                    $tgl_p = date('Y-m-d', strtotime($tgl_p));

                    $ctglpilih .="'".$tgl_p."',";
                }
            }
        }
        
        mysqli_close($cnmy);
    }
    
    $ptglpilih = date('Y-m-d', strtotime($tgl_pertama));
    $ptglpilih02 = date('Y-m-d', strtotime($tgl_kedua));
    
    
    if ($pjenis=="02") {
        echo "<span hidden><input type='checkbox' name='chktgl[]' value='$ptglpilih'> $ptglpilih &nbsp; &nbsp; </span>";
    ?>

        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan <span class='required'></span></label>
            <div class='col-md-6'>
                <div class='input-group date' id='tgl01'>
                    <input type="text" class="form-control" id='e_bulan01' name='e_bulan01' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl_pertama; ?>' Readonly>
                    <span class='input-group-addon'>
                        <span class='glyphicon glyphicon-calendar'></span>
                    </span>

                </div>
            </div>
        </div>

        <div id="div_bulan2">
            
            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>s/d. <span class='required'></span></label>
                <div class='col-md-6'>
                    <div class='input-group date' id='tgl02'>
                        <input type="text" class="form-control" id='e_bulan02' name='e_bulan02' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl_kedua; ?>' Readonly>
                        <span class='input-group-addon'>
                            <span class='glyphicon glyphicon-calendar'></span>
                        </span>

                    </div>
                </div>
            </div>
            
        </div>
        
        <script>
            $('#tgl01, #tgl02').datetimepicker({
                ignoreReadonly: true,
                allowInputToggle: true,
                format: 'DD MMMM YYYY'
            });
        </script>
    <?PHP
    }else {
    ?>
        <script>
            $('#cbln01, #cbln02').datetimepicker({
                ignoreReadonly: true,
                allowInputToggle: true,
                format: 'MMMM YYYY'
            });
            
            $(document).ready(function() {
                $('#cbln01').on('change dp.change', function(e){
                    ShowTanggalPilih();
                    var ijenis = document.getElementById('cb_jeniscuti').value;
                    if (ijenis=="02") {
                    }else{
                        ShowBulan2();
                    }
                });

                $('#cbln02').on('change dp.change', function(e){
                    ShowTanggalPilih2();
                });
            });
            function ShowBulan2() {
                var etgl =document.getElementById('e_bulan01').value;

                $.ajax({
                    type:"post",
                    url:"module/marketing/viewdatamkt.php?module=viewdatabulan2",
                    data:"utgl="+etgl,
                    success:function(data){
                        $("#div_bulan2").html(data);
                    }
                });
            }
    
        </script>
        
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan <span class='required'></span></label>
            <div class='col-md-6'>
                <div class='input-group date' id='cbln01'>
                    <input type="text" class="form-control" id='e_bulan01' name='e_bulan01' autocomplete='off' required='required' placeholder='F Y' value='<?PHP echo $tgl_pertama; ?>' Readonly>
                    <span class='input-group-addon'>
                        <span class='glyphicon glyphicon-calendar'></span>
                    </span>

                </div>
            </div>
        </div>

        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal <span class='required'></span></label>
            <div class='col-xs-9'>
                <div id="div_tgl">
                    <?PHP
                        $p_tgl = date('d', strtotime($ptglpilih));
                        $p_akh = date('t', strtotime($ptglpilih));

                        $pchkpilih="";
                        if (strpos($ctglpilih, $ptglpilih)==true) $pchkpilih="checked";
                        echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih' $pchkpilih> $p_tgl &nbsp; &nbsp; ";

                        $nom=2;
                        for ($ix=1;$ix<(INT)$p_akh;$ix++) {
                            $ptglpilih = date('Y-m-d', strtotime('+1 days', strtotime($ptglpilih)));

                            $pchkpilih="";
                            if (strpos($ctglpilih, $ptglpilih)==true) $pchkpilih="checked";

                            $p_tgl = date('d', strtotime($ptglpilih));
                            echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih' $pchkpilih> $p_tgl &nbsp; &nbsp; ";
                            if ($nom>5) {echo "<br/>"; $nom=0;}
                            $nom++;
                        }
                    ?>
                </div>
            </div>
        </div>
        <hr/>
        
        <div id="div_bulan2">
            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>s/d. Bulan <span class='required'></span></label>
                <div class='col-md-6'>
                    <div class='input-group date' id='cbln02X'>
                        <input type="text" class="form-control" id='e_bulan02' name='e_bulan02' autocomplete='off' required='required' placeholder='F Y' value='<?PHP echo $tgl_kedua; ?>' Readonly>
                        <span class='input-group-addon'>
                            <span class='glyphicon glyphicon-calendar'></span>
                        </span>

                    </div>
                </div>
            </div>


            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal <span class='required'></span></label>
                <div class='col-xs-9'>
                    <div id="div_tgl2">
                        <?PHP
                            $p_tgl = date('d', strtotime($ptglpilih02));
                            $p_akh = date('t', strtotime($ptglpilih02));

                            $p_b01 = date('Ym', strtotime($ptglpilih));
                            $p_b02 = date('Ym', strtotime($ptglpilih02));

                            $pchkpilih="";
                            if (strpos($ctglpilih, $ptglpilih02)==true AND $p_b01<>$p_b02) $pchkpilih="checked";
                            echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih02' $pchkpilih> $p_tgl &nbsp; &nbsp; ";

                            $nom=2;
                            for ($ix=1;$ix<(INT)$p_akh;$ix++) {
                                $ptglpilih02 = date('Y-m-d', strtotime('+1 days', strtotime($ptglpilih02)));

                                $pchkpilih="";
                                if (strpos($ctglpilih, $ptglpilih02)==true AND $p_b01<>$p_b02) $pchkpilih="checked";

                                $p_tgl = date('d', strtotime($ptglpilih02));
                                echo "<input type='checkbox' name='chktgl[]' value='$ptglpilih02' $pchkpilih> $p_tgl &nbsp; &nbsp; ";
                                if ($nom>5) {echo "<br/>"; $nom=0;}
                                $nom++;
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>

    <?PHP
    }
    
}elseif ($pmodule=="cekdataprosclssudahada") {
    include "../../config/koneksimysqli.php";

    $ptahun=$_POST['utahun'];
    
    $query = "select karyawanid from hrd.karyawan_cuti_close WHERE tahun='$ptahun'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        echo "sudahada"; mysqli_close($cnmy); exit;
    }
    
    mysqli_close($cnmy);
}

?>

