<script>
    function CariCardid($data1) {
        var udata=$($data1).val();
        $.ajax({
            type:"post",
            url:"module/mod_employee/search.php?module=cari_cardid",
            data:"udata="+udata,
            success:function(data){
                document.getElementById('e_id').value=data;
                document.getElementById("e_name").focus();
                HapusData();
            }
        });
    }

    function HapusData() {
        document.getElementById("e_name").value = "";
        document.getElementById("e_bcity").value = "";
        document.getElementById("e_ktp").value = "";
        document.getElementById("e_npwp").value = "";
        document.getElementById("e_celp").value = "";
        document.getElementById("e_tlp").value = "";
        document.getElementById("e_addr").value = "";
        document.getElementById("e_email").value = "";
    }
</script>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Data Employee</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/mod_employee/aksi_employee.php";
        switch($_GET['act']){
            default:

                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                            onclick=\"window.location.href='?module=$_GET[module]&xmodp=$_GET[xmodp]&act=tambahbaru';\">
                            <small>Data Employee</small></h2>
                            <div class='clearfix'></div>
                            </div>";

                        //isi content
                        echo "<div class='x_content'>";
                            //isi kata-kata
                            /*
                            echo "<p class='text-muted font-13 m-b-30'>";
                            echo "";
                            echo "</p>";
                             *
                             */

                            echo "<table id='datatable' class='table table-striped table-bordered'>";
                            echo "<thead><tr><th width='10px'>No</th><th width='70px'>Cardid</th>
                                <th>Employee</th><th>Alamat</th><th width='100px'>Jabatan</th><th width='110px'>Aksi</th></tr></thead>";
                            echo "<tbody>";
                            $no=1;
                            $tampil = mysqli_query($cnmy, "SELECT * FROM v_employee WHERE CARDID NOT IN ('00001', '00002') order by EMPLOYEE, CARDID");
                            while ($r=mysqli_fetch_array($tampil)){
                                echo "<tr scope='row'><td>$no</td>";
                                echo "<td>$r[CARDID]</td>";
                                echo "<td>$r[EMPLOYEE]</td>";
                                echo "<td>$r[ALAMAT]</td>";
                                echo "<td>$r[JABATAN]</td>";
                                $ketnon="Aktif";$ketnya="Data $r[EMPLOYEE] akan diaktifkan...?";
                                if ($r['STSNONAKTIF']==0 or empty($r['STSNONAKTIF'])){
                                    $ketnon="NonAktif";$ketnya="Data $r[EMPLOYEE] akan dinonaktifkan...?";
                                }
                                echo "<td>";//AKSI
                                    echo " <a class='btn btn-success btn-sm' href=?module=$_GET[module]&xmodp=$_GET[xmodp]&act=editdata&id=$r[CARDID]>Edit</a>
                                        <a class='btn btn-danger btn-sm' href=\"$aksi?module=$_GET[module]&act=hapus&id=$r[CARDID]&xmodp=$_GET[xmodp]&aktif=$ketnon\"
                                        onClick=\"return confirm('$ketnya')\">$ketnon</a>";
                                echo "</td>";
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
                ?> <script> window.onload = function() { document.getElementById("e_name").focus(); } </script> <?PHP

                echo "<form method='POST' action='$aksi?module=$_GET[module]&act=input&xmodp=$_GET[xmodp]'>";

                    echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='col-md-12 col-sm-12 col-xs-12'>
                            <h2><input type='button' value='Kembali' onclick='self.history.back()' class='btn btn-default'>";
                        echo "<button class='btn btn-primary' type='reset'>Reset</button>
                            <button type='submit' class='btn btn-success'>Simpan</button>";
                        echo "</h2><div class='clearfix'></div></div>";
                        
                        echo "<div class='col-md-6 col-xs-12'>";
                            echo "<div class='x_panel'>";
                              echo "<div class='x_content form-horizontal form-label-left'><br />";
                                
                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='btn_add'><span class='required'></span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='button' value='Add New' onclick='CariCardid()' class='btn btn-info btn-xs' name='btn_add' id='btn_add'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_id'>Cardid <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_id' name='e_id' required='required' class='form-control col-md-7 col-xs-12' Readonly>
                                        </div>";
                                    echo "</div>";// disabled='disabled'

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_name'>Nama Karyawan <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_name' name='e_name' required='required' class='form-control col-md-7 col-xs-12'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_bcity'>Tempat Lahir <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_bcity' name='e_bcity' required='required' class='form-control col-md-7 col-xs-12'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl01'>Tanggal Lahir <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='tgl01' name='e_born' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl lahir' value='01 November 2017' placeholder='dd mmm yyyy' Readonly>
                                        </div>";
                                    echo "</div>";

                                    /*
                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_born'>Tanggal Lahir <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                        echo "<input type='text' class='form-control has-feedback-left' id='single_cal2' name='e_born' placeholder='tgl lahir' aria-describedby='inputSuccess2Status'>
                                            <span class='fa fa-calendar-o form-control-feedback left' aria-hidden='true'></span>
                                            <span id='inputSuccess2Status' class='sr-only'>(success)</span>";
                                        echo "</div>";
                                    echo "</div>";
                                     * 
                                     */

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ktp'>No KTP <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_ktp' name='e_ktp' required='required' class='form-control col-md-7 col-xs-12'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_npwp'>NPWP <span class='required'></span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_npwp' name='e_npwp' class='form-control col-md-7 col-xs-12'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_genre'>Jenis Kelamin <span class='required'></span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                    echo "<select class='form-control' id='e_genre' name='e_genre'>";
                                        $tampil=mysqli_query($cnmy, "SELECT GKODE, GNAMA FROM tgender");
                                        while($r=mysqli_fetch_array($tampil)){
                                            echo "<option value='$r[GKODE]'>$r[GNAMA]</option>";
                                        }
                                    echo "</select>";
                                    echo "</div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_bold'>Golongan Darah <span class='required'></span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                    echo "<select class='form-control' id='e_bold' name='e_bold'>";
                                        $tampil=mysqli_query($cnmy, "SELECT BKODE FROM tblood order by BKODE");
                                        while($r=mysqli_fetch_array($tampil)){
                                            echo "<option value='$r[BKODE]'>$r[BKODE]</option>";
                                        }
                                    echo "</select>";
                                    echo "</div>";
                                    echo "</div>";
                                      
                              echo "</div>";
                            echo "</div>";
                          echo "</div>";


                          
                        echo "<div class='col-md-6 col-xs-12'>";
                            echo "<div class='x_panel'>";
                              echo "<div class='x_content form-horizontal form-label-left'><br />";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_agama'>Agama <span class='required'></span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                    echo "<select class='form-control' id='cb_agama' name='cb_agama'>";
                                        $tampil=mysqli_query($cnmy, "SELECT KDRELIGION, RELIGIDESC FROM t_religion order by KDRELIGION");
                                        while($r=mysqli_fetch_array($tampil)){
                                            if ($r['KDRELIGION']=="01")
                                                echo "<option value='$r[KDRELIGION]' selected>$r[RELIGIDESC]</option>";
                                            else
                                                echo "<option value='$r[KDRELIGION]'>$r[RELIGIDESC]</option>";
                                        }
                                    echo "</select>";
                                    echo "</div>";
                                    echo "</div>";
                                    
                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_nikah'>Menikah <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                    echo "<select class='form-control' id='cb_nikah' name='cb_nikah'>";
                                        $tampil=mysqli_query($cnmy, "SELECT KDMARITAL, MARITALDESC FROM t_marital order by MARITALDESC");
                                        while($r=mysqli_fetch_array($tampil)){
                                            echo "<option value='$r[KDMARITAL]'>$r[MARITALDESC]</option>";
                                        }
                                    echo "</select>";
                                    echo "</div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl02'>Tanggal Menikah <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='tgl02' name='e_dom' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl menikah' value='01 November 2017' placeholder='dd mmm yyyy' Readonly>
                                        </div>";
                                    echo "</div>";

                                    /*
                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_dom'>Tanggal Menikah <span class='required'></span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                        echo "<input type='text' class='form-control has-feedback-left' id='single_cal3' name='e_dom' placeholder='tgl menikah' aria-describedby='inputSuccess2Status'>
                                            <span class='fa fa-calendar-o form-control-feedback left' aria-hidden='true'></span>
                                            <span id='inputSuccess2Status' class='sr-only'>(success)</span>";
                                        echo "</div>";
                                    echo "</div>";
                                     *
                                     */

                                    
                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_celp'>No Hp <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_celp' name='e_celp' required='required' class='form-control col-md-7 col-xs-12'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_tlp'>Telpon <span class='required'></span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_tlp' name='e_tlp' class='form-control col-md-7 col-xs-12'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_addr'>Alamat <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_addr' name='e_addr' required='required' class='form-control col-md-7 col-xs-12'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_addr'>Email <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_email' name='e_email' required='required' class='form-control col-md-7 col-xs-12'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_jabatan'>Posisi <span class='required'></span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                    echo "<select class='form-control' id='e_jabatan' name='e_jabatan'>";
                                        $tampil=mysqli_query($cnmy, "SELECT KDJBT, JABATAN, LVLPOSISI FROM t_jabatan order by JABATAN");
                                        while($r=mysqli_fetch_array($tampil)){
                                            echo "<option value='$r[KDJBT]'>$r[JABATAN]</option>";
                                        }
                                    echo "</select>";
                                    echo "</div>";
                                    echo "</div>";


                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_divisi'>Divisi <span class='required'></span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                    echo "<select class='form-control' id='e_divisi' name='e_divisi'>";
                                        $tampil=mysqli_query($cnmy, "SELECT KDDIVISI, DIVISI FROM t_divisi order by DIVISI");
                                        while($r=mysqli_fetch_array($tampil)){
                                            echo "<option value='$r[KDDIVISI]'>$r[DIVISI]</option>";
                                        }
                                    echo "</select>";
                                    echo "</div>";
                                    echo "</div>";

                                    echo "</div>";
                                    

                              echo "</div>";
                            echo "</div>";
                          echo "</div>";

                        echo "</div>";//panel
                    echo "</div>";

                echo "</form>";
                
            break;

            case "editdata":

                include "edit_employee.php";

            break;

        }
        ?>

    </div>
    <!--end row-->
</div>

<script type="text/javascript">
    function toggleCexBox(source) {
        var aInputs = document.getElementsByTagName('input');
        for (var i=0;i<aInputs.length;i++) {
            if (aInputs[i] != source && aInputs[i].className == source.className) {
                aInputs[i].checked = source.checked;
            }
        }
    }
</script>