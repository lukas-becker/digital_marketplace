<!-- Template for displaying an article as a card -->
<!-- Therefore a variable $article of the class Article has to be initialized beforehand -->

<?php global $article ?>

<div class="card mb-5 p-0">
    <a href="/pages/article/detailed_article.php?article=<?php echo $article->get_id() ?>"><img
                src="data:image;base64,<?php try {
                    echo $article->get_first_image();
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                } ?>" class="card-img-top" alt="..."/></a>
    <a href="/pages/article/detailed_article.php?article=<?php echo $article->get_id() ?>"><h5
                class="card-title px-3 pt-2"><?php echo $article->get_title(); ?></h5></a>
    <div class="card-body pt-0 pb-1">
        <p class="card-text"><?php echo strip_tags($article->get_description()); ?></p>
    </div>
    <div class="card-footer d-flex justify-content-between">
        Price:
        <?php
        if (!$article->auction) {
            echo number_format($article->get_current_price(), 2, ",", "") . '€';
        } else {
            echo "auction";
        } ?>

        <div>
            <?php
            if (isset($_SESSION["user"]))
                $user = $_SESSION["user"];
            if (isset($user) && ($article->get_organization() == $user->organization || $user->is_site_admin())) {
                echo '<span class="me-2" onclick="toggle_visibility(' . $article->get_id() . ', this)"><i class="fas ' . ($article->get_visible() ? "fa-eye" : "fa-eye-slash") . '"></i></span>';
                echo '<a href="/pages/article/edit' . ($article->auction ? "_auction" : "") . '.php?article=' . $article->get_id() . '"><span class="text-success"><i class="fas fa-edit"></i></span></a>';
            }
            ?>
        </div>
    </div>
</div>

<script>
    //Send signal to show or hide an article
    function toggle_visibility(id, caller) {
        let isVisible = caller.firstChild.classList.contains("fa-eye");
        let icon = caller.firstChild;

        var xmlhttp = new XMLHttpRequest();

        if (isVisible) {
            xmlhttp.open("GET", "/controller/article_controller?set_visibility=false&id=" + id, true);
        } else {
            xmlhttp.open("GET", "/controller/article_controller?set_visibility=true&id=" + id, true);
        }

        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                var result = JSON.parse(this.responseText);
                if (result["status"] == true) {
                    if (isVisible) {
                        icon.classList.remove("fa-eye");
                        icon.classList.add("fa-eye-slash");
                    } else {
                        icon.classList.remove("fa-eye-slash");
                        icon.classList.add("fa-eye");
                    }

                } else if (result["status"] == false) {

                } else {

                }
            }
        };

        xmlhttp.send();
    }
</script>