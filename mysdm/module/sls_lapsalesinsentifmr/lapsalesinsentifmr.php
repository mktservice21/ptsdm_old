<?PHP include "config/cek_akses_modul.php"; ?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Report Prediksi Insentif MR</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        include "config/koneksimysqli_ms.php";
        $pidkaryawan=$_SESSION['IDCARD'];
        $pidjabatan=$_SESSION['JABATANID'];
        $pidgroup=$_SESSION['GROUP'];
        
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:

                $hari_ini = date("Y-m-d");
                //$tgl_pertama=date('F Y', strtotime('-1 month', strtotime($hari_ini)));
                $tgl_pertama = date('F Y', strtotime($hari_ini));
                ?>
                <script>
                    function disp_confirm(pText)  {
                        var ekryid = document.getElementById("cb_karyawan").value;
                        if (ekryid=="") {
                            alert("MR harus diisi....");
                            return false;
                        }
                        if (pText == "excel") {
                            document.getElementById("form1").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
                            document.getElementById("form1").submit();
                            return 1;
                        }else{
                            document.getElementById("form1").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
                            document.getElementById("form1").submit();
                            return 1;
                        }
                    }
                    
                    
                    
                </script>
                
                <script type="text/javascript">
                    $(document).ready(function() {
                        $('#cblnn01').datetimepicker({
                            ignoreReadonly: true,
                            allowInputToggle: true,
                            format: 'MMMM YYYY',
                            minDate: new Date(2021, 0 , 01),
                        });
                    });
                    
                </script>
                
                
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        <form name='form1' id='form1' method='POST' action="<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>" enctype='multipart/form-data' target="_blank">
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <h2>
                                    <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                                    <button type='button' class='btn btn-danger' onclick="disp_confirm('excel')">Excel</button>
                                    <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                                </h2>
                                <div class='clearfix'></div>
                            </div>

                            <!--kiri-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cbln01'>Bulan <span class='required'></span></label>
                                            <div class='col-md-8'>
                                                <div class="form-group">
                                                    <div class='input-group date' id='cblnn01'>
                                                        <input type='text' id='cblnn01' name='bulan' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>MR <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_karyawan' id='cb_karyawan' onchange="">
                                                    <?PHP
                                                    if ($pidjabatan=="15") {
                                                        $query_data = "select b.karyawanid as karyawanid, b.nama as nama from ms.karyawan as b WHERE b.karyawanid='$pidkaryawan' ";
                                                    }elseif ($pidjabatan=="18" OR $pidjabatan=="10") {
                                                        $query_data = "select distinct a.karyawanid as karyawanid, b.nama as nama 
                                                                from sls.imr0 as a 
                                                                JOIN sls.ispv0 as c on a.icabangid=c.icabangid and a.areaid=c.areaid and a.divisiid=c.divisiid join ms.karyawan as b 
                                                                on a.karyawanid=b.karyawanId WHERE c.karyawanid='$pidkaryawan'";
                                                    }elseif ($pidjabatan=="08") {
                                                        $query_data = "select distinct a.karyawanid as karyawanid, b.nama as nama 
                                                                from sls.imr0 as a 
                                                                JOIN sls.idm0 as c on a.icabangid=c.icabangid join ms.karyawan as b 
                                                                on a.karyawanid=b.karyawanId WHERE c.karyawanid='$pidkaryawan'";
                                                    }elseif ($pidjabatan=="20") {
                                                        $query_data = "select distinct a.karyawanid as karyawanid, b.nama as nama 
                                                                from sls.imr0 as a 
                                                                JOIN sls.ism0 as c on a.icabangid=c.icabangid join ms.karyawan as b 
                                                                on a.karyawanid=b.karyawanId WHERE c.karyawanid='$pidkaryawan'";
                                                    }else{
                                                        if ($pidkaryawan=="0000000158") {
                                                            $query_data = "select distinct a.karyawanid as karyawanid, b.nama as nama 
                                                                from sls.imr0 as a join ms.karyawan as b on a.karyawanid=b.karyawanid 
                                                                JOIN sls.icabang as c on a.icabangid=c.icabangid WHERE c.region='B'";
                                                        }elseif ($pidkaryawan=="0000000159") {
                                                            $query_data = "select distinct a.karyawanid as karyawanid, b.nama as nama 
                                                                from sls.imr0 as a join ms.karyawan as b on a.karyawanid=b.karyawanid 
                                                                JOIN sls.icabang as c on a.icabangid=c.icabangid WHERE c.region='T'";
                                                        }else{
                                                            echo "<option value='' selected>--Pilih--</option>";
                                                            if ($pidgroup=="1" OR $pidgroup=="24") {
                                                                $query_data = "select DISTINCT a.karyawanid as karyawanid, b.nama as nama "
                                                                        . " from sls.imr0 as a JOIN ms.karyawan as b on a.karyawanid=b.karyawanid";
                                                            }
                                                        }
                                                    }
                                                    if (!empty($query_data)) {
                                                        
                                                        $query =$query_data." ORDER BY b.nama";
                                                        $tampil = mysqli_query($cnms, $query);
                                                        $ketemu=mysqli_num_rows($tampil);
                                                        
                                                        if ((INT)$ketemu<=0) echo "<option value=''>_blank</option>";
                                                        
                                                        while ($rx= mysqli_fetch_array($tampil)) {
                                                            $nidkry=$rx['karyawanid'];
                                                            $nnmkry=$rx['nama'];
                                                            if ($nidkry==$pidkaryawan)
                                                                echo "<option value='$nidkry' selected>$nnmkry</option>";
                                                            else
                                                                echo "<option value='$nidkry'>$nnmkry</option>";
                                                        }
                                                        
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            <!--end kiri-->

                        </form>
                    </div><!--end xpanel-->
                </div>
                
                


                
                <?PHP
            break;

            case "tambahbaru":

            break;
        }
        ?>
    </div>
    <!--end row-->
</div>