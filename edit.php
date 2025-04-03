<?php
include 'db.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM notes WHERE id = $id");
$note = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $content, $id);
        $stmt->execute();
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Edit Note</h1>
    <form action="" method="POST">
        <input type="text" name="title" value="<?= htmlspecialchars($note['title']); ?>" required>
        <textarea name="content" required><?= htmlspecialchars($note['content']); ?></textarea>
        <button type="submit">Update</button>
    </form>
    <a href="index.php">Back</a>
</body>
</html>
