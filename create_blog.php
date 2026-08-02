<?php
require 'includes/auth.php';
require 'includes/db.php';
require_login();

$error = "";
$title_val = "";
$content_val = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title_val   = trim($_POST['title'] ?? '');
    $content_val = trim($_POST['content'] ?? '');

    if ($title_val === '' || $content_val === '') {
        $error = "Please fill in both the title and the content.";
    } else {
        $stmt = $conn->prepare("INSERT INTO blogPost (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $_SESSION['user_id'], $title_val, $content_val);

        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            header("Location: view_blog.php?id=" . $new_id);
            exit();
        } else {
            $error = "Something went wrong while saving your blog. Please try again.";
        }
        $stmt->close();
    }
}

$page_title = "Write a New Blog";
require 'includes/header.php';
?>

<div class="form-card wide">
    <h1>Write a New Blog</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo h($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="create_blog.php">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="<?php echo h($title_val); ?>" required>
        </div>

        <div class="form-group">
            <label for="content">Content (Markdown supported)</label>
            <div class="editor-wrapper">
                <div class="editor-pane">
                    <textarea id="content" name="content" placeholder="Write your blog in Markdown... e.g. # Heading, **bold**, *italic*, > quote"><?php echo h($content_val); ?></textarea>
                </div>
                <div class="preview-pane">
                    <div class="preview-label">Live Preview</div>
                    <div id="preview"></div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn">Publish Blog</button>
    </form>
</div>

<script src="js/editor.js"></script>

<?php require 'includes/footer.php'; ?>