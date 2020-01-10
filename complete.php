<?php
//関連ファイルのインポート
require_once ('./Message.php');
require_once ('./env.php');

if($_SERVER['REQUEST_METHOD']==="POST"){
  //POSTリクエスト時の処理

}else{
  //GETリクエスト時の処理

  //一覧表示用の配列を宣言
  $message_list = array();
  try{
    //DBにアクセスして登録済データを投稿の新しい順に取得
    $pdo = new PDO(DSN, DB_USER, DB_PASS);
    $msgs = $pdo->query(
      "SELECT * FROM messages ORDER BY id DESC"
    );

    //Messageオブジェクトに格納、配列に追加
    foreach($msgs as $msg){
      $message = new Message($msg['user_name'],$msg['user_email'],$msg['main'],$msg['created_at']);
      array_push($message_list,$message);
    }
  }catch(PDOEXception $e){
    print("DBに接続できませんでした。");
    die();
  }
}
?>
<!doctype html>
<html lang="ja">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <title>PHP伝言板</title>
</head>
<body>
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
          <h1 class="display-4">PHP Message Board</h1>
          <form method="POST">
            <div class="form-group">
              <label for="user_name">お名前</label>
              <input type="text" class="form-control" id="user_name">
              <small class="form-text text-muted">投稿者名を記入してください</small>
            </div>
            <div class="form-group">
                <label for="user_email">メールアドレス</label>
                <input type="email" class="form-control" id="user_email">
                <small class="form-text text-muted">投稿者のメールアドレスを記入してください</small>
              </div>
            <div class="form-group">
              <label for="main_messag">メッセージ</label>
              <textarea name="main" class="form-control" id="main_messag" rows="3"></textarea>
              <small class="form-text text-muted">メッセージ本文</small>
            </div>
            <button type="submit" class="btn btn-primary">投稿</button>
          </form>
        </div>
      </div>
    <!--表示部分-->
    <div class="container">
      <?php
      foreach($message_list as $message){ ?>
        <div class="alert alert-primary" role="alert">
           <p><?=$message->get_main() ?></p>
           <p class="text-right"><?=$message->get_user_name() ?>(<?=$message->get_created_at()?>)</p>
        </div>
      <?php } ?>
    </div>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>
