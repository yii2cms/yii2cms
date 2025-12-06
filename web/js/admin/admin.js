$(document).ready(function () {
    // 移动端侧边栏切换
    $(".sidebar-toggle").click(function () {
        $(".sidebar").toggleClass("show");
    });

    // 点击外部区域关闭侧边栏（仅移动端）
    $(document).click(function (e) {
        // 只在移动端处理
        if ($(window).width() <= 768) {
            // 如果点击的不是侧边栏或侧边栏切换按钮，关闭移动端侧边栏
            if (!$(e.target).closest(".sidebar, .sidebar-toggle").length) {
                $(".sidebar").removeClass("show");
            }
        }
    });

    // 下拉菜单功能
    $('.sidebar .dropdown-toggle').click(function (e) {
        e.preventDefault();

        var $this = $(this);
        var $dropdownMenu = $this.next('.dropdown-menu');

        // 切换当前菜单
        $dropdownMenu.toggleClass('show');
        $this.toggleClass('collapsed');

        // 关闭其他打开的菜单
        $('.sidebar .dropdown-menu').not($dropdownMenu).removeClass('show');
        $('.sidebar .dropdown-toggle').not($this).addClass('collapsed');
    });

    // 响应式处理
    $(window).resize(function () {
        if ($(window).width() > 768) {
            $('.sidebar').removeClass('show');
        }
    });

    // 图片预览
    $(".photo").click(function () {
        var src = $(this).attr("src");
        layer.photos({
            photos: {
                "title": "",
                "start": 0,
                "data": [{
                    "alt": "layer",
                    "pid": 1,
                    "src": src,
                }],
            }
        });
    });

    
    // 初始化：当当前激活的菜单没有子菜单时，展开下面的第一个有子菜单的菜单
    (function initExpandNextDropdownWhenNoSubmenu() {
        var $sidebar = $(".sidebar .nav");
        // 如果已有展开的下拉，则不处理，避免覆盖服务端的激活逻辑
        var $opened = $sidebar.find('.dropdown-menu.show');
        if ($opened.length) {
            return;
        }

        // 找到当前激活的顶级菜单（不含子菜单的 .nav-link.active）
        var $activeTop = $sidebar.find('.nav-link.active').not('.dropdown-toggle').first();

        // 如果没有找到激活的顶级菜单，直接展开第一个下拉菜单
        var $targetDropdown = null;
        if ($activeTop.length) {
            // 从激活项往下找第一个 .dropdown
            $targetDropdown = $activeTop.nextAll('.dropdown').first();
        }
        if (!$targetDropdown || !$targetDropdown.length) {
            // 兜底：展开侧栏中的第一个下拉菜单
            $targetDropdown = $sidebar.children('.dropdown').first();
        }

        if ($targetDropdown && $targetDropdown.length) {
            var $toggle = $targetDropdown.find('.dropdown-toggle').first();
            var $menu = $targetDropdown.find('.dropdown-menu').first();
            // 展开目标下拉，并保持其他下拉为收起
            $menu.addClass('show');
            $toggle.removeClass('collapsed').addClass('active');
            $('.sidebar .dropdown-menu').not($menu).removeClass('show');
            $('.sidebar .dropdown-toggle').not($toggle).addClass('collapsed').removeClass('active');
        }
    })();
});