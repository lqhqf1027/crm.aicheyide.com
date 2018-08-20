<?php

return array (
  'autoload' => false,
  'hooks' => 
  array (
    'admin_login_init' => 
    array (
      0 => 'loginbg',
    ),
    'upload_config_init' => 
    array (
      0 => 'upyun',
    ),
  ),
  'route' => 
  array (
    '/example$' => 'example/index/index',
    '/example/d/[:name]' => 'example/demo/index',
    '/example/d1/[:name]' => 'example/demo/demo1',
    '/example/d2/[:name]' => 'example/demo/demo2',
  ),
);