<!-- Scripts del proyecto -->

<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>

<script>
    function showSpinner() {
        $('#export-spinner').css('display', 'flex').hide().fadeIn(200);
    }

    function hideSpinner() {
        $('#export-spinner').fadeOut(200);
    }
</script>

<!--<script>

    window.addEventListener("load", function () {
        document.documentElement.classList.add("nav-ready");

        const loader = document.getElementById('main-loader');
        const content = document.getElementById('main-content-wrapper');

        if (loader && content) {
            loader.style.display = 'none';
            content.style.display = 'block';
        }
    });

</script>-->
