<?php

return array(

    'view_filter'=>array('Behavior\ShowRuntimeBehavior'),
	//显示运行时信息
    'app_init'=>array(
        'Common\Behavior\InitHookBehavior',
    ),

);
