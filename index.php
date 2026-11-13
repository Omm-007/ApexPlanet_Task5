



<?php
require_once 'config.php';

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && is_admin();

$search = trim($_GET['search'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 5;

$posts = [];
$totalPosts = 0;
$totalPages = 1;

// Show posts only after login
if ($isLoggedIn) {

    $conditions = [];
    $params = [];

    // Normal users can see only their own posts.
    // Admins can see all posts.
    if (!$isAdmin) {
        $conditions[] = 'user_id = :user_id';
        $params['user_id'] = (int) $_SESSION['user_id'];
    }

    // Search posts by title or content.
    if ($search !== '') {
        $conditions[] = '(title LIKE :title_search OR content LIKE :content_search)';

        $searchTerm = '%' . $search . '%';

        $params['title_search'] = $searchTerm;
        $params['content_search'] = $searchTerm;
    }

    $whereSql = !empty($conditions)
        ? 'WHERE ' . implode(' AND ', $conditions)
        : '';

    // Count matching posts.
    $countStmt = $pdo->prepare(
        "SELECT COUNT(*) FROM posts $whereSql"
    );

    $countStmt->execute($params);
    $totalPosts = (int) $countStmt->fetchColumn();

    $totalPages = max(1, (int) ceil($totalPosts / $limit));

    if ($page > $totalPages) {
        $page = $totalPages;
    }

    $offset = ($page - 1) * $limit;

    // Fetch posts.
    $stmt = $pdo->prepare(
        "SELECT id, user_id, title, content, created_at
         FROM posts
         $whereSql
         ORDER BY created_at DESC
         LIMIT :limit OFFSET :offset"
    );

    foreach ($params as $key => $value) {
        $stmt->bindValue(
            ':' . $key,
            $value,
            is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
        );
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    $posts = $stmt->fetchAll();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BlogSphere - Write, Share, Grow</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header class="site-header">

    <div class="container nav">

        <a class="brand" href="index.php">
            BlogSphere
        </a>

        <nav>
            <?php if ($isLoggedIn): ?>

                <a href="index.php">Dashboard</a>
                <a href="#about">About</a>
                <a href="#help">Help</a>

                <span>
                    Welcome, <?= e($_SESSION['username'] ?? 'User') ?>
                </span>

                <a class="button-link" href="create.php">
                    New Post
                </a>

                <form class="inline-form" method="POST" action="logout.php">
                    <input type="hidden" name="csrf_token"
                           value="<?= e(csrf_token()) ?>">

                    <button type="submit" class="secondary-button">
                        Logout
                    </button>
                </form>

            <?php else: ?>

                <a href="#about">About</a>
                <a href="#help">Help</a>
                <a href="#security">Security</a>

                <a href="login.php">Login</a>

                <a class="button-link" href="register.php">
                    Register
                </a>

            <?php endif; ?>
        </nav>

    </div>
</header>


<main>

<?php if (!$isLoggedIn): ?>

    <!-- PUBLIC HOMEPAGE -->

    <section class="hero">

        <p class="eyebrow">WRITE • SHARE • GROW</p>

        <h1>Welcome to BlogSphere</h1>

        <p>
            A simple place to express your thoughts,
            share your ideas and manage your own blog.
        </p>

        <div class="hero-actions">

            <a class="button-link" href="register.php">
                Get Started
            </a>

            <a class="secondary-button" href="login.php">
                Login to Your Account
            </a>

        </div>

    </section>


    <section class="container features-section">

        <h2>Everything You Need to Blog</h2>

        <div class="features-grid">

            <article class="feature-card">
                <h3>Create Your Posts</h3>
                <p>
                    Share your thoughts and publish
                    your own blog posts.
                </p>
            </article>

            <article class="feature-card">
                <h3>Manage Your Content</h3>
                <p>
                    Edit and manage your posts
                    whenever you need.
                </p>
            </article>

            <article class="feature-card">
                <h3>Private Accounts</h3>
                <p>
                    Access your personal posts
                    through your own account.
                </p>
            </article>

        </div>

    </section>


    <section class="container info-section" id="about">

        <h2>About BlogSphere</h2>

        <p>
            BlogSphere is a simple blog management platform
            where users can create, organize and manage
            their own blog posts.
        </p>

    </section>


    <section class="container info-section" id="help">

        <h2>Need Help?</h2>

        <p>
            You can register for a new account or log in
            to manage your blog posts. If you have trouble
            accessing your account, use the Forgot Password
            option on the login page.
        </p>

    </section>


    <section class="container info-section" id="security">

        <h2>Your Security Matters</h2>

        <p>
            BlogSphere uses account authentication,
            protected sessions and secure database
            operations to help protect user content.
        </p>

    </section>


<?php else: ?>

    <!-- PERSONAL DASHBOARD -->

    <section class="dashboard-hero container">

        <p class="eyebrow">YOUR PERSONAL SPACE</p>

        <h1>
            <?= $isAdmin ? 'Admin Dashboard' : 'My Dashboard' ?>
        </h1>

        <p>
            Welcome back, <?= e($_SESSION['username'] ?? 'User') ?>!
            Manage your blog posts from here.
        </p>

        <a class="button-link" href="create.php">
            + Create New Post
        </a>

    </section>


    <section class="container posts-section">

        <div class="dashboard-heading">

            <div>
                <h2>
                    <?= $isAdmin ? 'All Blog Posts' : 'My Posts' ?>
                </h2>

                <p>
                    Total Posts: <?= $totalPosts ?>
                </p>
            </div>

        </div>


        <!-- SEARCH -->

        <form method="GET" action="index.php" class="search-form">

            <input
                type="search"
                name="search"
                placeholder="Search your posts..."
                value="<?= e($search) ?>"
            >

            <button type="submit" class="button-link">
                Search
            </button>

            <?php if ($search !== ''): ?>
                <a href="index.php">Clear Search</a>
            <?php endif; ?>

        </form>


        <!-- POST RESULTS -->

        <?php if (empty($posts)): ?>

            <div class="empty-state">

                <?php if ($search !== ''): ?>

                    <h3>No Posts Found</h3>

                    <p>
                        We couldn't find any posts matching
                        "<?= e($search) ?>".
                        Try another keyword.
                    </p>

                <?php else: ?>

                    <h3>No Posts Yet</h3>

                    <p>
                        Start sharing your ideas by creating
                        your first blog post.
                    </p>

                    <a class="button-link" href="create.php">
                        Create Your First Post
                    </a>

                <?php endif; ?>

            </div>

        <?php else: ?>

            <div class="posts-grid">

                <?php foreach ($posts as $post): ?>

                    <?php
                    $canManage = $isAdmin ||
                        ((int) $post['user_id'] === (int) $_SESSION['user_id']);
                    ?>

                    <article class="post-card">

                        <p class="post-date">
                            <?= e($post['created_at']) ?>
                        </p>

                        <h3>
                            <?= e($post['title']) ?>
                        </h3>

                        <div class="post-content">
                            <?= nl2br(e($post['content'])) ?>
                        </div>

                        <?php if ($canManage): ?>

                            <div class="post-actions">

                                <a class="button-link"
                                   href="edit.php?id=<?= (int) $post['id'] ?>">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="delete.php"
                                      onsubmit="return confirm('Are you sure you want to delete this post?');">

                                    <input type="hidden"
                                           name="csrf_token"
                                           value="<?= e(csrf_token()) ?>">

                                    <input type="hidden"
                                           name="id"
                                           value="<?= (int) $post['id'] ?>">

                                    <button type="submit"
                                            class="danger-button">
                                        Delete
                                    </button>

                                </form>

                            </div>

                        <?php endif; ?>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </section>


    <!-- PAGINATION -->

    <?php if ($totalPages > 1): ?>

        <nav class="pagination" aria-label="Post pages">

            <?php if ($page > 1): ?>

                <a href="?<?= e(http_build_query([
                    'search' => $search,
                    'page' => $page - 1
                ])) ?>">
                    Previous
                </a>

            <?php endif; ?>

            <span>
                Page <?= $page ?> of <?= $totalPages ?>
            </span>

            <?php if ($page < $totalPages): ?>

                <a href="?<?= e(http_build_query([
                    'search' => $search,
                    'page' => $page + 1
                ])) ?>">
                    Next
                </a>

            <?php endif; ?>

        </nav>

    <?php endif; ?>

<?php endif; ?>

</main>


<footer class="site-footer">

    <div class="container">

        <h3>BlogSphere</h3>

        <p>Write • Share • Grow</p>

        <p>
            &copy; <?= date('Y') ?> BlogSphere.
            All rights reserved.
        </p>

    </div>

</footer>

</body>
</html>
