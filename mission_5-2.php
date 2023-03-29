<!DOCTYPE html>
<html lang = "ja">
<head>
    <meta charset = "UTF-8">
    <title>mission_5-1</title>
</head>
<body>
<?php
//PHPからデータベースを操作する手順
//1,データベースに接続する
$dsn = 'mysql:dbname=******;host =localhost';//データソース名を指定する文字列で、データベースの種類やホスト名、データベース名の情報）
$user = '******';
$password ='******';

try{//try_catch構文（エラー）
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));//データベース操作でエラーが発生した場合に警告

//2,データベース内にテーブルを設定
$sql="CREATE TABLE If NOT EXISTS board"//もしまだこのテーブルが存在しないのならば（すでにboardというテーブルが存在しているのに、同じな名前のテーブルを作成しようとした際に発生するエラーである）
    ."("
    ."id INT AUTO_INCREMENT PRIMARY KEY,"
    ."name char(32),"//カラムを設定（32文字）
    ."comment TEXT,"
    ."pass TEXT,"
    ."date TEXT"
    .");";
$stmt=$pdo->query($sql);//$sql変数に格納されているSQLクエリを実行し、結果を$stmt変数に格納します

//データベース内のテーブル一覧を表示
    $sql ="SHOW TABLES";//テーブルが作成された事が確認できる、
    $result = $pdo->query($sql);//
    foreach($result as $row){
    echo $row[0]; 
    echo "<br>";
    }
    echo "<hr>";
    
//データを入力する（データレコードの挿入）
$sql = $pdo->prepare("INSERT INTO board(name, comment, pass, date) VALUES(:name,:comment,:pass,:date)");//挿入を意味する（SQL文の中にパラメーターを指定する場合は、SQLに直接與や変数を書かずに「プレースホルダ」
$sql ->bindParam(':name',$name, PDO::PARAM_STR);//SQL文でプレースホルダーを指定した場合、実際の値をセット）
$sql ->bindParam(':comment',$comment, PDO::PARAM_STR);
$sql ->bindParam(':pass',$password, PDO::PARAM_STR);
$sql ->bindParam(':date',$date, PDO::PARAM_STR);

//新規投稿機能
if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"]) && empty($_POST["editNO"])){
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $pass = $_POST["pass"];
    $date = date("Y年m月d日 H時i分s秒");
    if($pass =="pass0261"){
         $sql -> execute();   
    }else{
            echo "パスワードが違います";
    }
    
}elseif(!empty($_POST["editNO"])){  
        $editNO = $_POST["editNO"];//変更する投稿番号
        $edit_name =$_POST['name'];
        $edit_com =$_POST['comment'];
        $edit_password =$_POST['pass'];
        $date = date("Y年m月d日 H時i分s秒");
        if($edit_password="pass0261"){
            $sql = 'UPDATE board SET name =:name, comment =:comment, pass =:pass,date =:date WHERE id =:id';//tbtestというテーブルの中で、idが指定された値に一致するレコードのnameとcommentを更新するということ
            $stmt =$pdo ->prepare($sql);
            $stmt ->bindParam(':id',$editNO, PDO::PARAM_INT);
            $stmt ->bindParam(':name',$edit_name, PDO::PARAM_STR);
            $stmt ->bindParam(':comment',$edit_com, PDO::PARAM_STR);
            $stmt ->bindParam(':pass',$edit_password, PDO::PARAM_STR);
            $stmt ->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt -> execute();   
        }else{
            echo "パスワードが違います";
        }
        
}if(!empty($_POST["delete"]) &&!empty($_POST["delete_pass"])){//削除機能
    $id = $_POST["delete"];
    $pass = $_POST["delete_pass"];
    if($pass =="pass0261"){
        $sql ='delete from board WHERE id=:id';
        $stmt = $pdo ->prepare($sql);//$pdoというデータベース接続オブジェクトを使って、$sqlというSQL文を実行するために準備を行っている（目的はSQLインジェクション攻撃から身を守るため）
        $stmt ->bindParam(':id',$id, PDO::PARAM_INT);
        $stmt -> execute();    
    }else{
        echo "パスワードが違います";
    }
    
    
}if(!empty($_POST["edit"])){//編集選択機能
    $edit = $_POST["edit"];
    $pass = $_POST["edit_pass"];
    $sql = 'SELECT*FROM board WHERE id =:id';//id=idの条件を基にboardテーブルから投稿を取得
    $stmt =$pdo ->prepare($sql);//SQL文を実行するための準備をする
    $stmt ->bindParam(':id',$edit, PDO::PARAM_INT);//SQL文中のパラメーター；idに$editをバインドする
    $stmt ->execute();//SQL文を実行する
    $result = $stmt ->fetch(PDO::FETCH_ASSOC);//クエリ実行結果から1行を取得するため、fetch
        if($pass =="pass0261"){
            $editname= $result["name"];
            $editcom =$result["comment"];
            $edit_password =$result['pass'];    
        }else{
            echo "パスワードが違います";
        }
}
}//tryの終わり
catch(PDOException $e){
    echo "エラーが発生しました".$e->getMessage();
}
?>

<form action ="" method ="post">
    <input type ="text" name ="name" placeholder ="名前" value="<?php if(isset($editname)) {echo $editname;}?>"><br>
    <input type ="text" name ="comment" placeholder ="コメント" value ="<?php if(isset($editcom)) {echo $editcom;}?>"><br>
    <input type ="hidden" name ="editNO" placeholder ="編集判別番号" value ="<?php if(isset($edit)) {echo $edit;}?>">
    <input type ="text" name ="pass" placeholder ="パスワード" value ="<?php if(isset($edit_password)) {echo $edit_password;}?>">
    
    
    <input type ="submit" name ="submit" value ="送信"><br><br>
</form>
<form action ="" method ="post">
    <input type ="number" name ="delete" placeholder ="削除対象番号"><br>
    <input type="text" name="delete_pass" placeholder="パスワード">
    <input type="submit" name="delete_submit" value="削除"><br><br>
</form>

<form action ="" method ="post">
    <input type="number" name="edit" placeholder="編集対象番号"><br>
    <input type="text" name="edit_pass" placeholder="パスワード">
    <input type="submit" name="edit_submit" placeholder="編集">
</form><br><br>

<?php
    $sql ='SELECT* FROM board';//SELECT=カラムを抽出（＊）は全て当てはまる
    $stmt = $pdo ->query($sql);
    $results =$stmt ->fetchAll();
    foreach($results as $row){
        echo $row['id'].' ';
        echo $row['name'].' ';
        echo $row['comment'].' ';
        echo $row['date'];
        echo "<hr>";
    }
?>
</body>
</html>
