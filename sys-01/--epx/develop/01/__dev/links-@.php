<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Controller List | <?=\_\SITE_URL?></title>
    
    <style>
        /* ===== Reset / Reboot (Bootstrap-like) ===== */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        /* ===== Blanket Dark Theme ===== */
        html, body {
            background-color: #000;   /* completely black page background */
            color: #e0e0e0;
        }

        /* Links */
        a {
            color: #82aaff;
        }
        a:hover {
            color: #b0c9ff;
        }

        /* Structural elements — remove background unless you want gray blocks */
        div, section, article, header, footer, nav, aside {
            background-color: transparent;  /* let black show through */
            padding: 0.5rem;
            border-radius: 4px;
        }

        /* Form elements */
        input, textarea, select, button {
            background-color: #111;   /* slightly lighter than black for contrast */
            color: #e0e0e0;
            border: 1px solid #333;
            border-radius: 4px;
            padding: 0.4rem 0.6rem;
        }
        input:focus, textarea:focus, select:focus, button:hover {
            border-color: #82aaff;
            outline: none;
        }

        /* Tables */
        table {
            background-color: transparent;  /* keep the full black bg */
        }
        th, td {
            border: 1px solid #333;
            padding: 0.5rem;
        }
        th {
            background-color: #111;  /* contrast header row */
        }
        tr:nth-child(even) {
            background-color: #0a0a0a; /* subtle row striping */
        }

    </style>  

    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
        }
        
        .url-item { margin-bottom: 0.5rem; }
        .url-link { text-decoration: none; margin:0; padding:0}
        .url-icon { margin-left: 0.4rem; font-size: 0.9rem; }
        .xui-url_tree{ white-space:nowrap; }
    </style>

    <script>
        let sandboxWin = null;
        function openSandbox(url) {
            if (!sandboxWin || sandboxWin.closed) {
                sandboxWin = window.open(url, 'sandboxWindow');
            } else {
                sandboxWin.location.href = url;
                sandboxWin.focus();
            }
        }
    </script>
</head>
<body>
    <svg xmlns="http://www.w3.org/2000/svg" style="display:none">
        <symbol id="icon-external" viewBox="0 0 16 16" fill="currentColor">
            <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5"/>
            <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z"/>
        </symbol>
    </svg>    
    <svg xmlns="http://www.w3.org/2000/svg" style="display:none">
        <symbol id="icon-login" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h8"/>
            <path d="M11 8l4 4-4 4"/>
            <path d="M19 5h2M21 5v14M21 19h-2"/> 
        </symbol>
    </svg>    
    <div >
        <h3><?=\_\SITE_URL?></h3>
        <div class="xui-url_tree">
            <?php 
            \_\nav\url_tree::_('*')->render(function($url, $path, $i){
                if($url){
                    ?>
                        <strong><?=$path?></strong>
                        <!-- External link icon -->
                        <a class="url-link" href="<?php echo htmlspecialchars($url); ?>" target="_blank" rel="noopener" class="url-icon text-secondary" title="Open in new tab">
                            <svg class="icon" width="12" height="12" aria-hidden="true"><use href="#icon-external"></use></svg>
                        </a>
                        <a class="url-link" href="<?=$url?>" title="<?=$path?>&#013;<?=$url?>">
                            <svg width="12" height="12" aria-hidden="true"><use href="#icon-login"/></svg>
                        </a>
                        <!-- Main link: reuses fixed window -->
                        <a class="url-link" href="#" onclick="openSandbox('<?php echo htmlspecialchars($url, ENT_QUOTES); ?>'); return false;">
                            <?=$url?>
                        </a>
                    <?php
                } else {
                    ?><span><?=$path?></span><?php    
                }
            });
            ?>
        </div>
    </div>
    
</body>
</html>



