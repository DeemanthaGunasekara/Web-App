<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'includes/auth.php';
require 'includes/db.php';

$sql = "SELECT blogPost.id, blogPost.title, blogPost.content, blogPost.created_at, users.username
        FROM blogPost JOIN users ON blogPost.user_id = users.id
        ORDER BY blogPost.created_at DESC";
$result = $conn->query($sql);

$page_title = "Home";
require 'includes/header.php';
?>

<h1>Latest Blogs</h1>

<?php if ($result && $result->num_rows > 0): ?>
    <div class="blog-list">
        <?php while ($row = $result->fetch_assoc()): ?>
            <article class="blog-card">
                <h2><a href="view_blog.php?id=<?php echo (int)$row['id']; ?>"><?php echo h($row['title']); ?></a></h2>
                <div class="blog-meta">
                    By <?php echo h($row['username']); ?> &middot;
                    <?php echo date("F j, Y", strtotime($row['created_at'])); ?>
                </div>
                <p class="blog-excerpt">
                    <?php
                        $preview = strip_tags($row['content']);
                        echo h(mb_strimwidth($preview, 0, 200, '...'));
                    ?>
                </p>
            </article>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <p>No blogs have been posted yet.</p>
        <?php if (is_logged_in()): ?>
            <p><a href="create_blog.php">Write the first one!</a></p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>