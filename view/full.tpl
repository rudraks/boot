<html>
<head>
    {foreach $METAS as $meta}
        <meta name="{$meta@key}" content="{$meta}">
    {/foreach}

    <title>{$TITLE}</title>

    {foreach $header->css as $link}
        <link name={$link@key} rel="stylesheet" type="text/css" href="{$link.link}"/>
    {/foreach}

    <script type="text/javascript">
        var CONTEXT_PATH = '{$smarty.const.CONTEXT_PATH}';
        var RX_MODE_DEBUG = !!('{$smarty.const.RX_MODE_DEBUG}');
        var RELOAD_VERSION = ('{$smarty.const.RELOAD_VERSION}');
                {foreach $header->const as $const}var {$const@key} =
        '{$const}';
        {/foreach}
    </script>
</head>
<body>


</body>


</html>
