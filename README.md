easy_themes
===========

How to add your extension to easy_themes
---

```
<?php

$GLOBALS['TL_EASY_THEMES_MODULES']['my_module'] = array
(
    'title'         => 'My Module',
    'label'         => 'My Module',
    'href'          => 'main.php?do=my_module&theme=%s',
    'href_fragment' => 'table=tl_additional_source',
    'icon'          => 'system/modules/my_module/html/my_module_icon.png',
    'appendRT'      => true
);
```

Description
---

 * title:			optional, otherwise easy_themes uses $GLOBALS['TL_LANG']['tl_theme']['...'][1]
 * label:			optional, otherwise easy_themes uses $GLOBALS['TL_LANG']['tl_theme']['...'][0]
 * href:			optional, alternative to href_fragment, overwrites href_fragment!
 * href_fragment:	alternative to href, will be added to the url like this: main.php?do=themes&id=\<theme id\>
 * icon:			optional, if not given, easy_themes will try to load an icon using Controller::generateImage('my_module.gif', ...)
 * appendRT:		boolean, optional, if set to true, easy_themes will append the request token (&rt=\<REQUEST_TOKEN\>)