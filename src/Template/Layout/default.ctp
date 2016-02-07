<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Muncie Arts and Culture Council
        <?= isset($pageTitle) ? " - $pageTitle" : '' ?>
    </title>

    <?= $this->fetch('meta') ?>
    <?= $this->Html->css('style') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <?= $this->Flash->render() ?>
    <?= $this->fetch('content') ?>

    <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="/js/jquery-1.12.0.min.js"><\/script>')</script>

    <?= $this->Html->script('/bootstrap/js/bootstrap.min') ?>

    <script>
        $(document).ready(function () {
            <?= $this->fetch('buffered') ?>
        });
    </script>

</body>
</html>
