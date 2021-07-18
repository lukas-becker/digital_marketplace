<!-- Show a modal with predefined message -->

<!-- Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog" id="messageModalDialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attention</h5>
                <!--<button type="button" class="btn-close"></button>-->
            </div>
            <div class="modal-body">
                <?php
                switch ($_GET["message"]) {
                    case "new_org":
                        echo "For security reasons you've been logged out. <br>Please login again.";
                        break;
                    case "no_org":
                        echo "You are not in an organization. <br>You cannot do this action.";
                        break;
                    case "not_vip":
                        echo "Only vip customers can bid on auctions.";
                        break;
                    case "missing_info":
                        echo "Something went wrong. <br>Please check the link you just opened or try again.";
                        break;
                    case "no_permission":
                        echo "You don't have permission to do this. <br>If you feel this is wrong contact us.";
                        break;
                    case "art_error":
                        echo "Something went wrong. <br>Please retry and make sure you fill out the form correctly.";
                        break;


                    default:
                        echo "Something went wrong.";
                        break;
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="closeModal()">Okay</button>
            </div>
        </div>
    </div>
</div>
<script>
    const messageModal = new bootstrap.Modal(document.getElementById('messageModal'), {
        backdrop: 'static',
        keyboard: false
    });
    messageModal.show();

    function closeModal() {
        messageModal.hide();
    }
</script>
