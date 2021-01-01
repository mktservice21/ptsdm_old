<?PHP
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $aksi="eksekusi3.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    
    $pdivisipm="";
    include "config/koneksimysqli_ms.php";
    $query = "SELECT DISTINCT divprodid FROM ms.penempatan_pm where karyawanid='$fkaryawan'";
    $tampil= mysqli_query($cnms, $query);
    $nrow= mysqli_fetch_array($tampil);
    $pdivisipm=$nrow['divprodid'];
    
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Laporan Budget DCC/DSS By Region</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                            <?PHP
                            if ($_SESSION['MOBILE']!="Y") {
                                echo "<button type='button' class='btn btn-danger' onclick=\"disp_confirm('excel')\">Excel</button>";
                            }
                            ?>
                            <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>

                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                
                                
                                <div class='col-sm-6'>
                                    <b>Periode</b>
                                    <div class="form-group">
                                        <div class='input-group date' id='cbln01'>
                                            <input type='text' id='e_tgl1' name='e_tgl1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='col-sm-6'>
                                    <b>s/d.</b>
                                    <div class="form-group">
                                        <div class='input-group date' id='cbln02'>
                                            <input type='text' id='e_tgl2' name='e_tgl2' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Kode/Akun</b> <input type="checkbox" id="chkbtnkode" value="deselect" onClick="SelAllCheckBox('chkbtnkode', 'chkbox_kode[]')" checked/>
                                        <div class="form-group">
                                            <div id="kotak-multi2" class="jarak">
                                                <?PHP
                                                    $query = "select a.kodeid, a.nama, a.divprodid from hrd.br_kode a where 1=1 ";
                                                    if (!empty($pdivisipm)) {
                                                        $query .= " AND a.divprodid='$pdivisipm' ";
                                                    }
                                                    $query .= " and a.kodeid IN ('700-01-03', '700-02-03', '700-04-03', '700-01-04', '700-02-04', '700-04-04')";
                                                    $query .= " ORDER BY a.divprodid, a.nama";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pkodeid=$z['kodeid'];
                                                        $pnmkode=$z['nama'];
                                                        $pnmdivisi=$z['divprodid'];
                                                        echo "&nbsp; <input type=checkbox value='$pkodeid' name='chkbox_kode[]' checked> $pkodeid - $pnmkode ($pnmdivisi)<br/>";
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Report Type</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_by" name="cb_by" onchange="">
                                                <option value="1">Region And Periode</option>
                                                <option value="2" selected>Region And Divisi</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>           
                    </div>

                </div>
            </div>
        </form>

    </div>
    <!--end row-->
</div>

<script>


    $('#cbln01').on('change dp.change', function(e){
        
        var idate1=document.getElementById('e_tgl1').value;
        var idate2=document.getElementById('e_tgl2').value;
        var ndate1 = new Date(idate1+" 01");
        var ndate2 = new Date(idate2+" 01");
        
        
        var month = new Array();
        month[0] = "January";
        month[1] = "February";
        month[2] = "March";
        month[3] = "April";
        month[4] = "May";
        month[5] = "June";
        month[6] = "July";
        month[7] = "August";
        month[8] = "September";
        month[9] = "October";
        month[10] = "November";
        month[11] = "December";
        
        var nbln1 = ndate1.getMonth();
        var nbulan1 = month[ndate1.getMonth()];
        var ntahun1 = ndate1.getFullYear();
        
        var nbln2 = ndate2.getMonth();
        var nbulan2 = month[ndate2.getMonth()];
        var ntahun2 = ndate2.getFullYear();
        
        if (nbln1=="NaN" || nbln2=="NaN") return false;
        
        if (parseInt(nbln1)>parseInt(nbln2)) {
            document.getElementById('e_tgl2').value=document.getElementById('e_tgl1').value;
        }else{
        
            if (ntahun1==ntahun2){
            }else{
                document.getElementById('e_tgl2').value=nbulan2+" "+ntahun1;
            }
            
        }
        //alert(nbulan1+" "+ntahun1+" - "+nbulan2+" "+ntahun2);
    });
    
    $('#cbln02').on('change dp.change', function(e){
        var idate1=document.getElementById('e_tgl1').value;
        var idate2=document.getElementById('e_tgl2').value;
        var ndate1 = new Date(idate1+" 01");
        var ndate2 = new Date(idate2+" 01");
        
        
        var month = new Array();
        month[0] = "January";
        month[1] = "February";
        month[2] = "March";
        month[3] = "April";
        month[4] = "May";
        month[5] = "June";
        month[6] = "July";
        month[7] = "August";
        month[8] = "September";
        month[9] = "October";
        month[10] = "November";
        month[11] = "December";
        
        var nbln1 = ndate1.getMonth();
        var nbulan1 = month[ndate1.getMonth()];
        var ntahun1 = ndate1.getFullYear();
        
        var nbln2 = ndate2.getMonth();
        var nbulan2 = month[ndate2.getMonth()];
        var ntahun2 = ndate2.getFullYear();
        //alert(nbln1+" "+nbln2);
        if (nbln1=="NaN" || nbln2=="NaN") return false;
        
        if (parseInt(nbln1)>parseInt(nbln2)) {
            document.getElementById('e_tgl1').value=document.getElementById('e_tgl2').value;
        }else{
        
            if (ntahun1==ntahun2){
            }else{
                document.getElementById('e_tgl1').value=nbulan1+" "+ntahun2;
            }
            
        }
        
        
    });
    
    
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


    function SelAllCheckBox(nmbuton, data){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);

        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
    }
    
</script>