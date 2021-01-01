<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<style>
    .form-group, .input-group, .control-label {
        margin-bottom:3px;
    }
    .control-label {
        font-size:12px;
    }
    input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
</style>

<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                    <div id='c-data'>
                        <?PHP include "viewdatainput2.php"; ?>
                    </div>
                    
                </div>
            </div>
            
            
        </form>
        
    </div>
    <!--end row-->
</div>


    