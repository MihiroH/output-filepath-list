<?php
// 警告メッセージの表示を停止
ini_set('log_errors', 'On');
ini_set('display_errors', 'Off');
ini_set('error_reporting', E_ALL );

// ディレクトリを再起的に削除
function rmrf($dir) {
  if (is_dir($dir) and !is_link($dir)) {
    array_map('rmrf',   glob($dir.'/*', GLOB_ONLYDIR));
    array_map('unlink', glob($dir.'/*'));
    rmdir($dir);
  }
}

// !!! dest配下のファイルを再起的に削除する場合 !!!
//$dir = "dest";
//rmrf($dir);
//system("rm -rf {$dir}");

//ファイルが送信された場合
$dir = __DIR__ . '/dest/';
$return_url = '//' . $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
$post_max_size = ini_get('post_max_size');

if (!isset($_FILES) || !$_FILES || !$_FILES['zip']) {
  echo 'ファイル形式が正しくないか、ファイルサイズが大きいです。<br>';
    echo 'アップロード可能なファイル形式: zip<br>';
    echo 'アップロード可能なファイルサイズの上限: ' . $post_max_size . '<br>';
  echo '<a href="' . $return_url . '">戻る</a>';
  exit;
}

$files_size = $_FILES['zip']['size'];

if($files_size > 0){
  $zip_name = basename($_FILES['zip']['name']);
  $zip_file = $dir . $zip_name . '.zip';

  //サーバにアップロード
  if (move_uploaded_file($_FILES['zip']['tmp_name'], $zip_file)) {
    $zip = new ZipArchive();

    if($zip->open($zip_file) === TRUE) {
      $zip->extractTo($dir . $zip_name);
      $zip->close();

      //zipファイルの削除
      unlink($zip_file);

      //展開されたフォルダ内にあるディレクトリを検索
      $dirs = scandir($dir . $zip_name . '/');

      //ディレクトリを決め打ち(0->'.', 1->'..'になる)
      $tmp_dir = $dir . $zip_name . '/' .  $dirs[2] . '/*';
      foreach(glob($tmp_dir) as $file){
        if(is_file($file)){
          var_dump($file);
        }
      }

      // csv作成
      $output=null;
      $retval=null;
      $command = "ls -d $(find ./dest/" . $zip_name . " -type f)";
      exec($command, $output, $retval);

      $file_path = __DIR__ . '/dest/' . $zip_name . '.csv';
      $fp = fopen($file_path, 'w');
      foreach ($output as $value) {
        $new_value = str_replace("./dest/" . $zip_name . "/", "", $value);
        fputcsv($fp, [$new_value]);
      }
      fclose($fp);

      // CSVファイルをダウンロード
      header('Content-Type: application/octet-stream');
      header("Content-Disposition: attachment; filename=filepaths.csv");
      header('Content-Transfer-Encoding: binary');
      readfile($file_path);

      $dir = "dest/" . $zip_name;
      system("rm -rf {$dir}");
    } else {
      echo '解凍エラー';
    }
  } else {
    echo 'アップロードエラー';
  }
}
