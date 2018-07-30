<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:108:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\public/../application/admin\view\promote\customertabs\add.html";i:1532573343;s:88:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\layout\default.html";i:1531976707;s:85:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\common\meta.html";i:1531976707;s:87:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\common\script.html";i:1531976707;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo $config['language']; ?>">
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>
    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !$config['fastadmin']['multiplenav']): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                
<form id="add-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" 
data-area='["600px","400px"]'>

<div class="form-group">
    <label class="control-label col-xs-12 col-sm-2"><?php echo __('导入模板下载'); ?>:</label>
    <div class="col-xs-12 col-sm-8">

        <a href="download" class="btn btn-success btn-embossed btn-download" title="<?php echo __('下载导入模板'); ?>">
            <i class="fa fa-download"></i> <?php echo __('下载导入模板'); ?></a>
        <!-- <a href="javascript:;" class="btn btn-danger btn-import <?php echo $auth->check('customer/customerresource/import')?'':'hide'; ?>" title="<?php echo __('Import'); ?>" id="btn-import-file" data-url="ajax/upload" data-mimetype="csv,xls,xlsx" data-multiple="false"><i class="fa fa-upload"></i> 导入EXCEL</a> -->
    </div>
</div>

<div class="form-group">
    <label class="control-label col-xs-12 col-sm-2"><?php echo __('客户信息导入'); ?>:</label>
    <div class="col-xs-12 col-sm-8">
        <div class="input-group">
            <input id="c-attachfile" data-rule="required" class="form-control" size="50" name="row[attachfile]" type="text" value="">
            <div class="input-group-addon no-border no-padding">
                <span><button type="button" id="plupload-attachfile" class="btn btn-danger plupload" data-input-id="c-attachfile" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                
            </div>
            <span class="msg-box n-right" for="c-attachfile"></span>
        </div>
        
    </div>
</div>

<div class="form-group layer-footer">
    <label class="control-label col-xs-12 col-sm-2"></label>
    <div class="col-xs-12 col-sm-8">
        <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
        <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
    </div>
</div>

<!-- <div class="form-group">
    <div class="col-xs-12 col-sm-8">
            <label for="file">客户信息导入:</label>
            <input type="file" id="file" name="file"/>
            
    </div>
    <button type="button" onclick="ajaxUploadFile()">确定</button>
</div> -->

<!-- <div class="report-file">
    <span>上传文件</span><input tabindex="3" size="3" name="report" class="file-prew" type="file" onchange="document.getElementById('textName').value=this.value">
</div>
<input type="text" id="textName" style="height: 28px;border:1px solid #f1f1f1" />
<button type="button" onclick="ajaxUploadFile()">确定</button> -->
</form>


<!-- <script>

function ajaxUploadFile() {
    var formData = new FormData();
    var xmlhttp;
    console.log(123);
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    }
    else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.open("POST","import",true);
    xmlhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    formData.append("file",document.getElementById("file").files[0]);
    xmlhttp.send(formData);
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4) {
            if (xmlhttp.status==200) {
                console.log("上传成功"+xmlhttp.responseText);
                window.parent.location.reload();
                parent.layer.closeAll('iframe');
            }else {
                console.log("上传失败"+xmlhttp.responseText);
            }
        }
    }
}

</script> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>
    </body>
</html>