<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ファイル名一覧取得</title>
  <style>
    body {
      text-align: center;
      padding: 24px;
    }
    form {
      margin-top: 40px;
    }
  </style>
</head>
<body>
  <h1>ファイル名一覧取得</h1>
  <form action="./thanks.php" method="post" enctype="multipart/form-data">
    <input id="file" type="file" accept="zip" name="zip">
    <input type="submit" name="btn_submit" value="csvダウンロード">
  </form>
</body>
</html>
