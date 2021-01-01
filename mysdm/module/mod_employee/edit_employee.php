<script> window.onload = function() { document.getElementById("e_name").focus(); } </script>
<?PHP

                $edit=mysqli_query($cnmy, "SELECT * FROM v_employee WHERE CARDID='$_GET[id]' and CARDID NOT IN ('00001', '00002')");
                $t=mysqli_fetch_array($edit);

                $tgllahir= date("d F Y", strtotime($t['TGL_LAHIR']));
                $tglmarit= date("d F Y", strtotime($t['DATE_MARITAL']));

                echo "<form method='POST' action='$aksi?module=$_GET[module]&act=update&xmodp=$_GET[xmodp]'>";

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
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_id'>Cardid <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_id' name='e_id' required='required' class='form-control col-md-7 col-xs-12' value='$t[CARDID]' Readonly>
                                        </div>";
                                    echo "</div>";// disabled='disabled'

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_name'>Nama Karyawan <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_name' name='e_name' required='required' class='form-control col-md-7 col-xs-12' value='$t[EMPLOYEE]'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_bcity'>Tempat Lahir <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_bcity' name='e_bcity' required='required' class='form-control col-md-7 col-xs-12' value='$t[T_LAHIR]'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl01'>Tanggal Lahir <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='tgl01' name='e_born' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl lahir' value='$tgllahir' placeholder='dd mmm yyyy' Readonly>
                                        </div>";
                                    echo "</div>";

                                    /*
                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_born'>Tanggal Lahir <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                        echo "<input type='text' class='form-control has-feedback-left' id='single_cal2' name='e_born' placeholder='tgl lahir' aria-describedby='inputSuccess2Status' value='$tgllahir'>
                                            <span class='fa fa-calendar-o form-control-feedback left' aria-hidden='true'></span>
                                            <span id='inputSuccess2Status' class='sr-only'>(success)</span>";
                                        echo "</div>";
                                    echo "</div>";
                                     * 
                                     */

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ktp'>No KTP <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_ktp' name='e_ktp' required='required' class='form-control col-md-7 col-xs-12' value='$t[KTP]'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_npwp'>NPWP <span class='required'></span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_npwp' name='e_npwp' class='form-control col-md-7 col-xs-12' value='$t[NPWP]'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_genre'>Jenis Kelamin <span class='required'></span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                    echo "<select class='form-control' id='e_genre' name='e_genre'>";
                                        $tampil=mysqli_query($cnmy, "SELECT GKODE, GNAMA FROM tgender");
                                        while($r=mysqli_fetch_array($tampil)){
                                            if ($r['GKODE']==$t['JEKEL'])
                                                echo "<option value='$r[GKODE]' selected>$r[GNAMA]</option>";
                                            else
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
                                            if ($r['BKODE']==$t['BLOOD'])
                                                echo "<option value='$r[BKODE]' selected>$r[BKODE]</option>";
                                            else
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
                                            if ($r['KDRELIGION']==$t['KDRELIGION'])
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
                                            if ($r['KDMARITAL']==$t['KDMARITAL'])
                                                echo "<option value='$r[KDMARITAL]' selected>$r[MARITALDESC]</option>";
                                            else
                                                echo "<option value='$r[KDMARITAL]'>$r[MARITALDESC]</option>";
                                        }
                                    echo "</select>";
                                    echo "</div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl02'>Tanggal Menikah <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='tgl02' name='e_dom' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl menikah'  value='$tglmarit' placeholder='dd mmm yyyy' Readonly>
                                        </div>";
                                    echo "</div>";
                                    /*
                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_dom'>Tanggal Menikah <span class='required'></span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                        echo "<input type='text' class='form-control has-feedback-left' id='single_cal3' name='e_dom' placeholder='tgl menikah' aria-describedby='inputSuccess2Status' value='$tglmarit'>
                                            <span class='fa fa-calendar-o form-control-feedback left' aria-hidden='true'></span>
                                            <span id='inputSuccess2Status' class='sr-only'>(success)</span>";
                                        echo "</div>";
                                    echo "</div>";
                                     *
                                     */

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_celp'>No Hp <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_celp' name='e_celp' required='required' class='form-control col-md-7 col-xs-12' value='$t[HP]'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_tlp'>Telpon <span class='required'></span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_tlp' name='e_tlp' class='form-control col-md-7 col-xs-12' value='$t[TELP]'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_addr'>Alamat <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_addr' name='e_addr' required='required' class='form-control col-md-7 col-xs-12' value='$t[ALAMAT]'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_addr'>Email <span class='required'>*</span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_email' name='e_email' required='required' class='form-control col-md-7 col-xs-12' value='$t[EMAIL]'>
                                        </div>";
                                    echo "</div>";

                                    echo "<div class='form-group'>";
                                    echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_jabatan'>Posisi <span class='required'></span></label>";
                                    echo "<div class='col-md-9 col-sm-9 col-xs-12'>";
                                    echo "<select class='form-control' id='e_jabatan' name='e_jabatan'>";
                                        $tampil=mysqli_query($cnmy, "SELECT KDJBT, JABATAN, LVLPOSISI FROM t_jabatan order by JABATAN");
                                        while($r=mysqli_fetch_array($tampil)){
                                            if ($r['KDJBT']==$t['KDJBT'])
                                                echo "<option value='$r[KDJBT]' selected>$r[JABATAN]</option>";
                                            else
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
                                            if ($r['KDDIVISI']==$t['KDDIVISI'])
                                                echo "<option value='$r[KDDIVISI]' selected>$r[DIVISI]</option>";
                                            else
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
?>
