<?PHP
    $jabatan_="";
    $fildiv="";
    $tampilbawahan = "N";
    $filkaryawncabang = "";
    $hanyasatukaryawan = "";
    $fildiv = "('OTC')";
    if (!empty($_SESSION['AKSES_JABATAN'])) {
        $jabatan_ = $_SESSION['AKSES_JABATAN'];
    }

    if (!empty($_SESSION['AKSES_CABANG'])) {
        $filkaryawncabang = $_SESSION['AKSES_CABANG'];
    }
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('01 F Y', strtotime($hari_ini));
    $tgl_terakhir = date('t F Y', strtotime($hari_ini));
    
    
    $aksi="eksekusi3.php";
    include "config/koneksimysqli_it.php";
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Pindah Cabang dan Area Customer</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                <u><b>LAMA</b></u>
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="e_cabang" name="e_cabang" onchange="TampilArea('e_cabang', 'e_area')">
                                            <?PHP
                                            $no=1;
                                            $pcabangpilih="";
                                            $query = "select iCabangId, nama from MKT.icabang ";
                                            $query .=" order by nama";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $picabang=$z['iCabangId'];
                                                $pnmcabang=$z['nama'];
                                                if ($no==1) $pcabangpilih=$picabang;
                                                
                                                if ($_SESSION['PIND_CABLAMA']==$picabang) {
                                                    $pcabangpilih=$picabang;
                                                    echo "<option value='$picabang' selected>$picabang - $pnmcabang</option>";
                                                }else{
                                                    echo "<option value='$picabang'>$picabang - $pnmcabang</option>";
                                                }
                                                
                                                $no++;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="e_area" name="e_area">
                                            <?PHP
                                            $query = "select areaId, Nama from MKT.iarea WHERE iCabangId='$pcabangpilih'";
                                            $query .=" order by nama";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $piarea=$z['areaId'];
                                                $pnmarea=$z['Nama'];
                                                if ($_SESSION['PIND_AREALAMA']==$piarea)
                                                    echo "<option value='$piarea' selected>$piarea - $pnmarea</option>";
                                                else
                                                    echo "<option value='$piarea'>$piarea - $pnmarea</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <button type='button' class='btn btn-success' onclick="TampilkanData('e_cabang', 'e_area')">Preview</button>
                                    </div>
                                </div>
                                
                                

                            </div>
                        </div>           
                    </div>
                    
                    <!--kanan-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                <u><b>BARU</b></u>
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="e_cabangbaru" name="e_cabangbaru" onchange="TampilArea('e_cabangbaru', 'e_areabaru')">
                                            <?PHP
                                            $no=1;
                                            $pcabangpilih="";
                                            $query = "select iCabangId, nama from MKT.icabang ";
                                            $query .=" order by nama";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $picabang=$z['iCabangId'];
                                                $pnmcabang=$z['nama'];
                                                if ($no==1) $pcabangpilih=$picabang;
                                                
                                                if ($_SESSION['PIND_CABBARU']==$picabang) {
                                                    $pcabangpilih=$picabang;
                                                    echo "<option value='$picabang' selected>$picabang - $pnmcabang</option>";
                                                }else{
                                                    echo "<option value='$picabang'>$picabang - $pnmcabang</option>";
                                                }
                                                
                                                $no++;
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="e_areabaru" name="e_areabaru">
                                            <?PHP
                                            $query = "select areaId, Nama from MKT.iarea WHERE iCabangId='$pcabangpilih'";
                                            $query .=" order by nama";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $piarea=$z['areaId'];
                                                $pnmarea=$z['Nama'];
                                                if ($_SESSION['PIND_AREABARU']==$piarea)
                                                    echo "<option value='$piarea' selected>$piarea - $pnmarea</option>";
                                                else
                                                    echo "<option value='$piarea'>$piarea - $pnmarea</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <button type='button' class='btn btn-success' onclick="TampilkanData2('e_cabangbaru', 'e_areabaru')">Preview</button>&nbsp; 
                                        <button type='button' class='btn btn-danger' onclick="ProsesDataPindah()">Submit</button>
                                    </div>
                                </div>
                                
                                

                            </div>
                        </div>           
                    </div>

                </div>
            </div>
        </form>

        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>

                <div id='loading'></div>
                <div id='c-data'>

                </div>
                
                
                <div id='loading2'></div>
                <div id='c-data2'>

                </div>

            </div>
        </div>
        
    </div>
    <!--end row-->
</div>

</script>

<script type="text/javascript">
    function TampilArea(icabang, iarea) {
        var ecab = document.getElementById(icabang).value;
        
        $.ajax({
            type:"post",
            url:"module/md_m_pindahcust/viewdata.php?module=viewdataarea",
            data:"ucab="+ecab,
            success:function(data){
                $("#"+iarea).html(data);
            }
        });
    }
    
    function TampilkanData() {
        var ecab = document.getElementById('e_cabang').value;
        var earea = document.getElementById('e_area').value;
        
        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/md_m_pindahcust/viewdatatabel.php?module=tampilkandata",
            data:"ucab="+ecab+"&uarea="+earea,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
            }
        });
    }
    
    function TampilkanData2() {
        var ecabbaru = document.getElementById('e_cabangbaru').value;
        var eareabaru = document.getElementById('e_areabaru').value;
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/md_m_pindahcust/viewdatatabel2.php?module=tampilkandata",
            data:"ucabbaru="+ecabbaru+"&uareabaru="+eareabaru,
            success:function(data){
                $("#c-data2").html(data);
                $("#loading2").html("");
            }
        });
    }
    
    function ProsesDataPindah() {
        var ecab = document.getElementById('e_cabang').value;
        var earea = document.getElementById('e_area').value;
        var ecabbaru = document.getElementById('e_cabangbaru').value;
        var eareabaru = document.getElementById('e_areabaru').value;
        
        if (ecab==""){
            alert("Cabang lama masih kosong...");
            return 0;
        }
        if (earea==""){
            alert("Area lama masih kosong...");
            return 0;
        }
        
        if (ecabbaru==""){
            alert("Cabang baru masih kosong...");
            return 0;
        }
        if (eareabaru==""){
            alert("Area baru masih kosong...");
            return 0;
        }
        
        ok_ = 1;
        if (ok_) {
            var r=confirm("Apakah akan proses pindah data customer...?")
            if (r==true) {
                //document.write("You pressed OK!")
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                
                document.getElementById("demo-form2").action = "module/md_m_pindahcust/aksi_pindahcust.php?module="+module+"&act=pindahdata"+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
    }
</script>