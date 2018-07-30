<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:108:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\public/../application/admin\view\newcars\newnventory\edit.html";i:1532673164;s:88:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\layout\default.html";i:1531976707;s:85:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\common\meta.html";i:1531976707;s:87:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\common\script.html";i:1531976707;}*/ ?>
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
                                <form id="edit-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Models_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-models_id" data-rule="required" data-source="planmanagement/models/index" class="form-control selectpage" name="row[models_id]" type="text" value="<?php echo $row['models_id']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Carnumber'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-carnumber" class="form-control" name="row[carnumber]" type="number" value="<?php echo $row['carnumber']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Reservecar'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-reservecar" class="form-control" name="row[reservecar]" type="number" value="<?php echo $row['reservecar']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Carprocess'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                        
            <!-- <select  id="c-carprocess" class="form-control selectpicker" name="row[carprocess]">
                <?php if(is_array($carprocessList) || $carprocessList instanceof \think\Collection || $carprocessList instanceof \think\Paginator): if( count($carprocessList)==0 ) : echo "" ;else: foreach($carprocessList as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['carprocess'])?$row['carprocess']:explode(',',$row['carprocess']))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select> -->
            <div class="radio">
                <?php if(is_array($carprocessList) || $carprocessList instanceof \think\Collection || $carprocessList instanceof \think\Paginator): if( count($carprocessList)==0 ) : echo "" ;else: foreach($carprocessList as $key=>$vo): ?>
                <label for="row[carprocess]-<?php echo $key; ?>"><input id="row[carprocess]-<?php echo $key; ?>" name="row[carprocess]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"1"))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label> 
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Pledge'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                        
            <!-- <select  id="c-pledge" class="form-control selectpicker" name="row[pledge]">
                <?php if(is_array($pledgeList) || $pledgeList instanceof \think\Collection || $pledgeList instanceof \think\Paginator): if( count($pledgeList)==0 ) : echo "" ;else: foreach($pledgeList as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['pledge'])?$row['pledge']:explode(',',$row['pledge']))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select> -->
            <div class="radio">
                <?php if(is_array($pledgeList) || $pledgeList instanceof \think\Collection || $pledgeList instanceof \think\Paginator): if( count($pledgeList)==0 ) : echo "" ;else: foreach($pledgeList as $key=>$vo): ?>
                <label for="row[pledge]-<?php echo $key; ?>"><input id="row[pledge]-<?php echo $key; ?>" name="row[pledge]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"1"))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label> 
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Licensenumber'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-licensenumber" data-rule="required" class="form-control" name="row[licensenumber]" type="text" value="<?php echo $row['licensenumber']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Presentationcondition'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-presentationcondition" class="form-control " rows="5" name="row[presentationcondition]" cols="50"><?php echo $row['presentationcondition']; ?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Note'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-note" class="form-control" name="row[note]" type="text" value="<?php echo $row['note']; ?>">
        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
        </div>
    </div>
</form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>
    </body>
</html>