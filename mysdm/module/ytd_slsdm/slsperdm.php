<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Report Sales YTD DM</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        include "config/koneksimysqli_ms.php";
        
        $pmyidcard=$_SESSION['IDCARD'];
        $pmyjabatanid=$_SESSION['JABATANID'];
        $pmygroupid=$_SESSION['GROUP'];
        
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:

                $hari_ini = date("Y-m-d");
                //$tgl_pertama=date('F Y', strtotime('-1 month', strtotime($hari_ini)));
                $tgl_pertama = date('F Y', strtotime($hari_ini));
                ?>
                <script>
                    function disp_confirm(pText)  {
                        var ekryid = document.getElementById("cb_karyawanid").value;
                        if (ekryid=="") {
                            alert("mr harus diisi....");
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
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        <form name='form1' id='form1' method='POST' action="<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>" enctype='multipart/form-data' target="_blank">
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <h2>
                                    <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                                    <!--<button type='button' class='btn btn-danger' onclick="disp_confirm('excel')">Excel</button>-->
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
                                                    <div class='input-group date' id='cbln01'>
                                                        <input type='text' id='cbln01' name='bulan' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='distibutor'>DM <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control' name='cb_karyawanid' id='cb_karyawanid'>
                                                    <?PHP
                                                    echo "<option value=''>-- Pilih --</option>";
                                                    if ($pmyjabatanid=="08") {
                                                        $query= "select b.karyawanid, b.nama from ms.karyawan b where karyawanid='$pmyidcard' ";
                                                    }elseif ($pmyjabatanid=="20") {
                                                        $query =" select DISTINCT a.karyawanid, b.nama from ms.idm0 a join ms.karyawan b on a.karyawanid=b.karyawanid "
                                                                . " JOIN ms.ism0 c on a.icabangid=c.icabangid where c.karyawanid='$pmyidcard' ";
                                                    }else{
                                                        if ($pmyidcard=="0000000158" OR $pmyidcard=="0000000159") {
                                                            $query =" select DISTINCT a.karyawanid, b.nama from ms.idm0 a join ms.karyawan b on a.karyawanid=b.karyawanid "
                                                                    . " JOIN ms.icabang c on a.icabangid=c.icabangid where 1=1 ";
                                                            if ($pmyidcard=="0000000158") {
                                                                $query .= " AND c.region='B' ";
                                                            }elseif ($pmyidcard=="0000000159") {
                                                                $query .= " AND c.region='T' ";
                                                            }
                                                        }else{
                                                            $query =" select DISTINCT a.karyawanid, b.nama from ms.idm0 a join ms.karyawan b on a.karyawanid=b.karyawanid ";
                                                        }
                                                    }
                                                    $query .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  "
                                                            . " and LEFT(b.nama,7) NOT IN ('NN DM - ')  "
                                                            . " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') "
                                                            . " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR') ";
                                                    $query .=" order by b.nama";
                                                    $tampil = mysqli_query($cnms, $query);
                                                    while ($rx= mysqli_fetch_array($tampil)) {
                                                        $nidkrydm=$rx['karyawanid'];
                                                        $nnmkrydm=$rx['nama'];
                                                        if ($pmyidcard==$nidkrydm)
                                                            echo "<option value='$nidkrydm' selected>$nnmkrydm</option>";
                                                        else
                                                            echo "<option value='$nidkrydm'>$nnmkrydm</option>";
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