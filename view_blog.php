<?php
require 'includes/auth.php';
require 'includes/db.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare(
    "SELECT blogPost.id, blogPost.title, blogPost.content, blogPost.user_id,
            blogPost.created_at, blogPost.updated_at, users.username
     FROM blogPost
     JOIN users ON blogPost.user_id = users.id
     WHERE blogPost.id = ?"
);
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
$is_owner = is_logged_in() && current_user_id() == $blog['user_id'];

// --- Simple built-in Markdown -> HTML converter (no external library needed) ---
function simple_markdown($text) {
    // Escape HTML first so raw tags in the post can't break the page or inject scripts.
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

    // Headings
    $text = preg_replace('/^###### (.*)$/m', '<h6>$1</h6>', $text);
    $text = preg_replace('/^##### (.*)$/m', '<h5>$1</h5>', $text);
    $text = preg_replace('/^#### (.*)$/m', '<h4>$1</h4>', $text);
    $text = preg_replace('/^### (.*)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^## (.*)$/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^# (.*)$/m', '<h1>$1</h1>', $text);

    // Bold / italic
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);

    // Inline code
    $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);

    // Blockquotes
    $text = preg_replace('/^&gt; (.*)$/m', '<blockquote>$1</blockquote>', $text);

    // Links [text](url)
    $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', $text);

    // Paragraphs: wrap blocks of text that aren't already headings/blockquotes
    $blocks = preg_split('/\n{2,}/', trim($text));
    $html = '';
    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block === '') continue;
        if (preg_match('/^<h[1-6]>|^<blockquote>/', $block)) {
            $html .= $block . "\n";
        } else {
            $html .= '<p>' . nl2br($block) . "</p>\n";
        }
    }
    return $html;
}

$html_content = simple_markdown($blog['content']);

$page_title = $blog['title'];
require 'includes/header.php';
?>