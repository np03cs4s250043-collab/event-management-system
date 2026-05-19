<aside class="sidebar">
    <div class="sidebar-header">
        <a href="<?= APP_URL ?>/index.php" class="logo">
            <span class="material-symbols-outlined" style="color:var(--primary)">confirmation_number</span>
            Eventify
        </a>
    </div>
    <nav>
        <?php foreach ($sidebarLinks as $link): ?>
        <a href="<?= h($link['url']) ?>" class="<?= !empty($link['active']) ? 'active' : '' ?>">
            <span class="material-symbols-outlined"><?= $link['icon'] ?></span>
            <?= h($link['label']) ?>
        </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
        <a href="<?= APP_URL ?>/index.php?page=logout" style="display:flex;align-items:center;gap:0.5rem;color:rgba(255,255,255,0.5);text-decoration:none;font-size:0.85rem">
            <span class="material-symbols-outlined">logout</span> Logout
        </a>
    </div>
<<<<<<< HEAD
</aside>
=======
</aside>
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
