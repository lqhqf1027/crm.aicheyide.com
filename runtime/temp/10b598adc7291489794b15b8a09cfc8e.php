<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:105:"D:\phpstudy\PHPTutorial\WWW\crm.aicheyide.com\public/../application/admin\view\order\salesorder\edit.html";i:1532688178;s:88:"D:\phpstudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\layout\default.html";i:1532573933;s:85:"D:\phpstudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\common\meta.html";i:1532573933;s:87:"D:\phpstudy\PHPTutorial\WWW\crm.aicheyide.com\application\admin\view\common\script.html";i:1532573933;}*/ ?>
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
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Plan_acar_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-plan_acar_name" data-rule="required" class="form-control" name="row[plan_acar_name]" type="text" value="<?php echo $row['plan_acar_name']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Sales_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-sales_id" data-rule="required" data-source="sales/index" class="form-control selectpage" name="row[sales_id]" type="text" value="<?php echo $row['sales_id']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Backoffice_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-backoffice_id" data-rule="required" data-source="backoffice/index" class="form-control selectpage" name="row[backoffice_id]" type="text" value="<?php echo $row['backoffice_id']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Control_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-control_id" data-rule="required" data-source="control/index" class="form-control selectpage" name="row[control_id]" type="text" value="<?php echo $row['control_id']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('New_car_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-new_car_id" data-rule="required" data-source="new/car/index" class="form-control selectpage" name="row[new_car_id]" type="text" value="<?php echo $row['new_car_id']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Order_no'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-order_no" data-rule="required" class="form-control" name="row[order_no]" type="text" value="<?php echo $row['order_no']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Username'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-username" data-rule="required" class="form-control" name="row[username]" type="text" value="<?php echo $row['username']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Phone'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-phone" data-rule="required" class="form-control" name="row[phone]" type="text" value="<?php echo $row['phone']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Id_card'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-id_card" data-rule="required" class="form-control" name="row[id_card]" type="text" value="<?php echo $row['id_card']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Genderdata'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <?php if(is_array($genderdataList) || $genderdataList instanceof \think\Collection || $genderdataList instanceof \think\Paginator): if( count($genderdataList)==0 ) : echo "" ;else: foreach($genderdataList as $key=>$vo): ?>
            <label for="row[genderdata]-<?php echo $key; ?>"><input id="row[genderdata]-<?php echo $key; ?>" name="row[genderdata]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['genderdata'])?$row['genderdata']:explode(',',$row['genderdata']))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label> 
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('City'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class='control-relative'><input id="c-city" data-rule="required" class="form-control" data-toggle="city-picker" name="row[city]" type="text" value="<?php echo $row['city']; ?>"></div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Detailed_address'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-detailed_address" data-rule="required" class="form-control" name="row[detailed_address]" type="text" value="<?php echo $row['detailed_address']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Emergency_contact_1'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-emergency_contact_1" data-rule="required" class="form-control" name="row[emergency_contact_1]" type="text" value="<?php echo $row['emergency_contact_1']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Emergency_contact_2'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-emergency_contact_2" data-rule="required" class="form-control" name="row[emergency_contact_2]" type="text" value="<?php echo $row['emergency_contact_2']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Family_members'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-family_members" data-rule="required" class="form-control" name="row[family_members]" type="text" value="<?php echo $row['family_members']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Customer_source'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                        
            <select  id="c-customer_source" data-rule="required" class="form-control selectpicker" name="row[customer_source]">
                <?php if(is_array($customerSourceList) || $customerSourceList instanceof \think\Collection || $customerSourceList instanceof \think\Paginator): if( count($customerSourceList)==0 ) : echo "" ;else: foreach($customerSourceList as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['customer_source'])?$row['customer_source']:explode(',',$row['customer_source']))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Turn_to_introduce_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-turn_to_introduce_name" class="form-control" name="row[turn_to_introduce_name]" type="text" value="<?php echo $row['turn_to_introduce_name']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Turn_to_introduce_phone'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-turn_to_introduce_phone" class="form-control" name="row[turn_to_introduce_phone]" type="text" value="<?php echo $row['turn_to_introduce_phone']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Turn_to_introduce_card'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-turn_to_introduce_card" class="form-control" name="row[turn_to_introduce_card]" type="text" value="<?php echo $row['turn_to_introduce_card']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Id_cardimages'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-id_cardimages" data-rule="required" class="form-control" size="50" name="row[id_cardimages]" type="text" value="<?php echo $row['id_cardimages']; ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-id_cardimages" class="btn btn-danger plupload" data-input-id="c-id_cardimages" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-id_cardimages"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-id_cardimages" class="btn btn-primary fachoose" data-input-id="c-id_cardimages" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-id_cardimages"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-id_cardimages"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Drivers_licenseimages'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-drivers_licenseimages" data-rule="required" class="form-control" size="50" name="row[drivers_licenseimages]" type="text" value="<?php echo $row['drivers_licenseimages']; ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-drivers_licenseimages" class="btn btn-danger plupload" data-input-id="c-drivers_licenseimages" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-drivers_licenseimages"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-drivers_licenseimages" class="btn btn-primary fachoose" data-input-id="c-drivers_licenseimages" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-drivers_licenseimages"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-drivers_licenseimages"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Residence_bookletimages'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-residence_bookletimages" data-rule="required" class="form-control" size="50" name="row[residence_bookletimages]" type="text" value="<?php echo $row['residence_bookletimages']; ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-residence_bookletimages" class="btn btn-danger plupload" data-input-id="c-residence_bookletimages" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-residence_bookletimages"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-residence_bookletimages" class="btn btn-primary fachoose" data-input-id="c-residence_bookletimages" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-residence_bookletimages"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-residence_bookletimages"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Housingimages'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-housingimages" data-rule="required" class="form-control" size="50" name="row[housingimages]" type="text" value="<?php echo $row['housingimages']; ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-housingimages" class="btn btn-danger plupload" data-input-id="c-housingimages" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-housingimages"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-housingimages" class="btn btn-primary fachoose" data-input-id="c-housingimages" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-housingimages"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-housingimages"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Bank_cardimages'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-bank_cardimages" data-rule="required" class="form-control" size="50" name="row[bank_cardimages]" type="text" value="<?php echo $row['bank_cardimages']; ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-bank_cardimages" class="btn btn-danger plupload" data-input-id="c-bank_cardimages" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-bank_cardimages"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-bank_cardimages" class="btn btn-primary fachoose" data-input-id="c-bank_cardimages" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-bank_cardimages"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-bank_cardimages"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Application_formimages'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-application_formimages" data-rule="required" class="form-control" size="50" name="row[application_formimages]" type="text" value="<?php echo $row['application_formimages']; ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-application_formimages" class="btn btn-danger plupload" data-input-id="c-application_formimages" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-application_formimages"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-application_formimages" class="btn btn-primary fachoose" data-input-id="c-application_formimages" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-application_formimages"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-application_formimages"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Call_listfiles'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-call_listfiles" data-rule="required" class="form-control" size="50" name="row[call_listfiles]" type="text" value="<?php echo $row['call_listfiles']; ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-call_listfiles" class="btn btn-danger plupload" data-input-id="c-call_listfiles" data-multiple="true"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-call_listfiles" class="btn btn-primary fachoose" data-input-id="c-call_listfiles" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-call_listfiles"></span>
            </div>
            
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Credit_reportimages'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-credit_reportimages" class="form-control" size="50" name="row[credit_reportimages]" type="text" value="<?php echo $row['credit_reportimages']; ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-credit_reportimages" class="btn btn-danger plupload" data-input-id="c-credit_reportimages" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-credit_reportimages"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-credit_reportimages" class="btn btn-primary fachoose" data-input-id="c-credit_reportimages" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-credit_reportimages"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-credit_reportimages"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Deposit_contractimages'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-deposit_contractimages" class="form-control" size="50" name="row[deposit_contractimages]" type="text" value="<?php echo $row['deposit_contractimages']; ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-deposit_contractimages" class="btn btn-danger plupload" data-input-id="c-deposit_contractimages" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-deposit_contractimages"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-deposit_contractimages" class="btn btn-primary fachoose" data-input-id="c-deposit_contractimages" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-deposit_contractimages"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-deposit_contractimages"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Deposit_receiptimages'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-deposit_receiptimages" class="form-control" size="50" name="row[deposit_receiptimages]" type="text" value="<?php echo $row['deposit_receiptimages']; ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-deposit_receiptimages" class="btn btn-danger plupload" data-input-id="c-deposit_receiptimages" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-deposit_receiptimages"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-deposit_receiptimages" class="btn btn-primary fachoose" data-input-id="c-deposit_receiptimages" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-deposit_receiptimages"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-deposit_receiptimages"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Guarantee_id_cardimages'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-guarantee_id_cardimages" class="form-control" size="50" name="row[guarantee_id_cardimages]" type="text" value="<?php echo $row['guarantee_id_cardimages']; ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-guarantee_id_cardimages" class="btn btn-danger plupload" data-input-id="c-guarantee_id_cardimages" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-guarantee_id_cardimages"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-guarantee_id_cardimages" class="btn btn-primary fachoose" data-input-id="c-guarantee_id_cardimages" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-guarantee_id_cardimages"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-guarantee_id_cardimages"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Guarantee_agreementimages'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-guarantee_agreementimages" class="form-control" size="50" name="row[guarantee_agreementimages]" type="text" value="<?php echo $row['guarantee_agreementimages']; ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-guarantee_agreementimages" class="btn btn-danger plupload" data-input-id="c-guarantee_agreementimages" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-guarantee_agreementimages"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-guarantee_agreementimages" class="btn btn-primary fachoose" data-input-id="c-guarantee_agreementimages" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-guarantee_agreementimages"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-guarantee_agreementimages"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Review_the_data'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <?php if(is_array($reviewTheDataList) || $reviewTheDataList instanceof \think\Collection || $reviewTheDataList instanceof \think\Paginator): if( count($reviewTheDataList)==0 ) : echo "" ;else: foreach($reviewTheDataList as $key=>$vo): ?>
            <label for="row[review_the_data]-<?php echo $key; ?>"><input id="row[review_the_data]-<?php echo $key; ?>" name="row[review_the_data]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['review_the_data'])?$row['review_the_data']:explode(',',$row['review_the_data']))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label> 
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Delivery_datetime'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-delivery_datetime" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[delivery_datetime]" type="text" value="<?php echo datetime($row['delivery_datetime']); ?>">
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