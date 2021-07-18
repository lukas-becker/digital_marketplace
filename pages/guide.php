<?php
include($_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php");
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Guide.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/controller/guide_controller.php";

$guide_controller = new guide_controller();
$category_controller = new category_controller();
$categories = $category_controller->get_all_categories();

if (isset($_GET["id"])) {
    $id = $_GET["id"];
} else {
    $id = 1;
}

$all_guides = $guide_controller->get_all_guides();
$active_guide = new Guide($id);


?>

<!doctype html>
<html lang="en">
<head>
    <?php
    $title = "Categories";
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php");
    ?>
    <link href="/style/categories_styles.css" rel="stylesheet">
</head>
<body>
<!--Include Navbar-->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 mb-5 pt-5">
    <div class="row">
        <h1>A simple guide to:
            <select class="form-control form-select d-inline w-50 mb-3 fs-1" id="sortMethod"
                    onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                <option value=""><?php echo $active_guide->category->get_name() ?></option>
                <?php foreach ($all_guides as $guide) {
                    if ($active_guide->id == $guide->id) continue;
                    ?>
                    <option value="/pages/guide.php?id=<?php echo $guide->id ?>"><?php echo $guide->category->get_name() ?></option>
                <?php } ?>
                <option value="" disabled>more coming soon!</option>
            </select>
        </h1>
    </div>
    <form action="/pages/article/all_articles.php" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo $id ?>">
        <input type="hidden" id="guide" name="guide" value="1">
        <?php foreach ($active_guide->questions as $question) { ?>
            <div class="row mb-5">
                <h5 class="col-3 title rounded-1"><?php echo $question->question_text ?></h5>
                <div class="col-8">
                    <select class="form-control form-select" id="q<?php echo $question->id ?>"
                            name="q[]">
                        <option value="" disabled selected>Select Answer</option>
                        <?php foreach ($question->answers as $answer) { ?>
                            <option value="<?php echo $answer->id ?>">
                                <?php echo $answer->answer_text . ";   " . $answer->description_text ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

            </div>
        <?php } ?>
        <button class="btn btn-primary" type="submit">submit answers</button>
    </form>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>

<div class="container my-5 pt-5">

</div>

</body>
</html>
