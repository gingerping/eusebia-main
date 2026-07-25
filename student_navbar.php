<?php
/**
 * Shared student navbar include.
 *
 * Requires (set by the including page before this include):
 *   $userdetails       - array from get_userdata()
 *
 * Optional (set by the including page before this include):
 *   $current_user_id   - falls back to $userdetails['id_student'] if not set
 *   $active_page        - one of: 'dashboard', 'submissions', 'changepass'
 *                          used to highlight the current item in the icon bar
 *
 * Edit this file to change the navbar on every page that includes it.
 * The notification bell (replacing "My Profile") shows enrollment/promotion
 * approve-reject updates from the admin, with an unread count badge.
 */
if (!isset($current_user_id)) {
    $current_user_id = $userdetails['id_student'] ?? '';
}
if (!isset($active_page)) {
    $active_page = '';
}

$notif_unread_count = $eusebia->get_unread_notification_count($current_user_id);
$notif_list = $eusebia->get_notifications($current_user_id, 8);

if (!function_exists('fb_notif_time_ago')) {
    function fb_notif_time_ago($datetime) {
        if (empty($datetime)) return '';
        $diff = time() - strtotime($datetime);
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        return date('M j, Y', strtotime($datetime));
    }
}
?>
<style>
    .fb-navbar {
        position: sticky;
        top: 0;
        z-index: 1030;
        background: linear-gradient(135deg, #0b2b5c 0%, #0f3b7a 100%);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        height: 56px;
        padding: 20px 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .fb-navbar-brand {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 1.1rem;
        color: #ffffff;
        text-decoration: none;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .fb-nav-icons {
        display: flex;
        align-items: center;
        height: 100%;
        gap: 2.5rem;
        margin: 0 auto;
    }
    .fb-nav-item {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        height: 100%;
        padding: 0 0.85rem;
        font-size: 1.4rem;
        color: rgba(255,255,255,0.65);
        text-decoration: none;
        transition: color 0.15s ease;
        border: none;
        background: none;
        cursor: pointer;
    }
    .fb-nav-label {
        font-size: 0.9rem;
        font-weight: 500;
        letter-spacing: -0.011em;
    }
    .fb-nav-item:hover {
        color: #ffffff;
    }
    .fb-nav-item.active {
        color: #ffffff;
    }
    .fb-nav-item.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 65%;
        height: 3px;
        border-radius: 3px 3px 0 0;
    }
    .fb-nav-spacer {
        flex-shrink: 0;
        width: 90px;
    }

    /* Notification bell */
    .fb-notif-badge {
        position: absolute;
        top: 6px;
        right: 2px;
        min-width: 17px;
        height: 17px;
        padding: 0 4px;
        border-radius: 999px;
        background: #e41e3f;
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        line-height: 17px;
        text-align: center;
        border: 2px solid #0b2b5c;
    }
    .fb-notif-panel {
        width: 340px;
        max-width: 90vw;
        max-height: 420px;
        overflow-y: auto;
        border: none;
        border-radius: 16px;
        box-shadow: 0 12px 28px rgba(0,0,0,0.18);
        padding: 0;
        margin-top: 10px;
    }
    .fb-notif-header {
        padding: 14px 18px;
        font-weight: 700;
        font-size: 1.05rem;
        color: #0b2b5c;
        border-bottom: 1px solid #eef1f6;
    }
    .fb-notif-empty {
        padding: 28px 18px;
        text-align: center;
        color: #8a96a3;
        font-size: 0.9rem;
    }
    .fb-notif-item {
        display: flex;
        gap: 12px;
        padding: 12px 18px;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid #f3f5f9;
    }
    .fb-notif-item.unread {
        background: #eef4ff;
    }
    .fb-notif-icon {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        color: #fff;
        background: #6c7a89;
    }
    .fb-notif-icon.ok { background: #28a745; }
    .fb-notif-icon.bad { background: #c0392b; }
    .fb-notif-body { flex: 1; min-width: 0; }
    .fb-notif-title {
        font-weight: 600;
        font-size: 0.88rem;
        color: #1a2c3e;
        margin-bottom: 2px;
    }
    .fb-notif-msg {
        font-size: 0.82rem;
        color: #5e7e9e;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .fb-notif-time {
        font-size: 0.75rem;
        color: #9aa8b5;
        margin-top: 4px;
    }

    @media (max-width: 768px) {
        .fb-navbar {
            flex-direction: column;
            align-items: flex-start;
            height: auto;
            padding: 0.6rem 1rem 0;
        }
        .fb-navbar-brand {
            padding-bottom: 0.5rem;
        }
        .fb-nav-spacer { display: none; }
        .fb-nav-icons { gap: 1rem; justify-content: space-evenly; width: 100%; height: 44px; margin: 0; }
        .fb-nav-item { width: 44px; padding: 0; }
        .fb-nav-label { display: none; }
    }
</style>

<nav class="fb-navbar">
    <a class="fb-navbar-brand" href="student_homepage.php">
        <i class="bi bi-mortarboard-fill me-1"></i> EPAMNHS
    </a>
    <div class="fb-nav-icons">
        <a class="fb-nav-item<?= $active_page === 'dashboard' ? ' active' : '' ?>" href="student_homepage.php" title="Dashboard">
            <i class="fas fa-home"></i><span class="fb-nav-label">Home</span>
        </a>
        <a class="fb-nav-item<?= $active_page === 'submissions' ? ' active' : '' ?>" href="my_submissions.php?id_student=<?= $current_user_id ?>" title="My Submissions">
            <i class="fas fa-file-alt"></i><span class="fb-nav-label">Submissions</span>
        </a>

        <div class="dropdown">
            <button class="fb-nav-item" id="notifBell" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                <i class="fas fa-bell"></i><span class="fb-nav-label">Notifications</span>
                <?php if ($notif_unread_count > 0): ?>
                    <span class="fb-notif-badge" id="notifBadge"><?= $notif_unread_count > 9 ? '9+' : $notif_unread_count ?></span>
                <?php endif; ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end fb-notif-panel" aria-labelledby="notifBell">
                <li class="fb-notif-header">Notifications</li>
                <?php if (empty($notif_list)): ?>
                    <li class="fb-notif-empty">No notifications yet.</li>
                <?php else: foreach ($notif_list as $n):
                    $type = $n['type'] ?? 'info';
                    $iconClass = $type === 'approved' ? 'fa-check' : ($type === 'rejected' ? 'fa-xmark' : 'fa-circle-info');
                    $bubbleClass = $type === 'approved' ? 'ok' : ($type === 'rejected' ? 'bad' : '');
                ?>
                    <li class="fb-notif-item<?= empty($n['is_read']) ? ' unread' : '' ?>">
                        <div class="fb-notif-icon <?= $bubbleClass ?>"><i class="fas <?= $iconClass ?>"></i></div>
                        <div class="fb-notif-body">
                            <div class="fb-notif-title"><?= htmlspecialchars($n['title']) ?></div>
                            <div class="fb-notif-msg"><?= htmlspecialchars($n['message']) ?></div>
                            <div class="fb-notif-time"><?= fb_notif_time_ago($n['created_at']) ?></div>
                        </div>
                    </li>
                <?php endforeach; endif; ?>
            </ul>
        </div>

        <a class="fb-nav-item<?= $active_page === 'changepass' ? ' active' : '' ?>" href="student_changepass.php?id_student=<?= $current_user_id ?>" title="Change Password">
            <i class="fas fa-key"></i><span class="fb-nav-label">Change Password</span>
        </a>
        <a class="fb-nav-item" href="logout.php" title="Logout">
            <i class="fas fa-sign-out-alt"></i><span class="fb-nav-label">Logout</span>
        </a>
    </div>
    <div class="fb-nav-spacer"></div>
</nav>

<script>
    (function () {
        var bell = document.getElementById('notifBell');
        if (!bell) return;
        bell.addEventListener('shown.bs.dropdown', function () {
            var badge = document.getElementById('notifBadge');
            if (badge) badge.remove();
            fetch('notifications_mark_read.php', { method: 'POST' }).catch(function () {});
        });
    })();
</script>