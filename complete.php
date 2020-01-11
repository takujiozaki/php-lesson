<?php
//session開始
session_start();

//関連ファイルのインポート
require_once ('./Message.php');
require_once ('./env.php');
require_once('vendor/autoload.php');

if($_SERVER['REQUEST_METHOD']==="POST"){
  //POSTリクエスト時の処理

  //POSTされたデータを取得
  $user_name = htmlspecialchars($_POST['user_name']);
  $user_email = htmlspecialchars($_POST['user_email']);
  $main = htmlspecialchars($_POST['main']);

  //バリデーション用の連想配列を定義
  $data = ['user_name' => $user_name, 'user_email' => $user_email, 'main' => $main];

  //データのチェック(バリデーション)
  $v = new Valitron\Validator($data);
  $v->rule('required', ['user_name', 'user_email', 'main'])->message('{field}は必須です');
  $v->rule('email', 'user_email')->message('{field}が不正です');
  $v->labels(array(
    'user_name' => '名前',
    'user_email' => 'メールアドレス',
    'main' => '本文'
  ));
  
  //データに不備が無ければ
  if($v->validate()) {

    //データベースに登録
    try{
      //DBに登録
      $pdo = new PDO(DSN, DB_USER, DB_PASS);
      $sql = 'INSERT INTO messages(user_name, user_email, main, created_at) values(:user_name, :user_email, :main, now())';
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
      $stmt->bindValue(':user_email', $user_email, PDO::PARAM_STR);
      $stmt->bindValue(':main', $main, PDO::PARAM_STR);
      $stmt->execute();
    }catch(PDOEXception $e){
      print("DBに接続できませんでした。");
      die();
    }
  } else {//不備があれば
    //入力値とエラーメッセージをセッションに登録
    $msg = new Message($user_name, $user_email, $main, '');
    $_SESSION['inputMsg'] = serialize($msg);
    $_SESSION['errorMsg'] = $v->errors();
  }

  //リダイレクト
  header('Location:'.$_SERVER['SCRIPT_NAME']);
  exit();

}else{
  //GETリクエスト時の処理

  //エラー表示
  $user_name = '';
  $user_email = '';
  $main = '';

  if(isset($_SESSION['inputMsg'])){
    $inputMsg = unserialize($_SESSION['inputMsg']);
    $user_name = $inputMsg->get_user_name();
    $user_email =$inputMsg->get_user_email();
    $main = $inputMsg->get_main();
    unset($_SESSION['inputMsg']);
  }

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
    <style>
        .error{
            color:red;
        }
    </style>
</head>
<body>
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
          <h1 class="display-4">PHP Message Board</h1>
          <?php
                if(isset($_SESSION['errorMsg'])){
                    foreach ($_SESSION['errorMsg'] as $error) {
                        echo '<ul class="error">';
                        foreach ($error as $value) {
                            echo "<li>".$value."</li>";
                        }
                        echo "</ul>";
                    }
                    unset($_SESSION['errorMsg']);
                }
            ?>
          <form method="POST">
            <div class="form-group">
              <label for="user_name">お名前</label>
              <input type="text" class="form-control" name="user_name" id="user_name" value="<?=$user_name ?>">
              <small class="form-text text-muted">投稿者名を記入してください</small>
            </div>
            <div class="form-group">
                <label for="user_email">メールアドレス</label>
                <input type="email" class="form-control" name="user_email" id="user_email" value="<?=$user_email ?>">
                <small class="form-text text-muted">投稿者のメールアドレスを記入してください</small>
              </div>
            <div class="form-group">
              <label for="main">メッセージ</label>
              <textarea name="main" class="form-control" id="main" rows="3"><?=$main ?></textarea>
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
