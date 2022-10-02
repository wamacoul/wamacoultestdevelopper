<?php

class ManageTable
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function createTables() {
        $sqlList = [
                    "CREATE TABLE IF NOT EXISTS locationFolders(
                        id serial PRIMARY KEY,
                        nameurl text NOT NULL UNIQUE

                    );",
                    "CREATE TABLE IF NOT EXISTS authors (
                        id serial PRIMARY KEY,
                        nameAuthor varchar(255) NOT NULL UNIQUE

                    );",
                    "CREATE TABLE IF NOT EXISTS books (
                        id serial PRIMARY KEY,
                        author_id integer NOT NULL references authors(id),
                        locationfolder_id integer NOT NULL references locationFolders(id),
                        nameBook varchar(255) NOT NULL UNIQUE 
                     );"
            ];

        // execute each sql statement to create new tables
        foreach ($sqlList as $sql) {
            $this->pdo->exec($sql);
           // echo $sql."<br />";
        }
        
        return $this;
    }
    public function dropTables() {
        $sql = "DROP TABLE IF EXISTS books, authors, locationFolders;";
        echo $sql."<br />";
        // execute each sql statement to create new tables
        $this->pdo->exec($sql);
        
        return $this;
    }

    public function insertData($locationFolder_id,$data)
    {
        $author = $this->insertAuthor($data->author);
        print $author['id']."<br />";
        $this->insertBook($data->name,$author['id'],$locationFolder_id);
    }

    /**
     * insert a new row into the locationFolders table
     * @param type $nameurl
     * @return the id of the inserted row
     */
    public function insertLocationFolder($nameurl) {
        // prepare statement for insert
        $sql = 'INSERT INTO locationFolders(nameurl) VALUES(:nameurl)';
        $stmt = $this->pdo->prepare($sql);

        // pass values to the statement
        $stmt->bindValue(':nameurl', $nameurl);
        echo $sql."<br />";

        try{
            // execute the insert statement
            $stmt->execute();
        }catch(\Exception $e){
            if(strpos($e, "SQLSTATE[23505]")){
                return $this->getLocationFolderByName($nameurl);
             }else{
                 throw $e;
             }
        }        
        echo $sql."<br />";

        // return generated id
        return $this->pdo->lastInsertId('locationFolders_id_seq');
    }
    public function getLocationFolderByName($locationFolder){
        // prepare statement for insert
        $sql = "SELECT * FROM locationfolders WHERE nameurl = '".$locationFolder."' LIMIT 1";

        try{
           foreach($this->pdo->query($sql) as $row)
           {
               return $row;
           }
        }catch(\Exception $e)
        {
           throw $e;
        }

   }
    /**
     * insert a new row into the authors table
     * @param type $name
     * @return the id of the inserted row
     */
    public function insertAuthor($name) {
        // prepare statement for insert
        $sql = 'INSERT INTO authors(nameAuthor) VALUES(:name)';
        $stmt = $this->pdo->prepare($sql);
        // pass values to the statement
        $stmt->bindValue(':name', $name);
        
        try{
            // execute the insert statement
            $stmt->execute();
        }catch(\Exception $e){
            if(strpos($e, "SQLSTATE[23505]")){
               return $this->getAuthorByName($name);
            }else{
                throw $e;
            }
        }        
        echo "name authors = ".$name."<br />";
        // return generated id
        return $this->pdo->lastInsertId('authors_id_seq');
    }

    public function getAuthorByName($nameAuthor){
         // prepare statement for insert
         $sql = "SELECT * FROM authors WHERE nameauthor = '".$nameAuthor."' LIMIT 1";

         try{
            foreach($this->pdo->query($sql) as $row)
            {
                return $row;
            }
         }catch(\Exception $e)
         {
            throw $e;
         }

    }
    public function getAuthorById($id){
         // prepare statement for insert
         $sql = "SELECT * FROM authors WHERE id = '".$id."' LIMIT 1";

         try{
            foreach($this->pdo->query($sql) as $row)
            {
                return $row;
            }
         }catch(\Exception $e)
         {
            throw $e;
         }

    }
    public function getAuthors(){
         // prepare statement for insert
         $sql = "SELECT * FROM authors LIMIT 30";

         try{
            $authors = $this->pdo->query($sql);
            return $authors;
         }catch(\Exception $e)
         {
            throw $e;
         }

    }
    public function getBooks($authorName=null){
         // prepare statement for insert
         if(empty($authorName))
         {
            $sql = "SELECT * FROM books ";

            try{
                $books = $this->pdo->query($sql);
                
                return $books;
            }catch(\Exception $e)
            {
                throw $e;
            }
         }else{
            $sql = "SELECT 
                        nameauthor, 
                        namebook 
                    FROM 
                        books 
                    RIGHT JOIN authors 
                        ON authors.id = books.author_id
                    WHERE
                        authors.nameauthor = '".$authorName."'
                    LIMIT 30"
                    ;
            try{
                $books = $this->pdo->query($sql);              
                return $books;
            }catch(\Exception $e)
            {
                throw $e;
            }
         }

    }

    /**
     * insert a new row into the authors table
     * @param type $name
     * @return the id of the inserted row
     */
    public function insertBook($name,$author_id,$locationfolder_id) {
        // prepare statement for insert
        $sql = 'INSERT INTO books(nameBook,author_id,locationfolder_id) VALUES(:names,:author_id,:locationfolder_id)';
        //die($sql);
        $stmt = $this->pdo->prepare($sql);
        // pass values to the statement
        $stmt->bindValue(':names', $name);
        $stmt->bindValue(':author_id', $author_id);
        $stmt->bindValue(':locationfolder_id', $locationfolder_id);
        echo $sql."<br />";
        
        try{
            // execute the insert statement
            $stmt->execute();
        }catch(\Exception $e){
            if(strpos($e, "SQLSTATE[23505]")){
                return ;
             }else{
                 throw $e;
             }
        }        
        echo $sql."<br />";
        // return generated id
        return $this->pdo->lastInsertId('authors_id_seq');
    }

    /**
     * return tables in the database
     */
    public function getTables() {
        $stmt = $this->pdo->query("SELECT table_name 
                                   FROM information_schema.tables 
                                   WHERE table_schema= 'public' 
                                        AND table_type='BASE TABLE'
                                   ORDER BY table_name");
        $tableList = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tableList[] = $row['table_name'];
        }

        return $tableList;
    }

}