<?php
include($_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php");
require_once $_SERVER['DOCUMENT_ROOT'] . "/controller/article_review_controller.php";
$category_controller = new category_controller();
?>
<!doctype html>
<html lang="en">
<head>
    <?php
    $title = "New Article";
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php");
    ?>

    <!-- Text Editor -->
    <!-- Library -->
    <script src="/scripts/wysihtml/wysihtml.js"></script>
    <script src="/scripts/wysihtml/wysihtml.toolbar.min.js"></script>

    <!-- wysihtml5 parser rules -->
    <script src="/scripts/wysihtml/advanced_and_extended.js"></script>

    <script src="/scripts/article_edit_scripts.inc.js"></script>

    <style>
        #wysihtml-toolbar .btn {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        #description {
            border-top-left-radius: 0;
        }

        .imgDelete {
            opacity: 0.5;
            cursor: pointer;
            text-shadow: 1px 1px black;
        }

        .imgDelete:hover {
            opacity: 1;
        }
    </style>


</head>
<body>
<!--Include Navbar-->
<?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/navbar.inc.php"; ?>

<div class="container my-5 pt-5">
    <h2>Create new Article</h2>

    <form enctype="multipart/form-data" class="px-5" method="post"
          action="/controller/article_controller.php?create_article=true"
          onkeydown="return event.key != 'Enter';"
    >
        <!-- Title -->
        <div class="row my-3">
            <label for="title" class="form-label fs-4">Title</label>
            <div class="col-12 col-md-4 p-0">
                <input type="text" id="title" name="title" class="form-control">
            </div>
        </div>
        <!-- Price -->
        <div class="row my-3">
            <label for="price" class="form-label fs-4">Price</label>
            <div class="col-12 col-md-4 p-0">
                <div class="input-group p-0">
                    <input type="number" id="price" name="price" step='0.01' class="form-control">
                    <span class="input-group-text">€</span>
                </div>
            </div>
        </div>
        <!-- Category -->
        <div class="row my-3">
            <label for="category" class="form-label fs-4">Category</label>
            <div class="col-12 col-md-4 p-0">
                <div class="p-0 dropdown">
                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="priceFilter"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        Categories
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="priceFilter">
                        <?php foreach ($category_controller->get_all_categories() as $category) { ?>
                            <li>
                                <div class="dropdown-item">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="cat[]"
                                               id="categoryRadios<?php echo $category->get_id() ?>"
                                               value="<?php echo $category->get_id() ?>" <?php if (isset($cat)) if (in_array($category->get_id(), $cat)) echo "checked"; ?>>
                                        <div class="p-0"><label class="form-check-label"
                                                                for="categoryRadios<?php echo $category->get_id() ?>">
                                                <?php echo $category->name ?>
                                            </label>
                                        </div>
                                        </input>
                                    </div>
                                </div>
                            </li>
                        <?php } ?>

                    </ul>
                </div>
            </div>
        </div>
        <!-- Shipping -->
        <div class="row my-3">
            <label for="shipping" class="form-label fs-4">Shipping Time</label>
            <div class="col-12 col-4 p-0">
                <div class="input-group p-0">
                    <input type="number" id="shipping" name="shipping" class="form-control">
                    <span class="input-group-text">Days</span>
                </div>
            </div>
        </div>

        <!-- Location -->
        <div class="row my-3">
            <label for="location" class="form-label fs-4">Location</label>
            <div class="col-12 col-md-4 p-0">
                <input type="text" id="location" name="location" class="form-control">
            </div>
        </div>

        <!-- Stock -->
        <div class="row my-3">
            <label for="stock" class="form-label fs-4">Initial available stock</label>
            <div class="col-12 col-4 p-0">
                <div class="input-group p-0">
                    <input type="number" id="stock" name="stock" class="form-control">
                    <span class="input-group-text">Pcs.</span>
                </div>
            </div>
        </div>

        <!-- Highlights -->
        <div class="row mt-4 mb-3">
            <ul class="list-group col-12 col-4 p-0" id="highlightGroup">
                <li class="list-group-item active" aria-current="true">Highlights</li>
                <li class="list-group-item">
                    <div class="input-group p-0">
                        <input type="text" id="newHighlight" class="form-control" placeholder="new Highlight">
                        <span class="input-group-text btn btn-primary" onclick="addHighlight()" id="newHighlightButton">Add</span>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Dscription -->
        <div class="row my-3">
            <label for="shipping" class="form-label fs-4">Product Description</label>
            <div class="col-12 p-0">
                <div id="wysihtml-toolbar" class="btn-group" style="display: none;">
                    <a data-wysihtml-command="bold" class="btn btn-primary"><i class="fas fa-bold"></i></a>
                    <a data-wysihtml-command="italic" class="btn btn-primary"><i class="fas fa-italic"></i></a>
                    <a data-wysihtml-command="underline" class="btn btn-primary"><i class="fas fa-underline"></i></a>

                    <!-- Some wysihtml5 commands like 'createLink' require extra paramaters specified by the user (eg. href) -->
                    <a data-wysihtml-command="createLink" class="btn btn-primary"><i class="fas fa-link"></i></a>
                    <div data-wysihtml-dialog="createLink" class="bg-primary ps-1 pt-1" style="display: none;">
                        <label class="text-white">Link:</label>
                        <input data-wysihtml-dialog-field="href" value="http://" class="text">

                        <a data-wysihtml-dialog-action="save" class="text-white text-decoration-none">OK</a>
                        <a data-wysihtml-dialog-action="cancel" class="me-1 text-white text-decoration-none">Cancel</a>
                    </div>
                    <a onclick="editor.composer.commands.exec('removeLink');" class="btn btn-primary"><i
                                class="fas fa-unlink"></i></a>
                </div>


                <textarea class="form-control" placeholder="Description" id="description" name="description">

                </textarea>
            </div>
        </div>

        <!-- Images -->
        <div class="row my-3">
            <label for="pictures" class="form-label fs-4">Product Images</label>
            <div class="col-4 col-md-2 p-0 mb-2">
                <label for="addImgInput" class="btn btn-primary" onclick="addPicture()">Add Picture</label>
                <input type="file" id="addImgInput" accept="image/png, image/jpeg" class="d-none"
                       onchange="addPicture()">
            </div>
            <div class="col-12 col-md-10">
                <div class="row bg-white col-10 form-control pt-2 pb-1 d-flex flex-row" id="imgBox">
                    <div class="p-0 me-1 mb-1 border position-relative d-none" id="drop_0"
                         style="height: 90px; width: 90px;" ondrop="drop(event)" ondragover="allowDrop(event)">
                        <img src="" id="img_0" class="position-absolute" draggable="true" ondragstart="drag(event)"
                             width="90">
                        <span class="position-absolute top-0 end-0 text-danger imgDelete" onclick="deletePicture(this)"><i
                                    class="fas fa-times"></i></span>
                    </div>
                    <input type="hidden" id="txt_0" name="img_txt[]" value="">


                    <span style="height: 90px; width: 0" class="p-0"></span>
                </div>
            </div>

        </div>

        <hr>
        <button type="submit" class="btn btn-warning form-control mb-5">Make public</button>
    </form>

</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>

</body>
</html>
