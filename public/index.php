<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <?php
        header("refresh: 50;");
    ?>
    <title>Document</title>
</head>
<body>
    <?php
        include_once('../Database/Connection.class.php');
        include_once('../Database/ManageTable.class.php');
        include_once('../Model/Book.class.php');

        try{
            $pdo = Connection::get()->connect();
            $manageTables = new ManageTable($pdo);
            if(empty($_POST['input']))
            {
                $books = $manageTables->getBooks();
                /* foreach($books as $row)
                {
                    echo $row['nameauthor'].'-'.$row['namebook'].'<br />';
                }   */
            }else
            {
                $input = $_POST['input'];
                $books = $manageTables->getBooks($input);
            }
            //echo 'A connection to PostgreSQL database server has been established successfully.';
        }catch(\PDOException $e)
        {
            echo $e->getMessage();
        } 
    ?>
     <div class="book">
        <h2> 
            <center> Book Listing</center>
        </h2>
        <div>
            <form action="#" method="POST">
                <div class="search-form">
                    <input type="text" name="input" id="input" placeholder="search by author">
                    <input class="submit" type="submit" value="submit">
                </div>
            </form>
        </div>
        <div id="row">
            <div class="animation" style="background-color: aqua;">
                <div class="table">
                    <div class="row">Author</div>
                    <div class="row">Book</div>
                </div>                
            </div>
            <?php
                if(!empty($_POST['input']))
                {
                    foreach($books as $row){
                        $author = $manageTables->getAuthorByName($row['nameauthor']);
            ?>
                <div class="animation">
                    <div class="table">
                        <div class="row">
                            <?php
                                if(empty($row['nameauthor'])){
                                    echo htmlspecialchars("<none>(no author found)");
                                }else{
                                    echo $row['nameauthor'];
                                }
                            ?>
                        </div>
                        <div class="row">
                            <?php
                                if(empty($row['namebook'])){
                                    echo htmlspecialchars("<none> (no books found)");
                                }else{
                                    echo $row['namebook'];
                                }
                            ?>
                        </div>
                    </div>                
                </div>
            <?php
                    }
                }
            ?>
            <?php
                foreach($books as $row){
                    /* if(!empty($_POST['input'])){
                        echo "testing".$row['nameauthor'];
                        $author = $manageTables->getAuthorByName($row['nameauthor']);
                        echo "testing".$author['nameauthor'];
                    }else{ */
                        $author = $manageTables->getAuthorById($row['author_id']);
                    //}
            ?>
                <div class="animation">
                    <div class="table">
                        <div class="row">
                            <?php
                                if(empty($author['nameauthor'])){
                                    echo htmlspecialchars("<none>(no author found)");
                                }else{
                                    echo $author['nameauthor'];
                                }
                            ?>
                        </div>
                        <div class="row">
                            <?php
                                if(empty($row['namebook'])){
                                    echo htmlspecialchars("<none> (no books found)");
                                }else{
                                    echo $row['namebook'];
                                }
                            ?>
                        </div>
                    </div>                
                </div>
            <?php
                }
            ?>
        </div>        
    </div>
    <script src="javaScript.js"></script>
</body>
</html>