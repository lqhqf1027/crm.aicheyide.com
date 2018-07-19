<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:86:"D:\wamp64\www\crm.aicheyide.com\public/../application/admin\view\backoffice\index.html";i:1531968085;s:74:"D:\wamp64\www\crm.aicheyide.com\application\admin\view\layout\default.html";i:1531717379;s:71:"D:\wamp64\www\crm.aicheyide.com\application\admin\view\common\meta.html";i:1531717379;s:73:"D:\wamp64\www\crm.aicheyide.com\application\admin\view\common\script.html";i:1531717379;}*/ ?>
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
                                <div class="panel panel-default panel-intro">
    <div class="panel-heading">
        <div class="panel-lead"><em>内勤管理，查看新客户，已分配客户，分配客户资源完成后推送消息给销售</em></div>
        <ul class="nav nav-tabs" >
            <li class="active text-center" style="width: 150px;"><a href="#new_customer" data-toggle="tab" style="color:coral;font-weight: bold"><i class="fa fa-flag-o"></i> 新客户</a></li>
            <li><a href="#second" data-toggle="tab" style="width: 150px;color:coral;font-weight: bold" class="text-center" ><i class="fa fa-flag-o"></i> 已分配客户</a></li>
        </ul>
    </div>
    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <!--新客户-->

            <div class="tab-pane fade active in" id="new_customer">
                <div id="toolbar1" class="toolbar">

                    <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                    <!--<a href="javascript:;" class="btn btn-success btn-add <?php echo $auth->check('customer/customerresource/add')?'':'hide'; ?>" title="<?php echo __('Add'); ?>" ><i class="fa fa-plus"></i> <?php echo __('Add'); ?></a>-->
                    <!--<a href="javascript:;" class="btn btn-success btn-edit btn-disabled disabled <?php echo $auth->check('customer/customerresource/edit')?'':'hide'; ?>" title="<?php echo __('Edit'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('Edit'); ?></a>-->
                    <!--<a href="javascript:;" class="btn btn-danger btn-del btn-disabled disabled <?php echo $auth->check('customer/customerresource/del')?'':'hide'; ?>" title="<?php echo __('Delete'); ?>" ><i class="fa fa-trash"></i> <?php echo __('Delete'); ?></a>-->
                    <!--<a href="javascript:;" class="btn btn-danger btn-import <?php echo $auth->check('customer/customerresource/import')?'':'hide'; ?>" title="<?php echo __('Import'); ?>" id="btn-import-file" data-url="ajax/upload" data-mimetype="csv,xls,xlsx" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('Import'); ?></a>-->
                    <!--<a class="btn btn-info btn-multi btn-disabled disabled" href="javascript:;" ><i class="fa fa-eye"></i> <?php echo __('Distribution'); ?></a>-->

                </div>
                <!-- <table id="table1" class="table table-striped table-bordered table-hover" width="100%">
                </table> -->
                <!-- <table id="table1" class="table table-striped table-bordered table-hover table-nowrap"
                           data-operate-edit="<?php echo $auth->check('plan/planacar/edit'); ?>"
                           data-operate-del="<?php echo $auth->check('plan/planacar/del'); ?>"
                           width="100%">
                </table> -->
                <table id="newCustomer" class="table table-striped table-bordered table-hover table-nowrap"
                       data-operate-edit="<?php echo $auth->check('customer/customerresource/edit'); ?>"
                       data-operate-del="<?php echo $auth->check('customer/customerresource/del'); ?>"
                       width="100%">
                </table>
            </div>
            <!--&lt;!&ndash;二手车&ndash;&gt;-->

            <!--<div class="tab-pane fade" id="second">-->
                <!--<div id="toolbar2" class="toolbar">-->
                    <!--<a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>-->
                    <!--<a href="javascript:;" class="btn btn-success btn-add <?php echo $auth->check('plan/planusedcar/add')?'':'hide'; ?>" title="<?php echo __('Add'); ?>" ><i class="fa fa-plus"></i> <?php echo __('Add'); ?></a>-->
                    <!--<a href="javascript:;" class="btn btn-success btn-edit btn-disabled disabled <?php echo $auth->check('plan/planusedcar/edit')?'':'hide'; ?>" title="<?php echo __('Edit'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('Edit'); ?></a>-->
                    <!--<a href="javascript:;" class="btn btn-danger btn-del btn-disabled disabled <?php echo $auth->check('plan/planusedcar/del')?'':'hide'; ?>" title="<?php echo __('Delete'); ?>" ><i class="fa fa-trash"></i> <?php echo __('Delete'); ?></a>-->
                    <!--<a href="javascript:;" class="btn btn-danger btn-import <?php echo $auth->check('plan/planusedcar/import')?'':'hide'; ?>" title="<?php echo __('Import'); ?>" id="btn-import-file" data-url="ajax/upload" data-mimetype="csv,xls,xlsx" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('Import'); ?></a>-->

                    <!--<div class="dropdown btn-group <?php echo $auth->check('plan/planusedcar/multi')?'':'hide'; ?>">-->
                        <!--<a class="btn btn-primary btn-more dropdown-toggle btn-disabled disabled" data-toggle="dropdown"><i class="fa fa-cog"></i> <?php echo __('More'); ?></a>-->
                        <!--<ul class="dropdown-menu text-left" role="menu">-->
                            <!--&lt;!&ndash; <li><a class="btn btn-link btn-multi btn-disabled disabled" href="javascript:;" data-params="status=normal"><i class="fa fa-eye"></i> <?php echo __('Set to normal'); ?></a></li>-->
                            <!--<li><a class="btn btn-link btn-multi btn-disabled disabled" href="javascript:;" data-params="status=hidden"><i class="fa fa-eye-slash"></i> <?php echo __('Set to hidden'); ?></a></li> &ndash;&gt;-->
                        <!--</ul>-->
                    <!--</div>-->
                <!--</div>-->
                <!--<table id="table2" class="table table-striped table-bordered table-hover table-nowrap"-->
                       <!--data-operate-edit="<?php echo $auth->check('plan/planfull/edit'); ?>"-->
                       <!--data-operate-del="<?php echo $auth->check('plan/planfull/del'); ?>"-->
                       <!--width="100%">-->
                <!--</table>-->
                <!--</table>-->
            <!--</div>-->

        </div>
    </div>
</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>
    </body>
</html>