<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
</head>
<body>
  <!-- generator supported -->
  <div><?php echo $i18n->__i18n("保存"); ?></div>

  <!-- generator supported ()内のスペース・改行は無視する -->
  <div><?php echo $i18n->__i18n(
    "戻る"
  ); ?></div>

  <div>
    <?php
      //generator unsupported
      $key = 'トップへ戻る';
      echo $foo = $i18n->__i18n($key);
    ?>
  </div>

  <div>
    <?php
      //generator unsupported
      echo $foo = $i18n->__i18n(
<<<EOF
改行入りの文章はgeneratorでは
対応しません。キーを`description for something`などとしてyamlを直接編集することによって扱うことは可能です。
EOF
      );
    ?>
  </div>
</body>
</html>