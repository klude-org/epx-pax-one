<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php if($v = o()->vars['html']['tab_title'] ?? null): ?><title><?=$v?></title><?php endif ?>
    <?php if($v = o()->vars['html']['tab_icon'] ?? null): ?><link rel="icon" href="<?=$v?>"><?php endif ?>
    <?php if($v = o()->vars['html']['base'] ?? null): ?><base href="<?=$v?>"><?php endif ?>
    <?php if($v = o()->vars['html']['canonical'] ?? null): ?><link rel="canonical" href="<?=$v?>"><?php endif ?>
    <?php foreach(o()->vars['html']['meta'] ?? [] as $k => $v): ?><meta name="<?=$k?>" content="<?=$v?>"><?=PHP_EOL; endforeach ?>
    
    <title>Setup</title>
    <link rel="icon" href="">
    <script>
        <?php if(\defined('_\CSRF')): ?>
        const X_CSRF = <?php echo json_encode(constant('_\CSRF')); ?>;
        <?php endif ?>
        if (typeof xui === 'undefined') {
            xui = {
                datasource: {}
            };
        }
        if (typeof xui.TRACE === 'undefined') {
            xui.TRACE = 6;
            xui.TRACE_1 = ((xui.TRACE ?? 0) >= 1);
            xui.TRACE_2 = ((xui.TRACE ?? 0) >= 2);
            xui.TRACE_3 = ((xui.TRACE ?? 0) >= 3);
            xui.TRACE_4 = ((xui.TRACE ?? 0) >= 4);
            xui.TRACE_5 = ((xui.TRACE ?? 0) >= 5);
            xui.TRACE_6 = ((xui.TRACE ?? 0) >= 6);
            xui.TRACE_7 = ((xui.TRACE ?? 0) >= 7);
            xui.TRACE_8 = ((xui.TRACE ?? 0) >= 8);
            xui.TRACE_9 = ((xui.TRACE ?? 0) >= 9);
            (xui.TRACE_1) && console.log({
                xui
            });
        }
        const CTLR_URL = '<?=\_\CTLR_URL ?? ''?>'
        const BASE_URL = '<?=\_\BASE_URL ?? ''?>'
        const SITE_URL = '<?=\_\SITE_URL ?? ''?>';
        const ROOT_URL = '<?=\_\ROOT_URL ?? ''?>';
    </script>
    
    <?=\_\view::plic('meta') ?>
    
    <?=\_\view::plic('head') ?>
    
    <?=\_\view::plic('head_plugins') ?>

    <?=\_\view::plic('style') ?>

    <?=\_\view::plic('script_head') ?>
</head>
<body>
    <?=$__INSET__?>
    <?=\_\view::plic('tail_plugins') ?>
    <?=\_\view::plic('script') ?>
</body>
</html>