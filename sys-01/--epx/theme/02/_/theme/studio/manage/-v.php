<?php 
    $o = (object)[];
    $o->sidebar = (function(){ 
        if($dob = \_\dob\json($_REQUEST->_['panel'].'/sidebar',['assoc' => false])){
            return $dob;
        } else {
            return (object)['nav' => []];
        }
    })();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- <meta charset="UTF-8"> -->
    <!--<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">-->
    <!-- <meta name="description" content=""> -->
    <!-- <meta name="author" content="Brian Pinto"> -->
    <!-- <meta name="generator" content="Hugo 0.84.0"> -->
    <!-- <base href="https://website.com/path/"> -->
    <!-- <link rel="canonical" href="https://website.com/"> -->
     
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php if($v = o()->vars['html']['tab_title'] ?? null): ?><title><?=$v?></title><?php endif ?>
    <?php if($v = o()->vars['html']['tab_icon'] ?? null): ?><link rel="icon" href="<?=$v?>"><?php endif ?>
    <?php if($v = o()->vars['html']['base'] ?? null): ?><base href="<?=$v?>"><?php endif ?>
    <?php if($v = o()->vars['html']['canonical'] ?? null): ?><link rel="canonical" href="<?=$v?>"><?php endif ?>
    <?php foreach(o()->vars['html']['meta'] ?? [] as $k => $v): ?><meta name="<?=$k?>" content="<?=$v?>"><?=PHP_EOL; endforeach ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }
        
        
        /*############################################################################*/
        /* scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #eee;
        }

        ::-webkit-scrollbar-thumb {
            background: #9f9f9f;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #5f5f5f;
        } 
        
        .sidebar-scroll {
            direction: rtl;             /* Scrollbar goes left */
            /* overflow-y: auto; */
            max-height: 100vh;          /* or as needed */
            
            position: relative;
            /* direction: rtl; */
            /* overflow-y: auto; */
            max-height: 100vh;
            padding-left: 5px; /* Reserve space for scrollbar */
            box-sizing: content-box;            
            
        }

        .sidebar-scroll > * {
            direction: ltr;             /* Content stays left-to-right */
        }        

        .xui-page {
            height: 100%;
            overflow: hidden;
            background-color: white;
        }
        /*############################################################################*/        

        .xui-container {
            display: flex;
            height: 100%;
            width: 100%;
        }

        .xui-sidebar {
            /* width: 150px; */
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            position: relative;
            
            padding-left: 5px;
            overflow: hidden;
        }

        .xui-sidebar.collapsed {
            display: none;
        }

        .xui-main {
            flex: 1;
            overflow: hidden;
            position: relative;
        }

        .xui-toggle-tab {
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            width: 14px;
            height: 50px;
            background-color: #6c757d;
            cursor: pointer;
            z-index: 1000;
            opacity: 0.5;
            transition: opacity 0.3s;
            clip-path: polygon(0 0, 100% 15%, 100% 85%, 0% 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .xui-toggle-tab:hover {
            opacity: 1;
        }

        .xui-toggle-tab i {
            color: white;
            font-size: 12px;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .xui-loader-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            display: none;
        }
        
        .xui-sidebar .xui-nav {
            width: 100%;
            min-width: 170px;
            height : calc(100vh - 80px);
            flex-wrap: nowrap;
            overflow: auto;
        }
        
        .active-highlight {
            font-weight:bold;
        }
        
        
        .nav-chevron.rotate {
            transform: rotate(90deg);
            transition: transform 0.3s ease;
        }        
        
        .nav-chevron {
            display: inline-block;
            font-size: 0.75rem; /* or 1rem, adjust as needed */
            font-weight: bold;
            transition: transform 0.3s ease;
        }        
        
        
    </style>
</head>

<body>
    <div class="xui-toggle-tab" id="sidebarToggle" onclick="toggleSidebar()">
        <i id="toggleIcon" class="bi bi-chevron-left"></i>
    </div>
    <div class="xui-container">
        <div class="xui-sidebar p-1" id="leftSidebar">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <a href="/" class="xui-nav-header d-flex align-items-center mb-md-0 me-md-auto text-decoration-none">
                    <span class="icon"><?=$o->sidebar->header->icon??''?></span> 
                    <span class="ms-1 fs-5 d-none d-sm-inline"><?=$o->sidebar->header->label??''?></span>
                </a>
            </div>
            <?php 
                ($nav_render__fn = function ($nav, $is_root = true) use(&$nav_render__fn){ ?>
                    <?php if($is_root): ?>
                    <ul class="xui-nav nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start sidebar-scroll pe-1" id="<?=$menu="sidebarMenu"?>">
                    <?php else: ?>
                    <ul class="show nav flex-column ms-2">
                    <?php endif ?>
                    <?php foreach($nav as $k => $v): ?>
                        <?php if(\is_scalar($v)): ?>
                            <li><span class="section-label"><?=$v?></span></li>
                        <?php elseif(isset($v->inner)): ?>
                        <li class="nav-item w-100">
                            <div class="w-100 d-flex align-items-center justify-content-between">
                                <a href="javascript:void(0)" class="nav-link w-100 d-flex align-items-center justify-content-between text-decoration-none" data-path="<?=$v->url?>" onclick="navigate('<?=$v->url?>')">
                                    <span class="d-flex align-items-center">
                                        <span class="icon"><?=$v->icon??''?></span> 
                                        <span class="ms-1 d-none d-sm-inline"><?=$v->label??''?></span> 
                                    </span>
                                </a>
                                <a href="#<?=$submenu=uniqid('menu-')?>" data-bs-toggle="collapse" class="nav-link text-decoration-none" data-path="<?=$v->url?>">
                                    <i class="bi bi-chevron-right nav-chevron"></i>
                                </a>
                            </div>
                            <div class="collapse" id="<?=$submenu?>" data-bs-parent="#<?=$menu?>">
                                <?php ($nav_render__fn)($v->inner, false) ?>
                            </div>
                        </li>
                        <?php else: ?>
                        <li class="nav-item w-100">
                            <a href="javascript:void(0)" class="nav-link w-100 d-flex align-items-center justify-content-between text-decoration-none" data-path="<?=$v->url?>" onclick="navigate('<?=$v->url?>')">
                                <span class="d-flex align-items-center">
                                    <span class="icon"><?=$v->icon??''?></span> 
                                    <span class="ms-1 d-none d-sm-inline"><?=$v->label??''?></span> 
                                </span>
                            </a>
                        </li>
                        <?php endif ?>
                    <?php endforeach ?>
                    </ul><?php 
                })($o->sidebar->nav);
                
                ($footer_rended__fn = function ($footer) use(&$footer_rended__fn){ if(!$footer){ return; }?>
                    <div class="dropdown dropup pt-2">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?=$footer->icon?>" alt="hugenerd" width="30" height="30" class="rounded-circle">
                            <span class="flex-fill d-none d-sm-inline mx-1" style="max-width:110px; overflow:hidden"><?=$footer->label?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                            <li><a class="dropdown-item" href="<?='?--auth=facet'?>">Switch Portal</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php foreach($footer->nav as $k => $v): ?>
                                <?php if($v == '-'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                <?php elseif(\is_scalar($v)): ?>
                                    <li><span class="section-label"><?=$v?></span></li>
                                <?php elseif(\is_object($v)): ?>
                                    <li><a class="dropdown-item" href="javascript:void(0)" data-path="<?=$v->url?>" onclick="navigate('<?=$v->url?>')"><?=$v->label??''?></a></li>
                                <?php else: ?>
                                <?php endif ?>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php })($o->sidebar->footer ?? null);
            ?>
        </div>
        <div class="xui-main">
            <div class="xui-loader-overlay" id="frameLoader">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <iframe id="contentFrame"></iframe>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('leftSidebar');
        const toggleTab = document.getElementById('sidebarToggle');
        const toggleIcon = document.getElementById('toggleIcon');
        const contentFrame = document.getElementById('contentFrame');
        const sidebarMenu = document.getElementById('sidebarMenu');
        const frameLoader = document.getElementById('frameLoader');

        function toggleSidebar() {
            sidebar.classList.toggle('collapsed');
            toggleIcon.classList.toggle('bi-chevron-left');
            toggleIcon.classList.toggle('bi-chevron-right');
        }

        const CTLR_URL = '<?=\_\CTLR_URL?>'
        const BASE_URL = '<?=\_\BASE_URL?>'
        const SITE_URL = '<?=\_\SITE_URL?>';
        const ROOT_URL = '<?=\_\ROOT_URL?>';
        console.log({CTLR_URL,BASE_URL,SITE_URL,ROOT_URL});
        function navigateWithoutReferrer(url) {
            const a = document.createElement('a');
            a.href = url;
            a.rel = 'noreferrer';
            a.target = '_self'; // same as window.location
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
        
        function navigate(page) {
            if (page.startsWith('--')) {
                // Use ROOT_URL and remove the '--' prefix
                contentFrame.src = SITE_URL + '/' + page.slice(2);
            } else if (
                page.startsWith('?')
            ){
                contentFrame.src = CTLR_URL + page;
            } else if (
                page.startsWith('//') ||
                page.startsWith('https://') ||
                page.startsWith('http://')
            ) {
                // Navigate the main window
                navigateWithoutReferrer(page);
            } else if (
                page.startsWith('/')
            ){
                // Navigate the main window
                navigateWithoutReferrer(page);
            } else if (
                page.startsWith('.')
            ){
                // Navigate the main window
                navigateWithoutReferrer(CTLR_URL + page.substring(1));
            } else {
                // Default behavior with baseUrl
                const baseUrl = window.location.origin + window.location.pathname;
                contentFrame.src = baseUrl.replace(/\/$/, '') + '/' + page;
            }
        }

        function highlightActiveTab(url) {
            const links = sidebarMenu.querySelectorAll('a[data-path]');
            links.forEach(link => {
                const path = link.getAttribute('data-path');
                var path_url = CTLR_URL+'/'+path;
                var frame_url = ROOT_URL+url;
                console.log({frame_url, path_url, path});
                if (frame_url == path_url) {
                    //link.classList.add('active');
                    link.classList.add('active-highlight');
                } else if (frame_url.startsWith(path_url)) {
                    link.classList.add('active-highlight');
                } else {
                    link.classList.remove('active');
                    link.classList.remove('active-highlight');
                }
            });
        }

        contentFrame.addEventListener('load', () => {
            frameLoader.style.display = 'none';
            try {
                const url = contentFrame.contentWindow.location.pathname;
                highlightActiveTab(url);
            } catch (e) {
                console.log(e);
                // ignore cross-origin access errors
            }
        });

        window.addEventListener('message', (event) => {
            if (event.data === 'start-loading') {
                console.log(event);
                frameLoader.style.display = 'flex';
            } else if (event.data === 'stop-loading') {
                console.log(event);
                frameLoader.style.display = 'none';
            }
        });

        // contentFrame.addEventListener('unload', () => {
        //   frameLoader.style.display = 'flex';
        // });

        // contentFrame.addEventListener('beforeunload', () => {
        //   frameLoader.style.display = 'flex';
        // });


        contentFrame.addEventListener('loadstart', () => {
            frameLoader.style.display = 'flex';
        });

        const observer = new MutationObserver(() => {
            frameLoader.style.display = 'flex';
        });

        observer.observe(contentFrame, { attributes: true, attributeFilter: ['src'] });
    </script>
    
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const collapses = document.querySelectorAll('[data-bs-toggle="collapse"]');
            collapses.forEach(toggle => {
                const chevron = toggle.querySelector('.bi-chevron-right');
                const targetId = toggle.getAttribute('href');
                const collapseEl = document.querySelector(targetId);

                if (!collapseEl || !chevron) return;

                collapseEl.addEventListener('show.bs.collapse', function () {
                    chevron.classList.add('rotate');
                });

                collapseEl.addEventListener('hide.bs.collapse', function () {
                    chevron.classList.remove('rotate');
                });
            });
        });
    </script>
    
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdown = document.querySelector('.dropdown');

            if (!dropdown) return;

            let timeout;

            dropdown.addEventListener('mouseleave', () => {
                // Use timeout to prevent flicker if user moves between toggle and menu
                timeout = setTimeout(() => {
                const dropdownToggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
                const bsDropdown = bootstrap.Dropdown.getInstance(dropdownToggle);
                if (bsDropdown) {
                    bsDropdown.hide();
                }
                }, 300); // Delay helps avoid accidental closure
            });

            dropdown.addEventListener('mouseenter', () => {
                // Clear any pending close
                clearTimeout(timeout);
            });
        });
    </script>
    
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>console.log({server:<?=\json_encode($GLOBALS['_TRACE'])?>})</script>
</body>

</html>