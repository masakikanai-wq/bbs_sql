<?php 

    // タイムゾーン設定
    date_default_timezone_set('Asia/Tokyo');
    
    // コメントのユニークidを取得
    $unique_id = uniqid();

    $id = $_GET['id'];
    // $FILE = 'message.txt';  //保存ファイル名
    // $file = json_decode(file_get_contents($FILE));
    $page_data = [];        //表示する配列

    //記事へのコメント関連
    // $COMMENT_DATA = 'comment.txt';
    // $comment_data = json_decode(file_get_contents($COMMENT_DATA));
    $comment_board = [];    // 全体配列
    $comment = '';          // コメント初期化
    $DATA = [];             // 追加するデータ
    $COMMENT_BOARD = [];    // 表示する配列
    $BOARD = [];            // 表示する配列

    // エラーメッセージ
    $error_message = [];

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
    // 手続き型の記述
    // $query = "SELECT * FROM `data`";
    // if ($success){
    //     $result = mysqli_query($link, $query);
    //     while ($row = mysqli_fetch_array($result)){
    //         $BOARD[] = [$row['id'], $row['title'], $row['message']];
    //     }
    // }
    
    // MySQLからデータを取得するための記述
    // 手続き型の記述
    // $query = "SELECT * FROM `comment`";
    // if ($success){
    //     $result = mysqli_query($link, $query);
    //     while ($row = mysqli_fetch_array($result)){
    //         $COOMENT_BOARD[] = [$row['id'], $row['article_id'], $row['comment']];
    //     }
    // }

    // オブジェクト型の記述
    // 記事タイトルと詳細の取得
    // WHERE id= '" . $id . "' の渡し方の記述が合っているか要確認
    $db = new PDO('mysql:dbname=laravel_news;host=localhost;charset=utf8','root','root');
    $articles = $db->prepare("SELECT * FROM `data` WHERE id= '" . $id . "' ");
    $articles->execute(array($_REQUEST['id']));
    $article = $articles->fetch();

    // オブジェクト型の記述
    // コメントの取得
    // データベースオブジェクトのインスタンスを作成する記述を複数回記述する必要があるか要確認
    $db = new PDO('mysql:dbname=laravel_news;host=localhost;charset=utf8','root','root');
    $comments = $db->query("SELECT * FROM `comment` WHERE article_id= '" . $id . "' ");
    $comment = $comments->fetch();

    // .txtパターンのときに必要
    // ネストした配列のlist()による展開（配列の配列の反復処理）
    // 公式のPHPドキュメントを参照
    // foreach ($file as $index => list($key, $comment_id)){
    //     if ($key == $id){
    //         $page_data = $file[$index];
    //     }
    // }

    // .txtパターンのときに必要
    // 記事ごとのコメントを取り出す処理
    // foreach ((array)$comment_data as $index => list($key, $comment_id)){
    //     $comment_board[] = $comment_data[$index];
    //     if ($comment_id == $id){
    //         $COMMENT_BOARD[] = $comment_data[$index];
    //     }
    // }

    // .txtパターンのときに必要
    // $COMMENT_DATAというファイルが存在しているときにファイルを読み込む
    // $COMMENT_DATAから$comment_boardに全体の配列を入れている
    // この処理必要ない？
    // if (file_exists($COMMENT_DATA)){
    //     $comment_board = json_decode(file_get_contents($COMMENT_DATA));
    // }

    // コメント送信ボタンが押されてからの処理
    if (!empty($_POST['btn_submit'])){

        if(!empty($_POST['comment'])){

            // 送信されたテキストを代入する
            $comment = $_POST['comment'];

            // .txtパターンのときに必要
            // 新規コメントデータを全体配列の挿入
            // $idを2番目に配置するのがポイント？
            // $unique_id → コメントごとのid
            // $id → 記事ごとのid
            // $DATA = [$unique_id, $id, $comment];
            // $comment_board[] = $DATA;

            // データ追加のためのQuery
            // $insert_query = "INSERT INTO `comment`(`id`, `article_id`, `comment`) VALUES ('{$unique_id}', '{$id}', '{$comment}')"; 
            // mysqli_query($link, $insert_query);

            // データベースオブジェクトのインスタンスを作成する記述を複数回記述する必要があるか要確認
            $db = new PDO('mysql:dbname=laravel_news;host=localhost;charset=utf8','root','root');
            $db->exec('INSERT INTO comment SET id="' . $unique_id . '", article_id="' . $id . '", comment="' . $comment . '"');

            // この$idを取得する記述は必要ないかも
            // $id = $_GET['id'];

             // .txtパターンのときに必要
            // 全体配列をファイルに保存する
            // file_put_contents($COMMENT_DATA, json_encode($comment_board, JSON_UNESCAPED_UNICODE));

            // 現在表示している記事詳細ページへリダイレクト
            // リダイレクトしないとリアルタイムでコメントが反映されなかった
            header("Location: article.php?id=$id");
            exit;
        }

        // コメント削除機能
        // 削除ボタンが押されたとき
        if (!empty($_POST['del'])){

            // $delに代入する必要があるのか確認すること
            $del = $_POST['del'];

            // 新しい全体配列を作る
            $new_comment_board = [];

            foreach ((array)$comment_data as $index => list($key, $comment_id)) {
                // 下記の記述は必要なさそうなので削除
                // $NEW_COMMENT_BOARD[] = $comment_data[$index];
                if ($key !== $del){
                    $new_comment_board[] = $comment_data[$index];
                }
            }

            file_put_contents($COMMENT_DATA, json_encode($new_comment_board, JSON_UNESCAPED_UNICODE));

            // 今いるページにリダイレクト
            header("Location: article.php?id=$id");
            exit;
        }

        //  エラーメッセージの表示
        if (empty($_POST['comment'])){
            $error_message[] = 'コメントは必須です。';
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
    <!-- 記事のタイトルと詳細表示部分 -->
    <section id="bbs-wrapper">
        <div class="container">
            <div class="bbs">
                <div>
                    <h3><?php echo $article[1]; ?></h3>
                </div>
                <div>
                    <p><?php echo $article[2]; ?></p>
                </div>
                <p class="home"><a href="/php_bbs">一覧に戻る</a></p>
            </div>
        </div>
    </section>
    <section>
        <div class="container article-hr">
            <hr>
        </div>
    </section>
    <!-- コメント投稿フォーム -->
    <section id="comment-submit-wrapper">
        <div class="container">
            <!-- エラーメッセージの表示 -->
            <?php if (!empty($error_message)):?>
                <ul>
                    <?php foreach($error_message as $value): ?>
                        <li><?php echo $value; ?></li>
                    <?php endforeach ?>
                </ul>
            <?php endif; ?>
            <div class="comment-submit">
                <form action="" method="post">
                    <div>
                        <label for="comment">この投稿に関するコメント</label>
                        <textarea name="comment" id="comment" cols="30" rows="10"></textarea>
                    </div>
                    <div class="input-submit">
                        <input class="btn" type="submit" name="btn_submit" value="送信">
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- コメント表示部分 -->
    <section id="comment-display-wrapper">
        <div class="container">
            <div class="comment-display">
                <h3>コメント一覧</h3>
                <hr>
                <?php
                    $db = new PDO('mysql:dbname=laravel_news;host=localhost;charset=utf8','root','root');
                    $comments = $db->query("SELECT * FROM `comment` WHERE article_id= '" . $id . "' ");
                ?>
                <?php while ($comment = $comments->fetch()): ?>
                    <p><?php print($comment['comment']); ?></p>
                    <hr>
                <?php endwhile; ?>
                <!-- <?php foreach((array)$comment as $value): ?>
                    <form class="comments" action="" method="post">
                        <article>
                            <div>
                                <p><?php echo $value[2]; ?></p>
                            </div> -->
                            <!-- コメント削除機能 -->
                            <!-- <div class="delete-submit">
                                <input type="hidden" name="del" value="<?php echo $value[0]; ?>">
                                <input class="btn-submit-delete" type="submit" name="btn_submit" value="削除" onclick="return confirm('コメントを削除しますか？')">
                            </div>
                        </article>
                    </form>
                    <hr>
                <?php endforeach; ?> -->
            </div>
        </div>
    </section>
</body>
</html>