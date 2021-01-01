<?PHP
    $fkaryawan=$_SESSION['IDCARD'];
    $query = "select PILIHAN from dbmaster.t_barang_wewenang WHERE karyawanid='$fkaryawan'";
    $tampil_= mysqli_query($cnmy, $query);
    $pn= mysqli_fetch_array($tampil_);
    $ppilihanwewenang=$pn['PILIHAN'];
    echo "<input type='hidden' id='e_wwnpilihan' name='e_wwnpilihan' value='$ppilihanwewenang' Readonly>";
            
            
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Group Produk Gimmick</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/mod_brg_grpprod/aksi_grpprod.php";
        switch($_GET['act']){
            default:

                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                            onclick=\"window.location.href='?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru';\">
                            <small></small>
                            </h2>
                            <div class='clearfix'></div>
                            </div>";

                        //isi content
                        echo "<div class='x_content'>";

                            echo "<table id='datatable' class='table table-striped table-bordered'>";
                            echo "<thead><tr><th width='10px'>No</th><th width='60px'>Kode</th><th>Nama</th><th>Approve</th><th width='100px'>Aktif</th></tr></thead>";
                            echo "<tbody>";
                            $no=1;
                            $query = "SELECT a.*, b.nama NAMA_KRY FROM dbmaster.t_divisi_gimick a LEFT JOIN hrd.karyawan b "
                                    . " on a.APVBY=b.karyawanId WHERE 1=1 ";
                            if ($ppilihanwewenang=="AL") {
                            }else{
                                $query .=" AND a.PILIHAN='$ppilihanwewenang' ";
                            }
                            $query .= " order by a.DIVISINM";
                            $tampil = mysqli_query($cnmy, $query);
                            while ($r=mysqli_fetch_array($tampil)){
                                echo "<tr scope='row'><td>$no</td>";
                                echo "<td>$r[DIVISIID]</td>";
                                echo "<td>$r[DIVISINM]</td>";
                                echo "<td>$r[NAMA_KRY]</td>";
                                echo "<td>$r[STSAKTIF]</td>";
                                /*
                                echo "<td>";//AKSI
                                    echo " <a class='btn btn-success btn-xs href=?module=$_GET[module]&idmenu=$_GET[idmenu]&act=editdata&id=$r[DIVISIID]>Edit</a>
                                        <a class='btn btn-danger btn-xs' href=\"$aksi?module=$_GET[module]&act=hapus&id=$r[DIVISIID]&idmenu=$_GET[idmenu]\"
                                        onClick=\"return confirm('Apakah Anda melakukan proses?')\">Aktif</a>";
                                echo "</td>";
                                 * 
                                 */
                                echo "</tr>";
                                $no++;
                            }
                            echo "</tbody>";
                            echo "</table>";
                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";

            break;

            case "tambahbaru":
                ?> <script> window.onload = function() { document.getElementById("e_divisiid").focus(); } </script> <?PHP
                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <small>Tambah Baru</small></h2>
                            <div class='clearfix'></div>
                            </div>";

                        //isi content
                        echo "<div class='x_content'><br/>";
                        echo "<form method='POST' action='$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]' id='demo-form2' data-parsley-validate class='form-horizontal form-label-left'>";
                        ?>
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Untuk Divisi <span class='required'></span></label>
                            <div class='col-xs-3'>
                                  <select class='form-control input-sm' id='cb_gprod' name='cb_gprod' data-live-search="true">
                                      <?PHP 
                                        if ($ppilihanwewenang=="AL") {
                                            echo "<option value='ET' selected>ETHICAL</option>";
                                            echo "<option value='OT'>OTC</option>";
                                        }elseif ($ppilihanwewenang=="ET") {
                                            echo "<option value='ET' selected>ETHICAL</option>";
                                        }elseif ($ppilihanwewenang=="OT") {
                                            echo "<option value='OT' selected>OTC</option>";
                                        }
                                      ?>
                                  </select>
                            </div>
                        </div>
                        <?PHP
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmgrpprod'>Kode <span class='required'>*</span></label>";
                        echo "<div class='col-md-3 col-sm-3 col-xs-3'>
                            <input type='text' id='e_divisiid' name='e_divisiid' required='required' class='form-control col-md-7 col-xs-12' maxlength='5' onkeyup=\"this.value = this.value.toUpperCase()\">
                            </div>";
                        echo "</div>";
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmgrpprod'>Nama <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_nmgrpprod' name='e_nmgrpprod' required='required' class='form-control col-md-7 col-xs-12' onkeyup=\"this.value = this.value.toUpperCase()\">
                            </div>";
                        echo "</div>";

                        echo "<div class='ln_solid'></div>";
                        echo "<div class='form-group'>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12 col-md-offset-3'>
                            <button class='btn btn-primary' type='reset'>Reset</button>
                            <button type='submit' class='btn btn-success'>Save</button>
                            </div>";
                        echo "</div>";

                        echo "</form>";
                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";
            break;

            case "editdata":
                ?> <script> window.onload = function() { document.getElementById("e_nmgrpprod").focus(); } </script> <?PHP

                $edit=mysqli_query($cnmy, "SELECT * FROM dbmaster.t_divisi_gimick WHERE DIVISIID='$_GET[id]'");
                $r=mysqli_fetch_array($edit);
                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <small>Edit Data</small></h2>
                            <div class='clearfix'></div>
                            </div>";

                        //isi content
                        echo "<div class='x_content'><br/>";
                        echo "<form method='POST' action='$aksi?module=$_GET[module]&act=update&idmenu=$_GET[idmenu]' id='demo-form2' data-parsley-validate class='form-horizontal form-label-left'>
                            <input type='hidden' name='id' value='$r[DIVISIID]'>";

                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmgrpprod'>Kode <span class='required'>*</span></label>";
                        echo "<div class='col-md-3 col-sm-3 col-xs-3'>
                            <input type='text' id='e_divisiid' name='e_divisiid' required='required' class='form-control col-md-7 col-xs-12' value='$r[DIVISIID]' Readonly maxlength='5' onkeyup=\"this.value = this.value.toUpperCase()\">
                            </div>";
                        echo "</div>";

                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nmgrpprod'>Nama <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_nmgrpprod' name='e_nmgrpprod' required='required' class='form-control col-md-7 col-xs-12' value='$r[DIVISINM]' onkeyup=\"this.value = this.value.toUpperCase()\">
                            </div>";
                        echo "</div>";

                        echo "<div class='ln_solid'></div>";
                        echo "<div class='form-group'>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12 col-md-offset-3'>
                            <button class='btn btn-primary' type='reset'>Reset</button>
                            <button type='submit' class='btn btn-success'>Save</button>
                            </div>";
                        echo "</div>";

                        echo "</form>";
                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";

            break;

        }
        ?>

    </div>
    <!--end row-->
</div>
