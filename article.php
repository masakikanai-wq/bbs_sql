<?php 

    // タイムゾーン設定
    date_default_timezone_set('Asia/Tokyo');
    
    // コメントのユニークidを取得
    $unique_id = uniqid();

    $id = $_GET['id'];
    $page_data = [];        //表示する配列

    //記事へのコメント関連
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

    // 記事タイトルと詳細の取得
    // WHERE id= '" . $id . "' の渡し方の記述が合っているか要確認
    $db = new PDO('mysql:dbname=laravel_news;host=localhost;charset=utf8','root','root');
    $articles = $db->query("SELECT * FROM `data` WHERE id= '" . $id . "' ");
    $articles->execute(array($_REQUEST['id']));
    $article = $articles->fetch();

    // コメントの取得
    // データベースオブジェクトのインスタンスを作成する記述を複数回記述する必要があるか要確認
    $db = new PDO('mysql:dbname=laravel_news;host=localhost;charset=utf8','root','root');
    $comments = $db->query("SELECT * FROM `comment` WHERE article_id= '" . $id . "' ");
    $comment = $comments->fetch();

    // コメント送信ボタンが押されてからの処理
    if (!empty($_POST['btn_submit'])){

        if(!empty($_POST['comment'])){

            // 送信されたテキストを代入する
            $comment = $_POST['comment'];

            // データベースオブジェクトのインスタンスを作成する記述を複数回記述する必要があるか要確認
            $db = new PDO('mysql:dbname=laravel_news;host=localhost;charset=utf8','root','root');
            $db->exec('INSERT INTO comment SET id="' . $unique_id . '", article_id="' . $id . '", comment="' . $comment . '"');

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

            $db = new PDO('mysql:dbname=laravel_news;host=localhost;charset=utf8','root','root');
            $statement = $db->prepare('DELETE FROM `comment` WHERE id=?');
            $statement->execute(array($del));

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
    <link rel="stylesheet" href="css/style_article.css">
</head>
<body>
    <!-- データベース接続確認 -->
    <?php 
        try {
            $db = new PDO('mysql:dbname=laravel_news;host=localhost;charset=utf8','root','root');
            // 接続成功の文字を出したいときはコメントアウトを解除
            // echo 'DB Connection Success!';
        } catch(PDOException $e) {
            echo 'DB接続エラー:' . $e->getMessage();
        };
    ?>
    <nav class="main-header">
        <div class="nav-bar">
            <a href="/php_bbs_sql" class="nav-link">Laravel News</a>
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
                <p class="home"><a href="/php_bbs_sql/">一覧に戻る</a></p>
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
                    <div class="input-body">
                        <textarea name="comment" id="comment" class="post-it post-it-blue"></textarea>
                        <input class="btn-submit" type="submit" name="btn_submit" value="コメントを書く">
                        <div class="input-submit">
                        </div>
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
                <div class="comment-display-area">
                    <form action="" method="post">
                        <div class="comment-input-body">
                            <?php while ($comment = $comments->fetch()): ?>
                                <div class="comment-input-posts">
                                    <p class="post-it post-it-blue"><?php print($comment['comment']); ?></p>
                                    <div class="delete-submit">
                                        <input type="hidden" name="del" value="<?php print $comment['id'] ?>"/>
                                        <input class="btn-submit-delete" type="submit" name="btn_submit" value="コメントを消す" onclick="return confirm('コメントを削除しますか？')">
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <script src="script.js"></script>
</body>
</html>