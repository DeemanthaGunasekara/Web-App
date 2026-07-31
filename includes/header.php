<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? h($page_title) . " – MyBlog" : "MyBlog"; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="index.php" class="logo">MyBlog</a>
            <nav class="main-nav">
                <a href="index.php">Home</a>
                <?php if (is_logged_in()): ?>
                    <a href="create_blog.php">Write a Blog</a>
                    <span class="nav-username">Hi, <?php echo h(current_username()); ?></span>
                    <a href="logout.php" class="btn-link">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php" class="btn-link">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="container">