
<?php 
try{
    $pdo = new PDO(
        'mysql:host=localhost;dbname=edusync;charset=utf8mb4',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ]
    );
} catch(PDOException $e) {
    die('Connection failed: ' . $e->getMessage());

}


?>