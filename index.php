<?php 

    // タイムゾーン設定
    date_default_timezone_set('Asia/Tokyo');

    $error_message = array();

    // 修正のための追記部分
    $view_name = ''; //タイトルの変数
    $message = ''; //記事の内容
    $now_date = ''; //現在時刻取得用

    $FILE = 'message.txt'; //保存ファイル名
    $id = uniqid(); //ユニークなIDを自動生成
    $DATA = []; //一回分の投稿の情報を入れる
    $BOARD = []; //全ての投稿の情報を入れる

    // phpMyAdminとの接続
    $user = 'root';
    $password = 'root';
    $db = 'laravel_news';
    $host = 'localhost';
    $port = 3306;

    $link = mysqli_init();
    $success = mysqli_real_connect(
        $link,
        $host,
        $user,
        $password,
        $db,
        $port
    );

    // MySQLからデータを取得するための記述
    $query = "SELECT * FROM `data`";
    if ($success){
        $result = mysqli_query($link, $query);
        while ($row = mysqli_fetch_array($result)){
            $BOARD[] = [$row['id'], $row['title'], $row['message']];
        }
    }

    // .txtパターンのときに必要
    // $FILEというファイルが存在しているときにファイルを読み込む
    // if (file_exists($FILE)){
    //     $BOARD = json_decode(file_get_contents($FILE));
    // }

    if (!empty($_POST['btn_submit'])){

        if(!empty($_POST['view_name']) && !empty($_POST['message'])){
            // 送信されたテキストを代入する
            $view_name = $_POST['view_name'];
            $message = $_POST['message'];

            // 現在時刻の取得方法を後で調べる
            // $now_date = date("Y-m-d H:i:s");

            // .txtパターンのときに必要
            // 新規データ
            // $DATA = [$id, $view_name, $message];
            // $BOARD[] = $DATA;

            // データ追加のためのQuery
            $insert_query = "INSERT INTO `data`(`id`, `title`, `message`) VALUES ('{$id}', '{$view_name}', '{$message}')"; 
            mysqli_query($link, $insert_query);

            // .txtパターンのときに必要
            // 全体配列をファイルに保存する
            // file_put_contentsは fopen()→fwrite()→fclose を実行するのと同じ
            // json_encodeは指定した値をJSON形式に変換した文字列を返す
            // JSON_UNESCAPED_UNICODE を入れることで日本語でjsonを返してくれる
            // これがないと文字コードで日本語部分が表示されてしまう
            //ファイル保存するときのテキストメッセージが改行できないので確認する
            // file_put_contents($FILE, json_encode($BOARD, JSON_UNESCAPED_UNICODE));

            header("Location: index.php");
            exit;
        }

        // ユーザーからの値をHTMLに出力するときにセキュリティ目的で使用する
        $view_name = htmlspecialchars($view_name, ENT_QUOTES);
        $message = htmlspecialchars($message, ENT_QUOTES);

        
        // エラーメッセージ取得
        // タイトルのチェック
        if (empty($_POST['view_name'])){
            $error_message[] = 'タイトルは必須です。';
        }
        
        // タイトル文字数チェック
        if ((mb_strlen($_POST['view_name'])) > 30){
            $error_message[] = 'タイトルは30文字以下にしてください。';
        }
        
        // コメントのチェック
        if (empty($_POST['message'])){
            $error_message[] = 'メッセージは必須です。';
        }
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BBS</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="script.js"></script>
</head>
<body>
    <?php 
        try {
            $db = new PDO('mysql:dbname=laravel_news;host=localhost;charset=utf8','root','root');
            echo 'DB Connection Success!';
        } catch(PDOException $e) {
            echo 'DB接続エラー:' . $e->getMessage();
        };
    ?>
    <nav class="main-header">
        <div class="nav-bar">
            <a href="/php_bbs" class="nav-link">Laravel News</a>
        </div>
    </nav>
    <section id="bbs-wrapper">
        <div class="container">
            <div class="bbs">
                <h2 class="content-header">さぁ、最新のニュースをシェアしましょう！</h2>
                <!-- エラーメッセージ表示 -->
                <?php if (!empty($error_message)):?>
                    <ul>
                        <?php foreach($error_message as $value): ?>
                            <li><?php echo $value; ?></li>
                        <?php endforeach ?>
                    </ul>
                <?php endif; ?>
                <!-- 投稿フォーム -->
                <form action="" method="post" onsubmit="return submitChk()">
                    <div>
                        <label for="view_name">タイトル</label>
                        <input id="view_name" type="text" name="view_name" value="<?php if (isset($view_name)){echo $view_name;} ?>">
                    </div>
                    <div>
                        <label for="message">記事</label>
                        <textarea name="message" id="message" cols="30" rows="10"></textarea>
                    </div>
                    <div class="input-submit">
                        <input class="btn" type="submit" name="btn_submit" value="送信">
                    </div>
                </form>
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            <hr>
        </div>
    </section>
    <section id="message-wrapper">
        <div class="container">
            <?php foreach((array)$BOARD as $value): ?>
                <article>
                    <div class="info">
                        <h2><?php echo $value[1]; ?></h2>
                        <!-- 時刻の表示がエラーになってしまうので後で修正する -->
                        <!-- <time><?php  echo date('Y年m月d日 H:i', strtotime($value[3])); ?></time> -->
                    </div>
                    <p><?php echo $value[2]; ?></p>
                    <p class="routing"><a href="article.php?id=<?php echo $value[0] ?>">記事全文・コメントを見る</a></p>
                </article>
                <hr>
            <?php endforeach; ?>
        </div>
    </section>
</body>
</html>