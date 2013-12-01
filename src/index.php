<?php

require 'modules/core/core1.lib.php';
require 'core.conf.php';
LoadIBC1Lib('common', 'framework');
require GetSysResPath('TestPage.class.php', 'modules/pages');

$p = new TestPage();
$p->Route(array(
    'box' => array('Welcome', NULL),
    'modules' => array(
        'theme' => array(
            'box' => array('ThemeList', NULL),
            'functions' => array(
                'select' => array('SelectTheme', NULL)
            )
        ),
        'language' => array(
            'box' => array('LangList', NULL),
            'functions' => array(
                'select' => array('SelectLang', NULL)
            )
        ),
        'configuration' => array(
            'box' => array('ConfigList', NULL)
        ),
        'configuration/editor' => array(
            'box' => array('ConfigEditor', NULL),
            'functions' => array(
                'save' => array('SaveConfig', NULL),
                'delete' => array('DeleteConfig', NULL)
            )
        ),
        'cache' => array(
            'boxes' => array(
                array('CachedBox', NULL),
                array('CachingController', NULL)
            ),
            'functions' => array(
                'clear' => array('ClearCachedData', NULL)
            )
        ),
        'cache/versioning' => array(
            'boxes' => array(
                array('CachedBox', array(
                        'mode' => 'versioning'
                )),
                array('CachingController', array(
                        'mode' => 'versioning'
                ))
            )
        ),
        'cache/timing' => array(
            'boxes' => array(
                array('CachedBox', array(
                        'mode' => 'timing'
                )),
                array('CachingController', array(
                        'mode' => 'timing'
                ))
            )
        )
    )
));
$p->Show();