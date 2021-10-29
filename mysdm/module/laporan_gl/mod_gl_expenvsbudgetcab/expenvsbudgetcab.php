<?php

    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupid=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    $pmobile=$_SESSION['MOBILE'];
    
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    $pact=$_GET['act'];
    $aksi="eksekusi3.php";
    
    $hari_ini = date("Y-m-d");
    $ptahun = date('Y', strtotime($hari_ini));
    
?>


<div class="">

    <div class="page-title"><div class="title_left"><h3>Expense VS Budget</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        
      
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' id='d-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                    
                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <div class='input-group date' id='thn01'>
                                                <input type='text' id='e_tahun' name='e_tahun' required='required' class='form-control' placeholder='tahun' value='<?PHP echo $ptahun; ?>' Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Departemen <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="cb_dept" name="cb_dept" onchange="ShowDariDepartemen()">
                                            <?PHP
                                                $query = "select iddep, nama_dep from dbmaster.t_department WHERE aktif<>'N' ";
                                                if ($fjbtid=="08") $query .=" AND iddep='SLS01' ";
                                                elseif ($fjbtid=="20") $query .=" AND iddep IN ('SLS01', 'SLS02') ";
                                                else{
                                                    $query .=" AND iddep IN ('SLS01', 'SLS02', 'SLS03') ";
                                                }
                                                $query .=" ORDER BY nama_dep";
                                                
                                                $tampil = mysqli_query($cnmy, $query);
                                                $ketemu=mysqli_num_rows($tampil);
                                                
                                                if ((INT)$ketemu>1) echo "<option value='' selected>-- All --</option>";
                                                
                                                while ($row= mysqli_fetch_array($tampil)) {
                                                    $niddep=$row['iddep'];
                                                    $nnmdep=$row['nama_dep'];

                                                    echo "<option value='$niddep' >$nnmdep</option>";
                                                }
                                                
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                
                                
                            </div>
                        </div>
                    </div>
                                
                                
                </div>
                
            </div>
                
        </form>
        
        
        
    </div>
    
</div>