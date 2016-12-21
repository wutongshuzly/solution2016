<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <link rel="stylesheet" type="text/css" href="http://images.cecb2b.com/css/common-service/reset.css" />
    <link rel="stylesheet" type="text/css" href="http://images.cecb2b.com/css/common-service/zfacommon/header_foot.css">
    <link rel="stylesheet" type="text/css" href="css/index.css" />
    <link rel="stylesheet" type="text/css" href="http://at.alicdn.com/t/font_1471621056_7883842.css">
    <script src="http://images.cecb2b.com/js/common_new/jquery-1.8/jquery.js" type="text/javascript"></script>
    <script type="text/javascript" src="http://images.cecb2b.com/js/common_new/common_top_2014.js"></script>
    <script type="text/javascript" src="http://images.cecb2b.com/js/common-service/commonstt.js"></script>
    <!-- <script src="http://images.cecb2b.com/js/common-service/jquery.SuperSlide.2.1.1.js"></script>-->
    <script src="http://images.cecb2b.com/js/common-service/common.js"></script>
    <script type="text/javascript" src="<?=Yii::$app->params['website']?>/header_data_load.do"></script>
    <script type="text/javascript" src=" http://images.cecb2b.com/js/common-service/zx.js"></script>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginContent('@app/views/layouts/header.php'); ?>
<!-- You may need to put some content here -->
<?php $this->endContent(); ?>
<?php $this->beginContent('@app/views/layouts/solutionLogo.php'); ?>
<?php $this->endContent(); ?>
<?php $this->beginBody() ?>
<div class="bg-color">
    <div class="container">
    <?php $this->beginContent('@app/views/layouts/solutionListAd.php'); ?>
    <?php $this->endContent(); ?>
    <?= $content ?>
    </div>
</div>
<?php $this->beginContent('@app/views/layouts/footer.php'); ?>
<!-- You may need to put some content here -->
<?php $this->endContent(); ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
