<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:122:"D:\phpstudy\PHPTutorial\WWW\crm.aicheyide.com\public/../application/admin\view\salesmanagement\customerlisttabs\index.html";i:1532944389;s:88:"D:\phpstudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\layout\default.html";i:1532573933;s:85:"D:\phpstudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\common\meta.html";i:1532573933;s:87:"D:\phpstudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\common\script.html";i:1532573933;}*/ ?>
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
        <div class="panel-lead"><em>新客户&待联系客户&有意向客户&暂无意向客户&待成交客户&已成交客户&放弃客户&跟进时间过期客户</em></div>
        <ul class="nav nav-tabs" >
            <li class="active text-center" style=""><a href="#new_customer" data-toggle="tab" style="font-weight: bold">新客户&nbsp;<span class="pull-right-container"><small class="badge pull-right bg-red" id="badge_new_customer"><?php echo $newCustomTotal; ?></small></span></a></li>
            <li><a href="#relation" data-toggle="tab" style="font-weight: bold" class="text-center" >待联系&nbsp;<span class="pull-right-container"><small class="badge pull-right bg-red" id="badge_relation"><?php echo $relationTotal; ?></small></span></a></li>
            <li><a href="#intention" data-toggle="tab" style="color:#18bc9c;font-weight: bold" class="text-center">有意向&nbsp;<span class="pull-right-container"><small class="badge pull-right bg-red" id="badge_intention"><?php echo $intentionTotal; ?></small></span></a></li>
            <li><a href="#nointention" data-toggle="tab" style="font-weight: bold" class="text-center">暂无意向&nbsp;<span class="pull-right-container"><small class="badge pull-right bg-red" id="badge_no_intention"><?php echo $nointentionTotal; ?></small></span></a></li>
            <li><a href="#giveup" data-toggle="tab" style="font-weight: bold" class="text-center">已放弃&nbsp;<span class="pull-right-container"><small class="badge pull-right bg-red" id="badge_give_up"><?php echo $giveupTotal; ?></small></span></a></li>
            <li><a href="#overdue" data-toggle="tab" style="color:#e74c3c;font-size:15px;font-weight: bold" class="text-center">跟进时间过期客户&nbsp;<span class="pull-right-container" id="badge_overdue"><small class="badge pull-right bg-red"><?php echo $overdueTotal; ?></small></span></a></li>
            <!--<li><a href="#deal" data-toggle="tab" style="font-weight: bold" class="text-center">待成交&nbsp;<span class="pull-right-container"><small class="badge pull-right bg-red"><?php echo $giveupTotal; ?></small></span></a></li>-->
            <!--<li><a href="#nodeal" data-toggle="tab" style="font-weight: bold" class="text-center">已成交&nbsp;<span class="pull-right-container"><small class="badge pull-right bg-red"><?php echo $giveupTotal; ?></small></span></a></li>-->
        </ul>
    </div>
    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <!--新客户-->

            <div class="tab-pane fade active in" id="new_customer">
                <div id="toolbar1" class="toolbar">

                    <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                    <a href="javascript:;" class="btn btn-success btn-add <?php echo $auth->check('customer/customerresource/add')?'':'hide'; ?>" title="<?php echo __('Add'); ?>" ><i class="fa fa-plus"></i> <?php echo __('Add'); ?></a>
                    <a href="javascript:;" class="btn btn-info btn-edit btn-disabled disabled <?php echo $auth->check('customer/customerresource/edit')?'':'hide'; ?>" title="<?php echo __('Edit'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('Batchfeedback'); ?></a>
                    <a class="btn btn-danger   btn-disabled disabled btn-selected" href="javascript:;" data-params="batch"><i class="fa fa-eye"></i>批量放弃</a>
                    <!--<a href="javascript:;" class="btn btn-danger btn-del btn-disabled disabled <?php echo $auth->check('customer/customerresource/del')?'':'hide'; ?>" title="<?php echo __('Delete'); ?>" ><i class="fa fa-trash"></i> <?php echo __('Delete'); ?></a>-->

                </div>

                <table id="newCustomer" class="table table-striped table-bordered table-hover table-nowrap"
                       data-operate-edit="<?php echo $auth->check('customer/customerresource/edit'); ?>"
                       data-operate-del="<?php echo $auth->check('customer/customerresource/del'); ?>"
                       width="100%">
                </table>
            </div>


            <!--待联系用户-->

            <div class="tab-pane fade active" id="relation">
                <div id="toolbar2" class="toolbar">

                    <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                    <a href="javascript:;" class="btn btn-info btn-edit btn-disabled disabled <?php echo $auth->check('customer/customerresource/edit')?'':'hide'; ?>" title="<?php echo __('Edit'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('Batchfeedback'); ?></a>
                    <a class="btn btn-danger   btn-disabled disabled btn-selected" href="javascript:;" data-params="batch"><i class="fa fa-eye"></i>批量放弃</a>

                </div>

                <table id="relations" class="table table-striped table-bordered table-hover table-nowrap"
                       data-operate-edit="<?php echo $auth->check('customer/customerresource/edit'); ?>"
                       data-operate-del="<?php echo $auth->check('customer/customerresource/del'); ?>"
                       width="100%">
                </table>
            </div>


            <!--有意向用户-->

            <div class="tab-pane fade active" id="intention">
                <div id="toolbar3" class="toolbar">

                    <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                    <a href="javascript:;" class="btn btn-info btn-edit btn-disabled disabled <?php echo $auth->check('customer/customerresource/edit')?'':'hide'; ?>" title="<?php echo __('Edit'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('Batchfeedback'); ?></a>
                    <a class="btn btn-danger   btn-disabled disabled btn-selected" href="javascript:;" data-params="batch"><i class="fa fa-eye"></i>批量放弃</a>

                </div>

                <table id="intentions" class="table table-striped table-bordered table-hover table-nowrap"
                       data-operate-edit="<?php echo $auth->check('customer/customerresource/edit'); ?>"
                       data-operate-del="<?php echo $auth->check('customer/customerresource/del'); ?>"
                       width="100%">
                </table>
            </div>

            <!--暂无意向用户-->

            <div class="tab-pane fade active" id="nointention">
                <div id="toolbar4" class="toolbar">

                    <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                    <a href="javascript:;" class="btn btn-info btn-edit btn-disabled disabled <?php echo $auth->check('customer/customerresource/edit')?'':'hide'; ?>" title="<?php echo __('Edit'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('Batchfeedback'); ?></a>
                    <a class="btn btn-danger   btn-disabled disabled btn-selected" href="javascript:;" data-params="batch"><i class="fa fa-eye"></i>批量放弃</a>

                </div>

                <table id="nointentions" class="table table-striped table-bordered table-hover table-nowrap"
                       data-operate-edit="<?php echo $auth->check('customer/customerresource/edit'); ?>"
                       data-operate-del="<?php echo $auth->check('customer/customerresource/del'); ?>"
                       width="100%">
                </table>
            </div>


            <!--放弃用户-->

            <div class="tab-pane fade active" id="giveup">
                <div id="toolbar5" class="toolbar">

                    <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>

                </div>

                <table id="giveups" class="table table-striped table-bordered table-hover table-nowrap"
                       data-operate-edit="<?php echo $auth->check('customer/customerresource/edit'); ?>"
                       data-operate-del="<?php echo $auth->check('customer/customerresource/del'); ?>"
                       width="100%">
                </table>
            </div>

            <!--跟进时间过期用户-->

            <div class="tab-pane fade active" id="overdue">
                <div id="toolbar6" class="toolbar">

                    <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                    <a href="javascript:;" class="btn btn-info btn-edit btn-disabled disabled <?php echo $auth->check('customer/customerresource/edit')?'':'hide'; ?>" title="<?php echo __('Edit'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('Batchfeedback'); ?></a>
                    <a class="btn btn-danger   btn-disabled disabled btn-selected" href="javascript:;" data-params="batch"><i class="fa fa-eye"></i>批量放弃</a>

                </div>

                <table id="overdues" class="table table-striped table-bordered table-hover table-nowrap"
                       data-operate-edit="<?php echo $auth->check('customer/customerresource/edit'); ?>"
                       data-operate-del="<?php echo $auth->check('customer/customerresource/del'); ?>"
                       width="100%">
                </table>
            </div>

            <!-待成交用户-->

            <div class="tab-pane fade active" id="deal">
                <div id="toolbar7" class="toolbar">

                    <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                    <a href="javascript:;" class="btn btn-info btn-edit btn-disabled disabled <?php echo $auth->check('customer/customerresource/edit')?'':'hide'; ?>" title="<?php echo __('Edit'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('Batchfeedback'); ?></a>
                    <a class="btn btn-danger   btn-disabled disabled btn-selected" href="javascript:;" data-params="batch"><i class="fa fa-eye"></i>批量放弃</a>

                </div>

                <table id="deals" class="table table-striped table-bordered table-hover table-nowrap"
                       data-operate-edit="<?php echo $auth->check('customer/customerresource/edit'); ?>"
                       data-operate-del="<?php echo $auth->check('customer/customerresource/del'); ?>"
                       width="100%">
                </table>
            </div>

            <!--已成交用户-->

            <div class="tab-pane fade active" id="nodeal">
                <div id="toolbar8" class="toolbar">

                    <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                    <a href="javascript:;" class="btn btn-info btn-edit btn-disabled disabled <?php echo $auth->check('customer/customerresource/edit')?'':'hide'; ?>" title="<?php echo __('Edit'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('Batchfeedback'); ?></a>
                    <a class="btn btn-danger   btn-disabled disabled btn-selected" href="javascript:;" data-params="batch"><i class="fa fa-eye"></i>批量放弃</a>

                </div>

                <table id="nodeals" class="table table-striped table-bordered table-hover table-nowrap"
                       data-operate-edit="<?php echo $auth->check('customer/customerresource/edit'); ?>"
                       data-operate-del="<?php echo $auth->check('customer/customerresource/del'); ?>"
                       width="100%">
                </table>
            </div>
        </div>
    </div>
</div>
<?php 

 ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>
    </body>
</html>