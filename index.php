<?php
include 'db.php';
$result = $conn->query("SELECT * FROM notes ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>My Notes</h1>
    <a href="add.php" class="btn">Add New Note</a>
    <div class="notes-container">
        <?php while ($row = $result->fetch_assoc()) : ?>
            <div class="note">
                <h2><?= htmlspecialchars($row['title']); ?></h2>
                <p><?= nl2br(htmlspecialchars($row['content'])); ?></p>
                <small>Created: <?= $row['created_at']; ?></small>
                <br>
                <a href="edit.php?id=<?= $row['id']; ?>">Edit</a> |
                <a href="delete.php?id=<?= $row['id']; ?>" onclick="return confirm('Delete this note?');">Delete</a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
