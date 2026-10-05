<?php
require __DIR__ . '/includes/init.php';

$tools = all_tools();
$categories = db_all('SELECT slug FROM categories ORDER BY name ASC');
$posts = db_all("SELECT slug, title FROM posts WHERE status = 'published' ORDER BY created_at DESC");
$pages = db_all('SELECT slug FROM pages ORDER BY slug ASC');

require __DIR__ . '/includes/header.php';
?>

<main class="container py-5">

    <div class="mb-5">
        <h1 class="display-5 fw-bold">Sitemap</h1>
        <p class="text-muted">
            Explore all important pages, tools, categories and articles on ATTAALI.
        </p>
    </div>

    <div class="row g-5">

        <!-- Main Pages -->
        <div class="col-md-6">
            <h2 class="h4 mb-3">Main Pages</h2>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <a href="<?php echo e(url('/')); ?>">Home</a>
                </li>
                <li class="mb-2">
                    <a href="<?php echo e(url('/tools')); ?>">Tools</a>
                </li>
                <li class="mb-2">
                    <a href="<?php echo e(url('/blog')); ?>">Blog</a>
                </li>
                <li class="mb-2">
                    <a href="<?php echo e(url('/about-us')); ?>">About Us</a>
                </li>
                <li class="mb-2">
                    <a href="<?php echo e(url('/contact')); ?>">Contact</a>
                </li>
            </ul>
        </div>

        <!-- Tools -->
        <div class="col-md-6">
            <h2 class="h4 mb-3">Tools</h2>
            <ul class="list-unstyled">
                <?php foreach ($tools as $slug => $tool): ?>
                    <li class="mb-2">
                        <a href="<?php echo e(url('/tools/' . $slug)); ?>">
                            <?php echo e($tool['name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Blog Categories -->
        <div class="col-md-6">
            <h2 class="h4 mb-3">Blog Categories</h2>
            <ul class="list-unstyled">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <li class="mb-2">
                            <a href="<?php echo e(url('/category/' . rawurlencode($category['slug']))); ?>">
                                <?php echo e(ucwords(str_replace('-', ' ', $category['slug']))); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="text-muted">No categories available.</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Blog Posts -->
        <div class="col-md-6">
            <h2 class="h4 mb-3">Blog Posts</h2>
            <ul class="list-unstyled">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <li class="mb-2">
                            <a href="<?php echo e(url('/blog/' . rawurlencode($post['slug']))); ?>">
                                <?php echo e($post['title']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="text-muted">No blog posts available.</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Important Pages -->
        <?php if (!empty($pages)): ?>
            <div class="col-12">
                <h2 class="h4 mb-3">Important Pages</h2>

                <ul class="list-unstyled row">
                    <?php foreach ($pages as $page): ?>
                        <li class="col-md-4 mb-2">
                            <a href="<?php echo e(url('/' . $page['slug'])); ?>">
                                <?php echo e(ucwords(str_replace('-', ' ', $page['slug'])));
                                ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

    </div>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
