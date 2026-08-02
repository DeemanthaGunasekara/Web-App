<?php
require 'includes/auth.php';
require 'includes/db.php';
require_login();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// Fetch the blog and confirm it exists.
$stmt = $conn->prepare("SELECT id, user_id, title, content FROM blogPost WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $page_title = "Not Found";
    require 'includes/header.php';
    echo '<div class="empty-state"><p>Blog post not found.</p><p><a href="index.php">Back to home</a></p></div>';
    require 'includes/footer.php';
    exit();
}

$blog = $result->fetch_assoc();

// --- AUTHORIZATION CHECK ---
// A user may only edit their OWN blog post, even if they guess another post's id.
if ($blog['user_id'] != current_user_id()) {
    $page_title = "Not Allowed";
    require 'includes/header.php';
    echo '<div class="alert alert-error">You are not allowed to edit this blog post.</div>';
    require 'includes/footer.php';
    exit();
}

$error = "";
$title_val = $blog['title'];
$content_val = $blog['content'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title_val   = trim($_POST['title'] ?? '');
    $content_val = trim($_POST['content'] ?? '');

    if ($title_val === '' || $content_val === '') {
        $error = "Please fill in both the title and the content.";
    } else {
        // Re-check ownership again right before writing, and scope the
        // UPDATE itself to user_id = ? as a defense-in-depth measure.
        $update = $conn->prepare("UPDATE blogPost SET title = ?, content = ? WHERE id = ? AND user_id = ?");
        $update->bind_param("ssii", $title_val, $content_val, $id, $_SESSION['user_id']);

        if ($update->execute()) {
            header("Location: view_blog.php?id=" . $id);
            exit();
        } else {
            $error = "Something went wrong while saving your changes. Please try again.";
        }
        $update->close();
    }
}

$page_title = "Edit Blog";
require 'includes/header.php';
?>

<div class="form-card wide">
    <h1>Edit Blog</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo h($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_blog.php?id=<?php echo $id; ?>">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="<?php echo h($title_val); ?>" required>
        </div>

        <div class="form-group">
            <label for="content">Content (Markdown supported)</label>
            <div class="editor-wrapper">
                <div class="editor-pane">
                    <textarea id="content" name="content"><?php echo h($content_val); ?></textarea>
                </div>
                <div class="preview-pane">
                    <div class="preview-label">Live Preview</div>
                    <div id="preview"></div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn">Save Changes</button>
        <a href="view_blog.php?id=<?php echo $id; ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="js/editor.js"></script>

<?php require 'includes/footer.php'; ?>
