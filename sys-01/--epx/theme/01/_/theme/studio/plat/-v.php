<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$_ENV['html']['browser']['title'] ?? null ?: 'Untitled'?></title>
    <link rel="icon" href="">
    <script>
        <?php if(\defined('_\CSRF')): ?>
        const X_CSRF = <?php echo json_encode(constant('_\CSRF')); ?>;
        <?php endif ?>
        
        if (typeof xui === 'undefined') {
            xui = {

                urls: {
                    root: `${window.location.protocol}//${window.location.host}`,
                    site: `${window.location.protocol}//${window.location.host}${window.location.pathname}`,
                    base: `${window.location.protocol}//${window.location.host}${window.location.pathname}`,
                },
                i: [],
                init(f = null) {
                    if (f) {
                        this.i.push(f);
                    }
                    return this;
                },
                _init_() {
                    this.i.forEach(function(f) {
                        f();
                    });
                },
                extend(a1, a2 = null) {
                    if (typeof a1 === 'string') {
                        if (typeof this[a1] === 'undefined') {
                            this[a1] = {};
                        }
                        if (a2) {
                            Object.assign(this[a1], a2);
                        }
                    } else if (a1 instanceof Object) {
                        Object.assign(this, a1);
                    }
                    return this;
                },
                event: {
                    _list: [],
                    add(name, delegate) {
                        if (this._list[name]) {
                            this._list[name].push(delegate);
                        } else {
                            this._list[name] = [delegate];
                        }
                    },
                    trigger(name, data) {
                        this._list[name]?.forEach(delegate => {
                            delegate(data);
                        });
                    }
                },
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
        
        window.onload = (event) => {
            xui._init_();
        };
    </script>
    
    <?=\_\view::plic('meta') ?>
    
    <?=\_\view::plic('head') ?>
    
    <style>
        /* ===== Global basics (safe) ===== */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
        }

        a {
            text-decoration: none;
        }

        /* ===== Scoped styles: only apply inside .xui-studio ===== */

        /* Theme tokens (Bootstrap-ish) */
        .xui-studio {
            --primary: #0d6efd;
            --primary-600: #0b5ed7;
            --primary-contrast: #ffffff;
            --focus-ring: rgba(13, 110, 253, .25);
            font-family: system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif;
            color: #111;
        }

        /* Flex utilities */
        .xui-studio .d-flex {
            display: flex;
        }

        .xui-studio .flex-column {
            flex-direction: column;
        }

        .xui-studio .flex-fill {
            flex: 1 1 auto;
        }

        /* requested */
        .xui-studio .flex-grow-1 {
            flex-grow: 1;
        }

        .xui-studio .flex-shrink-0 {
            flex-shrink: 0;
        }

        /* Spacing helpers (only those you use) */
        .xui-studio .mx-1 {
            margin-left: .25rem;
            margin-right: .25rem;
        }

        .xui-studio .w-100 {
            width: 100%;
        }

        /* Buttons (base + outline-primary + small) */
        .xui-studio .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .4em;
            padding: .375rem .75rem;
            font-size: .95rem;
            font-weight: 500;
            line-height: 1.25;
            border: 1px solid transparent;
            border-radius: .375rem;
            background: transparent;
            color: inherit;
            text-align: center;
            cursor: pointer;
            user-select: none;
            transition: background-color .15s ease, color .15s ease, border-color .15s ease, box-shadow .15s ease;
        }

        .xui-studio .btn:disabled,
        .xui-studio .btn[disabled] {
            opacity: .65;
            pointer-events: none;
        }

        .xui-studio .btn-sm {
            padding: .25rem .5rem;
            font-size: .875rem;
            border-radius: .25rem;
        }

        /* Outline Primary */
        .xui-studio .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
            background-color: transparent;
        }

        .xui-studio .btn-outline-primary:hover,
        .xui-studio .btn-outline-primary:active {
            color: var(--primary-contrast);
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .xui-studio .btn-outline-primary:focus-visible {
            outline: 0;
            box-shadow: 0 0 0 .2rem var(--focus-ring);
        }

        /* Optional helpers */
        .xui-studio .text-muted {
            color: #6c757d;
        }

        .xui-studio .hidden {
            display: none !important;
        }

        /* If your sidebar carries the class too, you can add sidebar-only rules safely: */
        .sidebar-left.xui-studio {
            /* your sidebar-specific styles here */
        }
    </style>
    
    <?=\_\view::plic('head_plugins') ?>
    <?=\_\view::plic('style') ?>

    <style>
        * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .xui-page {
            height: 100vh;
            display: flex;
        }

        .xui-sidebar-left {
            display: flex;
        }

        .xui-centerpane {
            flex-grow: 2;
            overflow: hidden;
        }

        .xui-sidebar-right {
            display: none;
        }
    </style>
    <style>
        .xui-sidebar-left {
            position: relative;
            flex-direction: column;
            background-color: #2b3c4f;
            color: #fff;
            padding: 5px 1px 5px;
            overflow-x: auto;
            overflow-y: auto;
        }

        .xui-sidebar-left .link {
            cursor: pointer;
            font-style: italic;
            color: #ccc;
        }

        .xui-sidebar-left .active {
            color: yellow;
            font-weight: bold;
        }

        .xui-sidebar-left .xui-sidenav-left {
            display: flex;
            flex-direction: column;
            overflow: auto;
            white-space: nowrap;
        }
    </style>
    <style>
        /* For WebKit browsers (Chrome, Edge, Safari) */
        .xui-sidenav-left::-webkit-scrollbar {
            width: 6px;
            /* vertical scrollbar width */
            height: 6px;
            /* horizontal scrollbar height */
        }

        .xui-sidenav-left::-webkit-scrollbar-track {
            background: transparent;
        }

        .xui-sidenav-left::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.3);
            /* light handle */
            border-radius: 3px;
        }

        /* For Firefox */
        .xui-sidenav-left {
            scrollbar-width: thin;
            /* slim */
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }

        .xui-sidenav-left {
            direction: rtl;
            /* flip scrollbar to the left */
            text-align: left;
            /* force text back to left alignment */
        }

        /* Prevent child elements from inheriting RTL */
        .xui-sidenav-left * {
            direction: ltr;
        }


        /* Keep the sidebar width stable when its scrollbar appears */
        .xui-sidenav-left {
            overflow: auto;
            /* or overflow-y: auto */
            scrollbar-gutter: stable;
            /* reserve space for the scrollbar */
        }

        /* If you’re placing the scrollbar on the LEFT using direction: rtl */
        .xui-sidenav-left {
            /* direction: rtl; */
            scrollbar-gutter: stable;
            /* reserves on the inline-start side (left in rtl) */
        }

        .xui-sidenav-left * {
            direction: ltr;
        }

        /* keep content normal */
    </style>

    <style>
        /* The draggable strip */
        .xui-sidebar-left .xui-sb-resizer {
            position: absolute;
            top: 0;
            right: 0;
            /* put on the right edge of the sidebar */
            width: 8px;
            /* comfortable target */
            height: 100%;
            cursor: col-resize;
            z-index: 10;
            /* sit above iframes in center pane */
            /* Optional visual hitline */
            background: transparent;
        }


        /* Use a CSS var for stable width from sessionStorage */
        .xui-sidebar-left {
            position: relative;
            flex: 0 0 var(--sb-w, 260px);
            width: var(--sb-w, 260px);
            min-width: 150px;
            max-width: 50vw;
            overflow: auto;
        }


        /* Hide just the sidebar until we apply persisted state */
        .xui-sidebar-left[data-prepaint="hide"] {
            visibility: hidden;
            /* prevents flicker */
        }

        /* collapsed tree hides its direct .tree-body */
        .xui-sidenav-left ul.tree li.collapsed>.tree-body {
            display: none;
        }

        /* Then add this tiny CSS rule (in your CSS block) to hide the sidebar as soon as it’s parsed (no flash), and we’ll remove it right after we apply the states: */
        html[data-prepaint-sidebar="1"] .xui-sidebar-left {
            visibility: hidden;
        }
    </style>

    <style>
        /* Hide children when collapsed */
        .xui-sidenav-left ul.tree li.collapsed>.tree-body {
            display: none;
        }

        /* Small caret button before the link */
        .xui-sidenav-left ul.tree li .twisty {
            display: inline-block;
            width: 0;
            height: 0;
            margin-right: 6px;
            border-top: 4px solid transparent;
            border-bottom: 4px solid transparent;
            border-left: 6px solid var(--tree-dir-color);
            vertical-align: middle;
            cursor: pointer;
        }

        /* Point down when expanded */
        .xui-sidenav-left ul.tree li[aria-expanded="true"]>.twisty {
            transform: rotate(90deg);
        }

        /* Optional: highlight on hover */
        .xui-sidenav-left ul.tree li .twisty:hover {
            filter: brightness(1.3);
        }
    </style>

    <script>
        /* Pre-paint width + hide sidebar */
        (function () {
            try {
                var w = sessionStorage.getItem('sb.w');
                if (w && /^\d+(\.\d+)?$/.test(w)) {
                    document.documentElement.style.setProperty('--sb-w', w + 'px');
                }
                document.documentElement.setAttribute('data-prepaint-sidebar', '1');
            } catch (e) { }
        })();
    </script>

    <?=\_\view::plic('script_head') ?>
    
</head>

<body translate="no">
    <div class="xui-page">
        <div class="xui-studio xui-sidebar-left" style="overflow-x:hidden">
            <div class="xui-sb-resizer" aria-hidden="true"></div>
            <div class="xui-sidenav-header">
                <a style="color:white" href="<?=\_\BASE_URL?>"><?=$_ENV['html']['page']['title'] ?? null ?: 'Untitled'?></a>
            </div>
            <div class="xui-sidenav-left xui-url_tree flex-fill">
                <?php 
                    //$sidenav->tree_head(['name' => 'setup', 'url' => \_\BASE_URL,]);
                    \_\nav\url_tree::_($_ENV['html']['url_tree']['panel'] ?? null)->render(function($v, $k, $i){
                        if($v){
                            ?><a class="link" href="#<?=$v?>" title="<?=$k?>&#013;<?=$v?>"><?=$k?></a><?php
                        } else {
                            ?><span><?=$k?></span><?php
                        }
                    }); 
                ?>
            </div>
            <div class="sidenav-footer">
                <a href="?--logout" class="btn btn-sm btn-outline-primary mx-1">Logout</a>
                <a href="?--switch" class="btn btn-sm btn-outline-primary mx-1">Switch</a>
            </div>
        </div>
        <style>#id-hashframe-iframe { width: 100%; height: 100%; border: 1px solid red; display: block; }</style>
        <div class="xui-centerpane">
            <!-- Spinner overlay -->
            <div id="centerpane-spinner" role="status" aria-live="polite" aria-label="Loading content">
                <div>
                <div class="spinner" aria-hidden="true"></div>
                <div class="spinner-label">Loading…</div>
                </div>
            </div>
            <iframe id="id-hashframe-iframe" src="" frameborder="0"></iframe>
        </div>
        <div class="xui-studio xui-sidebar-right">
            hidden and for future use
        </div>
        <div id="dragShield" aria-hidden="true"></div>
    </div>

    <?=\_\view::plic('tail_plugins') ?>
    
    <style>
        /* Drag fixes */
        body.dragging { user-select: none; cursor: col-resize; }
        body.dragging #mainFrame { pointer-events: none; }
        #dragShield {
            position: fixed;
            inset: 0;
            display: none;
            z-index: 2147483647;
            cursor: col-resize;
        }
        body.dragging #dragShield { display: block; }        
    </style>
    
    <style>
        /* Spinner overlay */
        .xui-centerpane { position: relative; } /* <-- ensure overlay positions correctly */

        #centerpane-spinner {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            background: rgba(255,255,255,0.65);
            backdrop-filter: blur(1px);
            z-index: 10;
            transition: opacity .15s ease;
            pointer-events: auto;
        }
        #centerpane-spinner.hidden {
            opacity: 0;
            pointer-events: none;
        }

        /* The spinner graphic */
        .spinner {
            width: 40px; height: 40px;
            border-radius: 50%;
            border: 4px solid #c7d2fe;      /* light ring */
            border-top-color: #4f46e5;       /* animated arc */
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Respect reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .spinner { animation: none; border-top-color: #4f46e5; }
        }

        /* Optional: small status text for screen readers/visual hint */
        .spinner-label { 
            font-size: 12px; 
            color: #374151; 
            margin-top: 8px; 
            text-align: center; 
        }
    </style>

    <script>
        
        xui.extend('reload_spinner', {
            spinnerFallbackTimer : null,
            init(){
                this.spinner = document.getElementById('centerpane-spinner');
            },
            start() {
                clearTimeout(this.spinnerFallbackTimer);
                this.spinner.classList.remove('hidden');
                // Fallback: never leave spinner stuck forever (e.g., network error)
                this.spinnerFallbackTimer = setTimeout(() => this.spinner.classList.add('hidden'), 15000);                
                return this;
            },
            stop() {
                clearTimeout(this.spinnerFallbackTimer);
                this.spinner.classList.add('hidden');
            },
        });
        
        xui.init(() => xui.reload_spinner.init());
    </script>

    <script>
        window.addEventListener("load", (event) => {
            document.querySelectorAll('.link').forEach(v => {
                var href = v.getAttribute('href');
                if (
                    href == window.location.href
                    || href + '/' == window.location.href
                ) {
                    v.classList.add('active');
                } else {
                    v.classList.remove('active');
                }
            });
        });
    </script>

    <script>
        (function () {
            // Helpers
            function getPath(li) {
                // Create a stable path like "0/2/1" using index among :scope > li
                var path = [];
                var node = li;
                while (node && node.tagName === 'LI') {
                    var parentUL = node.parentElement;
                    if (!parentUL) break;
                    var idx = Array.prototype.indexOf.call(parentUL.children, node);
                    path.unshift(idx);
                    // climb to LI parent (the LI that wraps this UL, if any)
                    var parentLI = parentUL.closest('li');
                    node = parentLI;
                }
                return path.join('/');
            }

            function readState() {
                try {
                    return JSON.parse(sessionStorage.getItem('tree.state') || '{}');
                } catch (e) {
                    return {};
                }
            }

            function writeState(state) {
                try { sessionStorage.setItem('tree.state', JSON.stringify(state)); } catch (e) { }
            }

            function saveWidth(widthPx) {
                try { sessionStorage.setItem('sb.w', String(widthPx)); } catch (e) { }
            }

            // 1) Apply collapse state from sessionStorage
            var state = readState();
            document.querySelectorAll('.xui-sidenav-left ul.tree li').forEach(function (li) {
                var body = li.querySelector(':scope > .tree-body');
                if (!body) return;

                // Ensure twisty exists (re-using your previous twisty feature if you added it)
                var twisty = li.querySelector(':scope > .twisty');
                if (!twisty) {
                    twisty = document.createElement('span');
                    twisty.className = 'twisty';
                    twisty.setAttribute('role', 'button');
                    twisty.setAttribute('tabindex', '0');
                    li.insertBefore(twisty, li.firstChild);
                }

                // Default expanded
                if (!li.hasAttribute('aria-expanded')) li.setAttribute('aria-expanded', 'true');

                // Restore persisted collapsed/expanded
                var key = getPath(li);
                if (state[key] === 'collapsed') {
                    li.classList.add('collapsed');
                    li.setAttribute('aria-expanded', 'false');
                } else {
                    li.classList.remove('collapsed');
                    li.setAttribute('aria-expanded', 'true');
                }

                // Wire toggle + persist
                function toggle(e) {
                    if (e) { e.preventDefault(); e.stopPropagation(); }
                    var isCollapsed = li.classList.toggle('collapsed');
                    li.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
                    var s = readState();
                    s[key] = isCollapsed ? 'collapsed' : 'expanded';
                    writeState(s);
                }
                twisty.onclick = toggle;
                twisty.onkeydown = function (e) {
                    if (e.key === 'Enter' || e.key === ' ') toggle(e);
                };

                // Optional: Alt/Meta-click on link toggles
                var a = li.querySelector(':scope > a.link');
                if (a && !a._toggleBound) {
                    a.addEventListener('click', function (e) {
                        if (e.altKey || e.metaKey) { toggle(e); }
                    });
                    a._toggleBound = true;
                }
            });

            // 2) Persist sidebar width using ResizeObserver (debounced)
            var sidebar = document.querySelector('.xui-sidebar-left');
            if (sidebar) {
                // On first parse, if inline width exists (from CSS var), keep it
                // Observe user resize to save new width
                var rafId = null;
                var ro = new ResizeObserver(function () {
                    if (rafId) cancelAnimationFrame(rafId);
                    rafId = requestAnimationFrame(function () {
                        var rect = sidebar.getBoundingClientRect();
                        // Save only if width looks sane
                        if (rect.width > 80 && rect.width < window.innerWidth * 0.9) {
                            saveWidth(rect.width);
                        }
                    });
                });
                ro.observe(sidebar);
            }

            // 3) Unhide the sidebar now that state is applied (FOUC-free)
            document.documentElement.removeAttribute('data-prepaint-sidebar');
        })();
    </script>

    <!-- After the sidebar exists in the DOM -->
    <script>
        (function () {
            var sb = document.querySelector('.xui-sidebar-left');
            var grip = sb && sb.querySelector('.xui-sb-resizer');
            if (!sb || !grip) return;

            var minW = 150, maxW = Math.max(300, window.innerWidth * 0.5);
            var dragging = false, startX = 0, startW = 0;

            function setW(px) {
                px = Math.min(Math.max(px, minW), maxW);
                document.documentElement.style.setProperty('--sb-w', px + 'px');
                try { sessionStorage.setItem('sb.w', String(px)); } catch (e) { }
            }

            grip.addEventListener('mousedown', function (e) {
                e.preventDefault();
                dragging = true;
                // If sidebar is RTL, dragging direction is inverted relative to screen
                var rtl = getComputedStyle(sb).direction === 'rtl';
                startX = e.clientX;
                startW = sb.getBoundingClientRect().width;
                document.body.style.userSelect = 'none';
                document.body.style.cursor = 'col-resize';
                document.body.classList.add('dragging');
                
                function onMove(ev) {
                    if (!dragging) return;
                    var dx = ev.clientX - startX;
                    // If RTL, moving mouse right should DECREASE width (handle is on the left)
                    var newW = rtl ? (startW - dx) : (startW + dx);
                    setW(newW);
                }

                function onUp() {
                    dragging = false;
                    document.removeEventListener('mousemove', onMove);
                    document.removeEventListener('mouseup', onUp);
                    document.body.style.userSelect = '';
                    document.body.style.cursor = '';
                    document.body.classList.remove('dragging');
                }

                document.addEventListener('mousemove', onMove);
                document.addEventListener('mouseup', onUp);
            });

            // (Optional) update max width on resize
            window.addEventListener('resize', function () {
                maxW = Math.max(300, window.innerWidth * 0.5);
                var cur = sb.getBoundingClientRect().width;
                if (cur > maxW) setW(maxW);
            });
        })();
    </script>


    <script>
        (function () {
            var snav = document.querySelector('.xui-sidenav-left');
            if (!snav) return;

            // --- Keys in sessionStorage
            var K_TOP = 'snav.scrollTop';
            var K_INL = 'snav.scrollInlineStart'; // logical "left" for LTR, "start edge" for RTL

            // --- RTL-safe helpers (normalize cross-browser differences)
            function isRTL(el) {
                return getComputedStyle(el).direction === 'rtl';
            }

            // Return the logical "inline-start" scroll offset:
            //  LTR  -> same as scrollLeft
            //  RTL  -> 0 = fully at start (visual left), increases as you scroll toward the visual right.
            function getInlineStartScroll(el) {
                var ltr = !isRTL(el);
                if (ltr) return el.scrollLeft;

                // RTL normalization across browsers:
                //  - Firefox uses negative scrollLeft (0 at start, -max at end)
                //  - Chrome/WebKit use positive scrollLeft (max at start, 0 at end)
                var max = el.scrollWidth - el.clientWidth;
                var sl = el.scrollLeft;
                if (sl < 0) return -sl;        // Firefox
                return max - sl;               // Chrome/WebKit
            }

            // Set the logical inline-start scroll offset
            function setInlineStartScroll(el, start) {
                var ltr = !isRTL(el);
                if (ltr) { el.scrollLeft = start; return; }

                var max = el.scrollWidth - el.clientWidth;
                // Try Firefox-style first (negative)
                el.scrollLeft = -start;
                if (el.scrollLeft !== -start) {
                    // Fallback to Chrome/WebKit style
                    el.scrollLeft = max - start;
                }
            }

            // --- Restore scroll (run after layout settles to avoid jump)
            function restoreScroll() {
                try {
                    var t = Number(sessionStorage.getItem(K_TOP) || 0);
                    var i = Number(sessionStorage.getItem(K_INL) || 0);

                    // Apply after one frame to ensure sizes are final (esp. after width/resize)
                    requestAnimationFrame(function () {
                        // Guard: clamp within current ranges
                        var maxTop = Math.max(0, snav.scrollHeight - snav.clientHeight);
                        var maxInl = Math.max(0, snav.scrollWidth - snav.clientWidth);
                        var top = Math.min(Math.max(0, t), maxTop);
                        var inl = Math.min(Math.max(0, i), maxInl);

                        snav.scrollTop = top;
                        setInlineStartScroll(snav, inl);

                        // Unhide once applied
                        document.documentElement.removeAttribute('data-prepaint-sidebar');
                    });
                } catch (e) {
                    document.documentElement.removeAttribute('data-prepaint-sidebar');
                }
            }

            // --- Persist on scroll (debounced)
            var ticking = false;
            function onScroll() {
                if (ticking) return;
                ticking = true;
                requestAnimationFrame(function () {
                    ticking = false;
                    try {
                        sessionStorage.setItem(K_TOP, String(snav.scrollTop));
                        sessionStorage.setItem(K_INL, String(getInlineStartScroll(snav)));
                    } catch (e) { }
                });
            }

            // Persist also on resize (in case content width/height changes)
            function onResize() {
                try {
                    sessionStorage.setItem(K_TOP, String(snav.scrollTop));
                    sessionStorage.setItem(K_INL, String(getInlineStartScroll(snav)));
                } catch (e) { }
            }

            snav.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', onResize);

            // Also save when tab/page is about to go away
            window.addEventListener('pagehide', onResize);
            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'hidden') onResize();
            });

            // Kick it off
            restoreScroll();
        })();
    </script>
    
    <script>
        xui.hashframe = {
            reloading: false,
            hash: window.location.hash,
            url: window.location,
            init() {
                this.iframe = {
                    get el(){ return document.getElementById('id-hashframe-iframe'); },
                    get url(){ return this.el.contentWindow.location.href; },
                };
            },
            before_loading() {
                xui.reload_spinner?.start();
                // credits: https://stackoverflow.com/a/1309769/10753162
                var frameDoc = this.iframe.el.contentDocument || this.iframe.el.contentWindow.document;
                frameDoc.removeChild(frameDoc.documentElement);
            },
            load_window_hash() {
                this.hash = window.location.hash;
                this.url = this.hash.substring(1);
                (xui.TRACE_2) && console.log({
                    action: 'loading window hash',
                    'xui.hashframe': this,
                });
                this.before_loading();
                this.iframe.el.src = this.url;
                this.nav?.set_active();
                this.readjust_hash();
            },
            readjust_hash() {
                // credits: https://stackoverflow.com/questions/1397329/how-to-remove-the-hash-from-window-location-url-with-javascript-without-page-r/5298684#5298684
                if (window.history.pushState) {
                    window.history.pushState('', '/', window.location.pathname)
                } else {
                    window.location.hash = '';
                }
            },
            reload_with_hash(hash) {
                // credits: https://stackoverflow.com/a/10612657
                // credits: https://stackoverflow.com/a/7647123
                this.reloading = true;
                window.location.hash = hash;
                window.location.reload();
            },
            log_status(){
                console.log({
                    c: {
                        w_location: window.location,
                        w_location_hash: window.location.hash,
                        w_frame_src: this.iframe.el.src,
                        w_frame_url: this.iframe.url,
                    }
                });
            },
            
        };
        
        xui.hashframe.nav = {
            set_active() {
                (xui.TRACE_2) && console.log('setting_active_nav');
                (xui.TRACE_1) && xui.hashframe.log_status();
                var hashURL = '#'+xui.hashframe.iframe.url;
                document.querySelectorAll('.xui-sidenav-left .link').forEach(v => {
                    if (v.getAttribute('href') == hashURL) {
                        console.log({active: hashURL, vHref: v.getAttribute('href')});
                        v.classList.add('active');
                    } else {
                        console.log({in_active: hashURL, vHref: v.getAttribute('href')});
                        v.classList.remove('active');
                    }
                });
            },
        };
        
        xui.hashframe.init();
        
        xui.hashframe.iframe.el.onload = function() {
            var url = document.getElementById('id-hashframe-iframe').src;
            //document.querySelector('.hashframe-url').innerHTML = ;
            sessionStorage.setItem('last_location-' + window.location.href, url ?? '');
            xui.reload_spinner?.stop();
            xui.hashframe.nav?.set_active();
        };
        
        window.addEventListener('hashchange', function() {
            if (xui.hashframe.reloading) {
                return;
            }
            xui.hashframe.load_window_hash();
        }, false);
    
        window.addEventListener("load", (event) => {
            var url = sessionStorage.getItem('last_location-' + window.location.href);
            window.location.hash = '#' + url;
        });
        
        xui.extend('reload_spinner', {
            on_click(e) {
                (xui.TRACE_5) && console.log('reload_spinner-on_click');
                xui.hashframe.before_loading();
                if (e.button == 0 && e.ctrlKey) {
                    (xui.TRACE_2) && console.log('left-cntrl-click');
                    xui.event.trigger('update_request', {
                        type: 'left-cntrl-click',
                        data: e
                    });
                } else if (e.button == 2 && e.ctrlKey) {
                    (xui.TRACE_2) && console.log('right-cntrl-click');
                    xui.event.trigger('update_request', {
                        type: 'right-cntrl-click',
                        data: e
                    });
                } else {
                    (xui.TRACE_2) && console.log('normal-click');
                    xui.event.trigger('update_request', {
                        type: 'left-click',
                        data: e
                    });
                }
                document.getElementById('id-hashframe-iframe').contentWindow.location.reload();
            },
        });        
        
    </script>
    
    <?=\_\view::plic('script') ?>
    
</body>

</html>