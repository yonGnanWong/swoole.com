<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <title>提示-<?= Swoole::$php->config['common']['site_name'] ?></title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php include __DIR__ . '/../include/css.php'; ?>
</head>
<body>

<!-- MAIN PANEL -->
<!-- RIBBON -->
<div class="row animated flipInY" style="margin: 150px auto; width: 800px;">
    <div class="well">
        <h2 class="row-seperator-header"><i class="fa fa-comments"></i> 提示信息 </h2>
        <div>
            <div class="alert alert-<?= $error ? 'danger' : 'success' ?> alert-block">
                <h4 class="alert-heading"><?= $info ?></h4>
                <?= $detail ?>
            </div>
            <div class="modal-footer">
                <?php if (empty($links)) { ?>
                <a class="btn btn-primary" href="/person/index/">
                    返回个人主页
                </a>
                <?php } else { ?>
                    <?php foreach ($links as $li): ?>
                        <a class="btn btn-<?= empty($li['type']) ? 'default' : $li['type'] ?>" href="<?= $li['url'] ?>">
                            <?= $li['text'] ?>
                        </a>
                    <?php endforeach; ?>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php include dirname(__DIR__) . '/include/javascript.php'; ?>
    <script>
        pageSetUp();
    </script>
</body>
</html>
