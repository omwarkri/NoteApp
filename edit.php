<?php
include 'db.php';

// Check if ID exists and is valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM notes WHERE id = $id");

// Check if note exists
if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$note = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("UPDATE notes SET title = ?, content = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssi", $title, $content, $id);
        
        if ($stmt->execute()) {
            header("Location: index.php?success=1");
            exit();
        } else {
            $error = "Failed to update note. Please try again.";
        }
    } else {
        $error = "Please fill in both title and content.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note | Modern Note App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6c5ce7;
            --primary-light: #a29bfe;
            --secondary: #00cec9;
            --accent: #fd79a8;
            --light: #f8f9fa;
            --dark: #2d3436;
            --gray: #636e72;
            --light-gray: #dfe6e9;
            --danger: #d63031;
            --success: #00b894;
            --card-bg: #ffffff;
            --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: var(--bg-gradient);
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .app-header {
            text-align: center;
            margin-bottom: 2rem;
            
        }

        .app-title {
            color: var(--primary);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: inline-block;
            background: linear-gradient(to right, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            color: var(--primary);
            text-decoration: none;
            margin-bottom: 2rem;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            background-color: rgba(108, 92, 231, 0.1);
        }

        .back-btn:hover {
            color: white;
            background-color: var(--primary);
            transform: translateX(-5px);
        }

        .back-btn i {
            transition: transform 0.3s ease;
        }

        .back-btn:hover i {
            transform: translateX(-5px);
        }

        .edit-form {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            border-top: 4px solid var(--primary);
        }

        .form-group {
            margin-bottom: 1.8rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.8rem;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: var(--light);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.2);
            background-color: white;
        }

        .form-group input {
            height: 55px;
            font-weight: 500;
        }

        .form-group textarea {
            min-height: 300px;
            resize: vertical;
            line-height: 1.7;
        }

        .submit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(108, 92, 231, 0.3);
            width: 100%;
            margin-top: 1rem;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(108, 92, 231, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn i {
            transition: transform 0.3s ease;
        }

        .submit-btn:hover i {
            transform: translateX(5px);
        }

        .error-message {
            color: var(--danger);
            background-color: rgba(214, 48, 49, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        .last-updated {
            color: var(--gray);
            font-size: 0.9rem;
            margin-top: 1.5rem;
            text-align: center;
            font-style: italic;
        }

        @media (max-width: 768px) {
            body {
                padding: 1.5rem;
            }
            
            .app-title {
                font-size: 2rem;
            }
            
            .edit-form {
                padding: 1.5rem;
            }
            
            .form-group textarea {
                min-height: 250px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="app-header">
        <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Notes
            </a>
            <h1 class="app-title"><i class="fas fa-edit"></i> Edit Note</h1>
            
        </header>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form class="edit-form" method="POST">
            <div class="form-group">
                <label for="title"><i class="fas fa-heading"></i> Title</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($note['title']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="content"><i class="fas fa-align-left"></i> Content</label>
                <textarea name="content" id="content" required><?php echo htmlspecialchars($note['content']); ?></textarea>
            </div>
            
            <button type="submit" class="submit-btn">
                <i class="fas fa-save"></i> Update Note
            </button>
            
            <?php if (isset($note['updated_at'])) : ?>
                <p class="last-updated">
                    Last updated: <?php echo date('M j, Y \a\t g:i A', strtotime($note['updated_at'])); ?>
                </p>
            <?php endif; ?>
        </form>
    </div>

    <script>
        document.getElementById('title').focus();
        
        const textarea = document.getElementById('content');
        const charCount = document.createElement('div');
        charCount.style.textAlign = 'right';
        charCount.style.color = '#636e72';
        charCount.style.fontSize = '0.8rem';
        charCount.style.marginTop = '0.5rem';
        textarea.parentNode.appendChild(charCount);
        
        function updateCharCount() {
            const count = textarea.value.length;
            charCount.textContent = `${count} characters`;
        }
        
        textarea.addEventListener('input', updateCharCount);
        updateCharCount();
    </script>
</body>
</html>