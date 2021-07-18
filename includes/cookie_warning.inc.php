<!-- Show a modal with the remark that cookies are used  -->
<?php
if (!isset($_COOKIE["consent"])) {
    ?>
    <!-- Modal -->
    <div class="modal fade" id="cookieModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" id="cookieModalDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cookies</h5>
                    <!--<button type="button" class="btn-close"></button>-->
                </div>
                <div class="modal-body">
                    This site uses cookies!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="acceptCookies()">Accept</button>
                    <a href="https://google.com" class="btn btn-secondary">Deny</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const cookieModal = new bootstrap.Modal(document.getElementById('cookieModal'), {
            backdrop: 'static',
            keyboard: false
        });
        cookieModal.show();

        /**
         * Hide the modal with the remark of cookies
         */
        function acceptCookies() {
            document.cookie = "consent=true; max-age=31536000; path=/";
            cookieModal.hide();
        }
    </script>

    <?php
}
?>
