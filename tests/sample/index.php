<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
</head>
<body>
  <!-- generator supported -->
  <div><?php echo Gomo\I18n::get("保存"); ?></div>

  <!-- generator supported ()内のスペース・改行は無視する -->
  <div><?php echo Gomo\I18n::get(
    "戻る"
  ); ?></div>

  <div>
    <?php
      //generator unsupported リテラルな文字列のみ
      $key = 'トップへ戻る';
      echo $foo = Gomo\I18n::get($key);
    ?>
  </div>

  <div>
    <?php
      //generator unsupported 改行を含む文字列は対応しません
      echo $foo = Gomo\I18n::get(
<<<EOF
改行入りの文章はgeneratorでは
対応しません。キーを`description for something`などとしてyamlを直接編集することによって扱うことは可能です。
EOF
      );
    ?>
  </div>
</body>
</html>