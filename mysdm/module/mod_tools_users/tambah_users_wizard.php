<!-- Smart Wizard -->
<div id="wizard" class="form_wizard wizard_horizontal">

    <ul class="wizard_steps">
        <li>
            <a href="#step-1">
                <span class="step_no">1</span>
                <span class="step_descr">Data Kelompok<br /><small>Level 3</small></span>
            </a>
        </li>
        <li>
            <a href="#step-2">
                <span class="step_no">2</span>
                <span class="step_descr">Data Kelompok<br /><small>Level 2</small></span>
            </a>
        </li>
        <li>
            <a href="#step-3">
                <span class="step_no">3</span>
                <span class="step_descr">Data Kelompok<br /><small>Level 1</small></span>
            </a>
        </li>
        <!--
        <li>
            <a href="#step-4">
                <span class="step_no">4</span>
                <span class="step_descr">Step 4<br /><small>Step 4 description</small></span>
            </a>
        </li>
        -->
    </ul>

    <div id="step-1">

        <script> window.onload = function() { document.getElementById("e_kelompok").focus(); } </script>
        <script>
            function getDataModal1(d1){
                document.form1.e_kelompok.value = d1;
            }
        </script>
        <!-- Modal -->
        <div class='modal fade' id='myModal' role='dialog'>

            <div class='modal-dialog modal-lg'>

                <!-- Modal content-->
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'>&times;</button>
                        <h4 class='modal-title'>Pilih Kelompok</h4>
                    </div>

                    <div class='modal-body'>
                        <?PHP

                        echo "<table id='datatable-checkbox' class='table table-striped table-bordered'>";
                        echo "<thead><tr><th width='10px'>No</th><th width='60px'>Kode</th><th width='600%'>Nama</th></tr></thead>";
                        echo "<tbody>";
                        $no=1;
                        $tampil = mysql_query("SELECT KODE_LVL2, NAMA_LVL2 FROM v_kelompok_lvl order by KODE_LVL2");
                        while ($r=mysql_fetch_array($tampil)){
                            echo "<tr scope='row'><td>$no</td>";
                            echo "<td><a data-dismiss='modal' href='#' onClick=\"getDataModal1('$r[KODE_LVL2]')\">
                                $r[KODE_LVL2]</a></td>";
                            echo "<td>$r[NAMA_LVL2]</td>";
                            echo "</tr>";
                            $no++;
                        }
                        echo "</tbody>";
                        echo "</table>";

                        ?>
                    </div>

                    <div class='modal-footer'>
                        <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                    </div>
                </div>

            </div>
        </div>


        <div class="">

            <!--row-->
            <div class="row">

                <?php

                        echo "<form method='POST' action='$aksi?module=$_GET[module]&act=input&xmodp=$_GET[xmodp]' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>";

                        echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                            //panel
                            echo "<div class='x_panel'>";


                                //isi content
                                echo "<div class='x_content'>";

                                //title
                                /*
                                echo "<div class='col-md-12 col-sm-12 col-xs-12'>
                                    <h2>
                                    <input type='button' value='Kembali' onclick='self.history.back()' class='btn btn-default'>
                                    <button class='btn btn-primary' type='reset'>Reset</button>
                                    <button type='submit' class='btn btn-success'>Simpan</button>";
                                echo "</h2><div class='clearfix'></div></div>";
                                 * 
                                 */

                                    //isi kata-kata
                                    /*
                                    echo "<p class='text-muted font-13 m-b-30'>";
                                    echo "";
                                    echo "</p>";
                                     *
                                     */

                                echo "<div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_kelompok'>Kelompok <span class='required'>*</span></label>
                                    <div class='col-sm-9 col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group '>
                                            <span class='input-group-btnHapus'>
                                                <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal'>Go!</button>
                                            </span>
                                            <input type='text' class='form-control' id='e_kelompok' name='e_kelompok' required='required' data-inputmask=\"'mask' : '****.***.***'\">
                                        </div>
                                    </div>
                                </div>";

                                echo "<div class='form-group'>";
                                echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_klpkode'>Kode/No Perkiraan <span class='required'>*</span></label>";
                                echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                                    <input type='text' id='e_klpkode' name='e_klpkode' required='required' class='form-control col-md-7 col-xs-12' data-inputmask=\"'mask' : '****.***.***'\">
                                    </div>";
                                echo "</div>";

                                echo "<div class='form-group'>";
                                echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Keterangan <span class='required'>*</span></label>";
                                echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                                    <input type='text' id='e_ket' name='e_ket' required='required' class='form-control col-md-7 col-xs-12' >
                                    </div>";
                                echo "</div>";

                                echo "<div class='form-group'>";
                                echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Tipe <span class='required'>*</span></label>";
                                echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='btn-group' data-toggle='buttons'>
                                            <label class='btn btn-default'><input type='radio' class='flat' name='rb_tipe' id='rb_tipe1' value='A'> Aktiva </label>
                                            <label class='btn btn-default'><input type='radio' class='flat' name='rb_tipe' id='rb_tipe2' value='P'> Passiva </label>
                                            <label class='btn btn-default'><input type='radio' class='flat' name='rb_tipe' id='rb_tipe3' value='R'> Rugi Laba </label>
                                        </div>
                                    </div>";
                                echo "</div>";

                                echo "<p/>&nbsp;<div class='form-group'>";
                                echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'></label>";
                                echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                                    <input type='button' value='Kembali' onclick='self.history.back()' class='btn btn-default'>
                                    <button class='btn btn-primary' type='reset'>Reset</button>
                                    <button type='submit' class='btn btn-success'>Simpan</button>
                                    </div>";
                                echo "</div>";


                                echo "</div>";//end x_content

                            echo "</div>";//end panel

                        echo "</div>";

                        echo "</form>";

                ?>

            </div>
            <!--end row-->
        </div>

    </div>

    <div id="step-2">

    </div>

    <div id="step-3">

    </div>

</div>
<!-- End SmartWizard Content -->

