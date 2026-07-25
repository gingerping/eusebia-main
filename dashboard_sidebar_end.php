<!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
    <!-- PWA -->
    <script src="js/pwa.js"></script>

    <!--
        Fix: Bootstrap dropdown menus (e.g. the "Actions" menu in student tables)
        were getting clipped/cut off by the scrollable .table-responsive wrapper,
        and Bootstrap's own Popper positioning kept fighting our fix. Each toggle
        now has data-display="static" so Popper never touches these menus — we
        fully control their position ourselves, switching them to `position: fixed`
        computed from the toggle button's on-screen coordinates so they float on
        top of the table (like a native <select>) instead of getting clipped.
    -->
    <script>
    $(document).on('shown.bs.dropdown', '.dropdown', function () {
        var $menu   = $(this).find('.dropdown-menu').first();
        var $toggle = $(this).find('[data-toggle="dropdown"]').first();
        if (!$menu.length || !$toggle.length) return;

        $menu.addClass('dropdown-menu-floating');

        var rect = $toggle[0].getBoundingClientRect();
        var menuWidth = $menu.outerWidth();

        var left = rect.right - menuWidth;
        if (left < 4) left = 4; // don't run off the left edge of the screen

        var top = rect.bottom + 2;
        // If there isn't enough room below, open the menu upward instead
        var menuHeight = $menu.outerHeight();
        if (top + menuHeight > window.innerHeight && rect.top - menuHeight > 0) {
            top = rect.top - menuHeight - 2;
        }

        $menu.css({ top: top + 'px', left: left + 'px', right: 'auto' });
    });

    $(document).on('hidden.bs.dropdown', '.dropdown', function () {
        $(this).find('.dropdown-menu').removeClass('dropdown-menu-floating').css({ top: '', left: '', right: '' });
    });

    // Reposition while the dropdown is open if the page is scrolled/resized
    $(window).on('scroll resize', function () {
        $('.dropdown').has('.dropdown-menu.show').trigger('shown.bs.dropdown');
    });
    </script>

</body>

</html>