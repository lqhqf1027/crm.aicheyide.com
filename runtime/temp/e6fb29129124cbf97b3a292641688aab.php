<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:110:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\public/../application/admin\view\promote\customertabs\index.html";i:1532945408;s:88:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\layout\default.html";i:1531976707;s:85:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\common\meta.html";i:1531976707;s:87:"D:\phpStudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\common\script.html";i:1531976707;}*/ ?>
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
        <div class="panel-lead"><em>客户池，导入客户资源，分配给内勤，同时可以展示销售反馈回来的信息</em></div>
        <div>
            <p style="color:coral;font-weight: bold">注意事项：</p>
            <ol>
                <li style="color:rgb(250, 10, 10);font-weight: bold">导入客户信息时，请先下载导入模板 </li>
                <!-- <li style="color:rgb(250, 10, 10);font-weight: bold">批量分配时，请先选择下方数据 </li> -->
            </ol>
        </div>
        <ul class="nav nav-tabs" >
            <li class="active text-center"><a href="#new_customer" data-toggle="tab">新客户 &nbsp;<span class="pull-right-container"><small class="badge pull-right bg-red" id="badge_new_customer"></small></span></a></li>
            <li><a href="#new_allocation" data-toggle="tab" class="text-center" > 已分配 &nbsp;<span class="pull-right-container"><small class="badge pull-right bg-red" id="badge_new_allocation"><?php echo $total1; ?></small></span></a></li>
            <li><a href="#new_feedback" data-toggle="tab"  class="text-center" > 已反馈 &nbsp;<span class="pull-right-container"><small class="badge pull-right bg-red"><?php echo $total2; ?></small></span></a></li>
        </ul>
    </div>
    <div class="panel-body">
        <div id="myTabContent" class="tab-content">


            <!--新客户-->
            
            <div class="tab-pane fade active in" id="new_customer">
                <div id="toolbar1" class="toolbar">
                   
                    <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                    <a href="javascript:;" class="btn btn-success btn-add <?php echo $auth->check('customer/customerresource/add')?'':'hide'; ?>" title="<?php echo __('Add'); ?>" ><i class="fa fa-plus"></i> <?php echo __('Add'); ?></a>
                    <!-- <a href="javascript:;" class="btn btn-success btn-edit btn-disabled disabled <?php echo $auth->check('promote/customertabs/edit')?'':'hide'; ?>" title="<?php echo __('Import'); ?>" ><i class="fa fa-upload"></i> <?php echo __('Import'); ?></a> -->
                    <!-- <a href="javascript:;" class="btn btn-danger btn-del btn-disabled disabled <?php echo $auth->check('customer/customerresource/del')?'':'hide'; ?>" title="<?php echo __('Delete'); ?>" ><i class="fa fa-trash"></i> <?php echo __('Delete'); ?></a> -->
                    <!-- <a href="javascript:;" class="btn btn-danger import" title="<?php echo __('Import'); ?>"><i class="fa fa-upload"></i> <?php echo __('Import'); ?></a> -->
                    <a href="customertabs/download" class="btn btn-embossed btn-primary btn-download" title="<?php echo __('下载导入模板'); ?>">
                        <i class="fa fa-download"></i> <?php echo __('下载导入模板'); ?></a>
                    <a href="javascript:;" class="btn btn-danger btn-import <?php echo $auth->check('customer/customerresource/import')?'':'hide'; ?>" title="<?php echo __('导入客户信息'); ?>" id="btn-import-file" data-url="ajax/upload" data-mimetype="csv,xls,xlsx" data-multiple="false"><i class="fa fa-upload"></i> 导入客户信息</a>
                    <a class="btn btn-info btn-disabled disabled btn-selected" href="javascript:;" data-params="distribution"><i class="fa fa-eye"></i> 批量分配</a>
                    <!-- 导出客户信息 -->
                    <!-- <a href="javascript:;" class="btn btn-success btn-export <?php echo $auth->check('promote/customertabs/export')?'':'hide'; ?>" title="<?php echo __('导出客户信息'); ?>" id="btn-export-file"><i class="fa fa-download"></i> <?php echo __('导出客户信息'); ?></a> -->

                </div>
                <table id="newCustomer" class="table table-striped table-bordered table-hover table-nowrap"
                        data-operate-edit="<?php echo $auth->check('customer/customerresource/edit'); ?>" 
                        data-operate-del="<?php echo $auth->check('customer/customerresource/del'); ?>" 
                        width="100%">
                </table>
            </div>


            <!--已分配-->
            
            <div class="tab-pane fade" id="new_allocation">
                <div id="toolbar2" class="toolbar">
                        <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                        <!-- <a href="javascript:;" class="btn btn-success btn-add <?php echo $auth->check('plan/planusedcar/add')?'':'hide'; ?>" title="<?php echo __('Add'); ?>" ><i class="fa fa-plus"></i> <?php echo __('Add'); ?></a>
                        <a href="javascript:;" class="btn btn-success btn-edit btn-disabled disabled <?php echo $auth->check('plan/planusedcar/edit')?'':'hide'; ?>" title="<?php echo __('Edit'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('Edit'); ?></a>
                        <a href="javascript:;" class="btn btn-danger btn-del btn-disabled disabled <?php echo $auth->check('plan/planusedcar/del')?'':'hide'; ?>" title="<?php echo __('Delete'); ?>" ><i class="fa fa-trash"></i> <?php echo __('Delete'); ?></a>
                        <a href="javascript:;" class="btn btn-danger btn-import <?php echo $auth->check('plan/planusedcar/import')?'':'hide'; ?>" title="<?php echo __('Import'); ?>" id="btn-import-file" data-url="ajax/upload" data-mimetype="csv,xls,xlsx" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('Import'); ?></a>
                        


                        <div class="dropdown btn-group <?php echo $auth->check('plan/planusedcar/multi')?'':'hide'; ?>">
                            <a class="btn btn-primary btn-more dropdown-toggle btn-disabled disabled" data-toggle="dropdown"><i class="fa fa-cog"></i> <?php echo __('More'); ?></a>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a class="btn btn-link btn-multi btn-disabled disabled" href="javascript:;" data-params="ismenu=1"><i class="fa fa-eye"></i> 批量上架</a></li>
                            <li><a class="btn btn-link btn-multi btn-disabled disabled" href="javascript:;" data-params="ismenu=0"><i class="fa fa-eye-slash"></i> 批量下架</a></li>
                            </ul>
                        </div> -->
                        
                </div>
                <table id="newAllocation" class="table table-striped table-bordered table-hover table-nowrap"
                        data-operate-edit="<?php echo $auth->check('customer/customerresource/edit'); ?>" 
                        data-operate-del="<?php echo $auth->check('customer/customerresource/del'); ?>"
                        width="100%">
                </table>

            </div>


            <!--已反馈-->

            <div class="tab-pane fade" id="new_feedback">
                    <div id="toolbar3" class="toolbar">
                            <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                            <!-- <a href="javascript:;" class="btn btn-success btn-add <?php echo $auth->check('plan/planusedcar/add')?'':'hide'; ?>" title="<?php echo __('Add'); ?>" ><i class="fa fa-plus"></i> <?php echo __('Add'); ?></a>
                            <a href="javascript:;" class="btn btn-success btn-edit btn-disabled disabled <?php echo $auth->check('plan/planusedcar/edit')?'':'hide'; ?>" title="<?php echo __('Edit'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('Edit'); ?></a>
                            <a href="javascript:;" class="btn btn-danger btn-del btn-disabled disabled <?php echo $auth->check('plan/planusedcar/del')?'':'hide'; ?>" title="<?php echo __('Delete'); ?>" ><i class="fa fa-trash"></i> <?php echo __('Delete'); ?></a>
                            <a href="javascript:;" class="btn btn-danger btn-import <?php echo $auth->check('plan/planusedcar/import')?'':'hide'; ?>" title="<?php echo __('Import'); ?>" id="btn-import-file" data-url="ajax/upload" data-mimetype="csv,xls,xlsx" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('Import'); ?></a>
    
                            <div class="dropdown btn-group <?php echo $auth->check('plan/planusedcar/multi')?'':'hide'; ?>">
                                <a class="btn btn-primary btn-more dropdown-toggle btn-disabled disabled" data-toggle="dropdown"><i class="fa fa-cog"></i> <?php echo __('More'); ?></a>
                                <ul class="dropdown-menu text-left" role="menu">
                                    <li><a class="btn btn-link btn-multi btn-disabled disabled" href="javascript:;" data-params="ismenu=1"><i class="fa fa-eye"></i> 批量上架</a></li>
                                <li><a class="btn btn-link btn-multi btn-disabled disabled" href="javascript:;" data-params="ismenu=0"><i class="fa fa-eye-slash"></i> 批量下架</a></li>
                                </ul>
                            </div> -->
                    </div>
                    <table id="newFeedback" class="table table-striped table-bordered table-hover table-nowrap"
                        data-operate-edit="<?php echo $auth->check('customer/customerresource/edit'); ?>" 
                        data-operate-del="<?php echo $auth->check('customer/customerresource/del'); ?>"
                       width="100%">
                    </table>
                    
                </div>
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