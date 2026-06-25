<?php
session_start();
require_once 'config.php';

// ==========================================
// 1. KIỂM TRA SESSION & PHÂN QUYỀN
// ==========================================
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Bạn không có quyền truy cập!'); window.location.href='index.php';</script>"; exit;
}

$message = ''; $msgType = '';
$audio_dir  = 'uploads/audio/';  $image_dir  = 'uploads/images/';
$cat_dir    = 'uploads/categories/'; $album_dir  = 'uploads/albums/';
$banner_dir = 'uploads/banners/';    $artist_dir = 'uploads/artists/';
$mv_dir     = 'uploads/mv/';

foreach ([$audio_dir, $image_dir, $cat_dir, $album_dir, $banner_dir, $artist_dir, $mv_dir] as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0777, true);
}

// ==========================================
// 2. XỬ LÝ POST FORM
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- CHỦ ĐỀ ---
    if (isset($_POST['action_category'])) {
        $action = $_POST['action_category'];
        if ($action === 'add') {
            $img_path = '';
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
                $tmp_path = $cat_dir . time() . '_' . basename($_FILES['image_file']['name']);
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $tmp_path)) {
                    $img_path = $tmp_path;
                }
            }
            $pdo->prepare("INSERT INTO categories (name, image_url) VALUES (?, ?)")->execute([trim($_POST['name']), $img_path]);
            $message = "Thêm chủ đề thành công!"; $msgType = "success";
        } elseif ($action === 'edit') {
            $id = $_POST['category_id']; $query = "UPDATE categories SET name = ?"; $params = [trim($_POST['name'])];
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
                $img_path = $cat_dir . time() . '_' . basename($_FILES['image_file']['name']);
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $img_path)) { $query .= ", image_url = ?"; $params[] = $img_path; }
            }
            $query .= " WHERE id = ?"; $params[] = $id;
            $pdo->prepare($query)->execute($params);
            $message = "Cập nhật chủ đề thành công!"; $msgType = "success";
        } elseif ($action === 'delete') {
            $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$_POST['category_id']]);
            $message = "Đã xóa chủ đề!"; $msgType = "success";
        }
    }

    // --- ALBUM ---
    elseif (isset($_POST['action_album'])) {
        $action = $_POST['action_album'];
        if ($action === 'add') {
            $img_path = $album_dir . time() . '_' . basename($_FILES['image_file']['name']);
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $img_path)) {
                $pdo->prepare("INSERT INTO albums (title, category_id, image_url) VALUES (?, ?, ?)")->execute([trim($_POST['title']), $_POST['category_id'], $img_path]);
                $message = "Thêm Album thành công!"; $msgType = "success";
            }
        } elseif ($action === 'edit') {
            $id = $_POST['album_id']; $query = "UPDATE albums SET title = ?, category_id = ?"; $params = [trim($_POST['title']), $_POST['category_id']];
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
                $img_path = $album_dir . time() . '_' . basename($_FILES['image_file']['name']);
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $img_path)) { $query .= ", image_url = ?"; $params[] = $img_path; }
            }
            $query .= " WHERE id = ?"; $params[] = $id;
            $pdo->prepare($query)->execute($params);
            $message = "Cập nhật Album thành công!"; $msgType = "success";
        } elseif ($action === 'delete') {
            $pdo->prepare("DELETE FROM albums WHERE id = ?")->execute([$_POST['album_id']]);
            $message = "Đã xóa Album!"; $msgType = "success";
        }
    }

    // --- BÀI HÁT ---
    elseif (isset($_POST['action_song'])) {
        $action   = $_POST['action_song'];
        $lyrics   = $_POST['lyrics'] ?? '';
        $composer = trim($_POST['composer'] ?? '');
        $mood     = trim($_POST['mood'] ?? '');
        $status   = $_POST['status'] ?? 'approved';

        if ($action === 'add') {
            $title       = trim($_POST['title']);
            $artist      = trim($_POST['artist']);
            $category_id = $_POST['category_id'];
            $album_id    = !empty($_POST['album_id']) ? $_POST['album_id'] : null;

            $upload_err_map = [
                UPLOAD_ERR_INI_SIZE   => 'File vượt quá giới hạn upload_max_filesize trong php.ini',
                UPLOAD_ERR_FORM_SIZE  => 'File vượt quá MAX_FILE_SIZE trong form',
                UPLOAD_ERR_PARTIAL    => 'File chỉ upload được một phần (mạng yếu?)',
                UPLOAD_ERR_NO_TMP_DIR => 'Server thiếu thư mục tạm',
                UPLOAD_ERR_CANT_WRITE => 'Server không ghi được file (kiểm tra quyền thư mục)',
            ];

            // --- AUDIO ---
            $audio_path = '';
            $audio_err  = $_FILES['audio_file']['error'] ?? UPLOAD_ERR_NO_FILE;
            if ($audio_err === UPLOAD_ERR_OK) {
                $audio_path = $audio_dir . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['audio_file']['name']));
                if (!move_uploaded_file($_FILES['audio_file']['tmp_name'], $audio_path)) {
                    $audio_path = '';
                    $message = "Lỗi ghi file audio. Thư mục '{$audio_dir}' có thể thiếu quyền ghi.";
                    $msgType  = 'error';
                }
            } elseif ($audio_err !== UPLOAD_ERR_NO_FILE) {
                $message = "Lỗi upload audio: " . ($upload_err_map[$audio_err] ?? "Code=$audio_err");
                $msgType = 'error';
            }
            // Fallback: URL trực tiếp
            if (empty($audio_path) && $msgType !== 'error') {
                $audio_url_input = trim($_POST['audio_url'] ?? '');
                if (!empty($audio_url_input)) {
                    $audio_path = $audio_url_input;
                } else {
                    $message = "Vui lòng chọn file audio hoặc nhập URL audio.";
                    $msgType  = 'error';
                }
            }

            // --- ẢNH BÌA ---
            $image_path = '';
            if ($msgType !== 'error') {
                $image_err = $_FILES['image_file']['error'] ?? UPLOAD_ERR_NO_FILE;
                if ($image_err === UPLOAD_ERR_OK) {
                    $image_path = $image_dir . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['image_file']['name']));
                    if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $image_path)) {
                        $image_path = '';
                        $message = "Lỗi ghi file ảnh bìa. Thư mục '{$image_dir}' có thể thiếu quyền ghi.";
                        $msgType  = 'error';
                    }
                } elseif ($image_err !== UPLOAD_ERR_NO_FILE) {
                    $message = "Lỗi upload ảnh: " . ($upload_err_map[$image_err] ?? "Code=$image_err");
                    $msgType  = 'error';
                }
                // Fallback: URL hoặc placeholder
                if (empty($image_path) && $msgType !== 'error') {
                    $image_url_input = trim($_POST['image_url'] ?? '');
                    $image_path = !empty($image_url_input)
                        ? $image_url_input
                        : 'https://placehold.co/300x300/1e2730/FFF?text=' . urlencode($title);
                }
            }

            // --- INSERT DB ---
            if ($msgType !== 'error' && !empty($audio_path)) {
                $pdo->prepare("INSERT INTO songs (title, artist, composer, audio_url, image_url, category_id, album_id, lyrics, mood, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
                    ->execute([$title, $artist, $composer, $audio_path, $image_path, $category_id, $album_id, $lyrics, $mood, $status]);
                $message = "Thêm bài hát \"{$title}\" thành công!";
                $msgType  = 'success';
            }

        } elseif ($action === 'edit') {
            $song_id = $_POST['song_id']; $title = trim($_POST['title']); $artist = trim($_POST['artist']);
            $category_id = $_POST['category_id']; $album_id = !empty($_POST['album_id']) ? $_POST['album_id'] : null;
            $query = "UPDATE songs SET title=?, artist=?, composer=?, category_id=?, album_id=?, lyrics=?, mood=?, status=?";
            $params = [$title, $artist, $composer, $category_id, $album_id, $lyrics, $mood, $status];
            if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] == 0) {
                $audio_path = $audio_dir . time() . '_' . basename($_FILES['audio_file']['name']);
                if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $audio_path)) { $query .= ", audio_url=?"; $params[] = $audio_path; }
            }
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
                $image_path = $image_dir . time() . '_' . basename($_FILES['image_file']['name']);
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $image_path)) { $query .= ", image_url=?"; $params[] = $image_path; }
            }
            $query .= " WHERE id = ?"; $params[] = $song_id;
            $pdo->prepare($query)->execute($params);
            $message = "Cập nhật bài hát thành công!"; $msgType = "success";
        } elseif ($action === 'delete') {
            $pdo->prepare("DELETE FROM songs WHERE id = ?")->execute([$_POST['song_id']]);
            $message = "Đã xóa bài hát!"; $msgType = "success";
        } elseif ($action === 'approve') {
            $pdo->prepare("UPDATE songs SET status='approved' WHERE id=?")->execute([$_POST['song_id']]);
            $message = "Đã duyệt bài hát!"; $msgType = "success";
        } elseif ($action === 'reject') {
            $pdo->prepare("UPDATE songs SET status='rejected' WHERE id=?")->execute([$_POST['song_id']]);
            $message = "Đã từ chối bài hát!"; $msgType = "success";
        }
    }

    // --- BANNER ---
    elseif (isset($_POST['action_banner'])) {
        if ($_POST['action_banner'] === 'add') {
            $img_path = $banner_dir . time() . '_' . basename($_FILES['image_file']['name']);
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $img_path)) {
                $pdo->prepare("INSERT INTO banners (title, image_url) VALUES (?, ?)")->execute([$_POST['title'], $img_path]);
                $message = "Đã thêm Banner!"; $msgType = "success";
            }
        } elseif ($_POST['action_banner'] === 'delete') {
            $pdo->prepare("DELETE FROM banners WHERE id = ?")->execute([$_POST['banner_id']]);
            $message = "Đã xóa Banner!"; $msgType = "success";
        }
    }

    // --- NGHỆ SĨ ---
    elseif (isset($_POST['action_artist'])) {
        $action = $_POST['action_artist'];
        if ($action === 'add') {
            $img_path = $artist_dir . time() . '_' . basename($_FILES['image_file']['name']);
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $img_path)) {
                $pdo->prepare("INSERT INTO artists (name, bio, facebook, youtube, image_url) VALUES (?, ?, ?, ?, ?)")
                    ->execute([$_POST['name'], $_POST['bio'] ?? '', $_POST['facebook'] ?? '', $_POST['youtube'] ?? '', $img_path]);
                $message = "Đã thêm Nghệ sĩ!"; $msgType = "success";
            }
        } elseif ($action === 'edit') {
            $id = $_POST['artist_id'];
            $query = "UPDATE artists SET name=?, bio=?, facebook=?, youtube=?"; $params = [$_POST['name'], $_POST['bio'] ?? '', $_POST['facebook'] ?? '', $_POST['youtube'] ?? ''];
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
                $img_path = $artist_dir . time() . '_' . basename($_FILES['image_file']['name']);
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $img_path)) { $query .= ", image_url=?"; $params[] = $img_path; }
            }
            $query .= " WHERE id=?"; $params[] = $id;
            $pdo->prepare($query)->execute($params);
            $message = "Cập nhật Nghệ sĩ thành công!"; $msgType = "success";
        } elseif ($action === 'delete') {
            $pdo->prepare("DELETE FROM artists WHERE id = ?")->execute([$_POST['artist_id']]);
            $message = "Đã xóa Nghệ sĩ!"; $msgType = "success";
        }
    }

    // --- NGƯỜI DÙNG ---
    elseif (isset($_POST['action_user'])) {
        $action = $_POST['action_user'];
        if ($action === 'edit_role') {
            $pdo->prepare("UPDATE users SET role=? WHERE id=?")->execute([$_POST['role'], $_POST['user_id']]);
            $message = "Đã cập nhật quyền người dùng!"; $msgType = "success";
        } elseif ($action === 'ban') {
            $pdo->prepare("UPDATE users SET status='banned' WHERE id=?")->execute([$_POST['user_id']]);
            $message = "Đã khóa tài khoản!"; $msgType = "success";
        } elseif ($action === 'unban') {
            $pdo->prepare("UPDATE users SET status='active' WHERE id=?")->execute([$_POST['user_id']]);
            $message = "Đã mở khóa tài khoản!"; $msgType = "success";
        } elseif ($action === 'delete') {
            $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$_POST['user_id']]);
            $message = "Đã xóa người dùng!"; $msgType = "success";
        }
    }

    // --- KHUYẾN MÃI ---
    elseif (isset($_POST['action_promo'])) {
        $action = $_POST['action_promo'];
        if ($action === 'add') {
            $pdo->prepare("INSERT INTO promotions (code, discount_percent, description, expires_at) VALUES (?, ?, ?, ?)")
                ->execute([strtoupper(trim($_POST['code'])), (int)$_POST['discount_percent'], trim($_POST['description']), $_POST['expires_at']]);
            $message = "Thêm mã khuyến mãi thành công!"; $msgType = "success";
        } elseif ($action === 'delete') {
            $pdo->prepare("DELETE FROM promotions WHERE id=?")->execute([$_POST['promo_id']]);
            $message = "Đã xóa mã khuyến mãi!"; $msgType = "success";
        }
    }

    // --- CẤU HÌNH SECTIONS TRANG CHỦ ---
    elseif (isset($_POST['action_config'])) {
        // Tự động tạo bảng site_config nếu chưa tồn tại
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS site_config (
                cfg_key VARCHAR(100) PRIMARY KEY,
                cfg_value TEXT NOT NULL
            )");
        } catch (PDOException $e) { /* ignore */ }

        $keys_to_save = [
            'bxh_col1_title', 'bxh_col1_songs',
            'bxh_col2_title', 'bxh_col2_songs',
            'bxh_col3_title', 'bxh_col3_songs',
        ];
        $stmt = $pdo->prepare("INSERT INTO site_config (cfg_key, cfg_value) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE cfg_value = VALUES(cfg_value)");
        foreach ($keys_to_save as $k) {
            $v = trim($_POST[$k] ?? '');
            $stmt->execute([$k, $v]);
        }
        // Lưu dynamic sections JSON
        if (isset($_POST['homepage_sections_json'])) {
            $sections_json = $_POST['homepage_sections_json'];
            // Validate JSON
            $decoded = json_decode($sections_json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $stmt->execute(['homepage_sections_json', $sections_json]);
            }
        }
        $message = "Đã lưu cấu hình trang chủ!"; $msgType = "success";
    }

    // --- XÓA HÀNG LOẠT (BULK DELETE) ---
    elseif (isset($_POST['action_bulk'])) {
        $table  = $_POST['bulk_table'] ?? '';
        $ids_raw = $_POST['bulk_ids'] ?? '';
        $ids = array_filter(array_map('intval', explode(',', $ids_raw)));
        if (!empty($ids) && in_array($table, ['songs','albums','categories','banners','artists'])) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            if ($table === 'categories') {
                // Xóa cascade: albums và songs trong các categories này
                $pdo->prepare("DELETE FROM songs WHERE category_id IN ($placeholders)")->execute($ids);
                $pdo->prepare("DELETE FROM albums WHERE category_id IN ($placeholders)")->execute($ids);
            } elseif ($table === 'albums') {
                // Xóa songs thuộc albums này
                $pdo->prepare("DELETE FROM songs WHERE album_id IN ($placeholders)")->execute($ids);
            }
            $pdo->prepare("DELETE FROM $table WHERE id IN ($placeholders)")->execute($ids);
            $count = count($ids);
            $message = "Đã xóa $count mục khỏi bảng $table!"; $msgType = "success";
        }
    }

    // --- RESET TOÀN BỘ BẢNG ---
    elseif (isset($_POST['action_reset'])) {
        $table = $_POST['reset_table'] ?? '';
        $confirm_text = trim($_POST['reset_confirm'] ?? '');
        if ($confirm_text === 'XAC NHAN XOA' && in_array($table, ['songs','albums','categories','banners','artists'])) {
            if ($table === 'categories') {
                $pdo->exec("DELETE FROM songs");
                $pdo->exec("DELETE FROM albums");
                $pdo->exec("DELETE FROM categories");
            } elseif ($table === 'albums') {
                // Đặt album_id = NULL cho songs thuộc albums này
                $pdo->exec("UPDATE songs SET album_id = NULL");
                $pdo->exec("DELETE FROM albums");
            } else {
                $pdo->exec("DELETE FROM $table");
            }
            $message = "Đã xóa toàn bộ dữ liệu bảng $table!"; $msgType = "success";
        } else {
            $message = "Xác nhận không đúng, không xóa dữ liệu."; $msgType = "danger";
        }
    }
}

// ==========================================
// 2b. TRẢ VỀ JSON NẾU LÀ AJAX REQUEST
// ==========================================
if (isset($_POST['is_ajax'])) {
    // Đọc lại dữ liệu mới nhất từ DB
    $aj_cats  = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
    $aj_albs  = $pdo->query("SELECT * FROM albums ORDER BY id DESC")->fetchAll();
    $aj_songs = $pdo->query("SELECT s.*, c.name as category_name, a.title as album_title FROM songs s LEFT JOIN categories c ON s.category_id = c.id LEFT JOIN albums a ON s.album_id = a.id ORDER BY s.id DESC")->fetchAll();
    $aj_bans  = $pdo->query("SELECT * FROM banners ORDER BY id DESC")->fetchAll();
    $aj_arts  = $pdo->query("SELECT * FROM artists ORDER BY id DESC")->fetchAll();
    try { $aj_users = $pdo->query("SELECT id, username, email, role, status, created_at FROM users ORDER BY id DESC")->fetchAll(); }
    catch (PDOException $e) { $aj_users = []; }
    try { $aj_promos = $pdo->query("SELECT * FROM promotions ORDER BY id DESC")->fetchAll(); }
    catch (PDOException $e) { $aj_promos = []; }
    $aj_cfg = [];
    try {
        $cfg_rows = $pdo->query("SELECT cfg_key, cfg_value FROM site_config")->fetchAll();
        foreach ($cfg_rows as $r) $aj_cfg[$r['cfg_key']] = $r['cfg_value'];
    } catch (PDOException $e) { $aj_cfg = []; }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status'      => $msgType === 'success' ? 'success' : ($msgType === 'danger' ? 'error' : 'success'),
        'message'     => $message,
        'categories'  => $aj_cats,
        'albums'      => $aj_albs,
        'songs'       => $aj_songs,
        'banners'     => $aj_bans,
        'artists'     => $aj_arts,
        'users'       => $aj_users,
        'promos'      => $aj_promos,
        'site_config' => $aj_cfg,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ==========================================
// 3. ĐỌC DỮ LIỆU
// ==========================================
$categories  = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
$albums      = $pdo->query("SELECT * FROM albums ORDER BY id DESC")->fetchAll();
$songs       = $pdo->query("SELECT s.*, c.name as category_name, a.title as album_title FROM songs s LEFT JOIN categories c ON s.category_id = c.id LEFT JOIN albums a ON s.album_id = a.id ORDER BY s.id DESC")->fetchAll();
$banners     = $pdo->query("SELECT * FROM banners ORDER BY id DESC")->fetchAll();
$artists     = $pdo->query("SELECT * FROM artists ORDER BY id DESC")->fetchAll();
$total_views = (int) array_sum(array_column($songs, 'views'));

// Users – thêm cột status nếu chưa tồn tại (graceful fallback)
try {
    $users = $pdo->query("SELECT id, username, email, role, status, created_at FROM users ORDER BY id DESC")->fetchAll();
} catch (PDOException $e) {
    $users = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC")->fetchAll();
    foreach ($users as &$u) $u['status'] = 'active';
    unset($u);
}

// Promotions – graceful fallback
try {
    $promotions = $pdo->query("SELECT * FROM promotions ORDER BY id DESC")->fetchAll();
} catch (PDOException $e) {
    $promotions = [];
}

// Site config – graceful fallback
$site_config = [];
try {
    $cfg_rows = $pdo->query("SELECT cfg_key, cfg_value FROM site_config")->fetchAll();
    foreach ($cfg_rows as $r) $site_config[$r['cfg_key']] = $r['cfg_value'];
} catch (PDOException $e) {
    $site_config = [];
}

// Thống kê
$pending_songs = array_filter($songs, fn($s) => ($s['status'] ?? 'approved') === 'pending');
$total_pending = count($pending_songs);
$top_songs     = array_slice(array_filter($songs, fn($s) => ($s['views'] ?? 0) > 0), 0, 5);
usort($top_songs, fn($a, $b) => ($b['views'] ?? 0) <=> ($a['views'] ?? 0));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị viên — NCT Music</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --bg-main: #1a2128; --bg-sidebar: #161d24; --bg-card: #1e2730;
            --bg-hover: rgba(255,255,255,0.06); --accent-color: #00d4d4;
            --accent-orange: #ff8c00; --accent-green: #22c55e; --accent-red: #e05c6d;
            --accent-purple: #a78bfa;
            --text-main: #ffffff; --text-sub: #8a9bb0; --border-color: rgba(255,255,255,0.07);
        }
        * { box-sizing: border-box; }
        body { background-color: var(--bg-main); color: var(--text-main); font-family: 'Inter', sans-serif; margin: 0; overflow-x: hidden; }
        a { text-decoration: none; color: inherit; cursor: pointer; }
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #3a4a5a; border-radius: 4px; }

        /* ===== SIDEBAR ===== */
        #sidebar {
            width: 230px; height: 100vh; position: fixed; top: 0; left: 0;
            background-color: var(--bg-sidebar); border-right: 1px solid var(--border-color);
            display: flex; flex-direction: column; overflow-y: auto; z-index: 100;
        }
        .logo-box {
            padding: 20px 20px 18px; display: flex; align-items: center; gap: 10px;
            cursor: pointer; border-bottom: 1px solid var(--border-color); margin-bottom: 8px;
        }
        .logo-icon { width: 36px; height: 36px; background: var(--accent-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #000; font-size: 18px; font-weight: 900; }
        .logo-text .brand { font-size: 16px; font-weight: 800; color: white; letter-spacing: 0.5px; display: block; }
        .logo-text .tagline { font-size: 10px; color: var(--text-sub); font-weight: 400; display: block; }
        .menu-section { padding: 4px 0 12px; }
        .menu-section-title { padding: 6px 20px 4px; font-size: 10px; font-weight: 700; color: #4a6070; text-transform: uppercase; letter-spacing: 1.2px; }
        .menu-list { list-style: none; padding: 0 10px; margin: 0; }
        .menu-list li a { display: flex; align-items: center; gap: 12px; padding: 9px 12px; color: var(--text-sub); font-size: 13px; font-weight: 500; border-radius: 8px; transition: 0.15s; }
        .menu-list li a i { font-size: 16px; width: 20px; text-align: center; }
        .menu-list li a:hover { background-color: var(--bg-hover); color: white; }
        .menu-list li a.active { background-color: rgba(0,212,212,0.12); color: var(--accent-color); }
        .menu-list li a.active i { color: var(--accent-color); }
        .nav-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 18px; background: var(--accent-red); color: white; border-radius: 9px; font-size: 9px; font-weight: 700; margin-left: auto; padding: 0 4px; }
        .admin-badge { display: inline-block; font-size: 9px; font-weight: 700; color: var(--accent-color); border: 1px solid var(--accent-color); border-radius: 3px; padding: 1px 5px; margin-left: auto; letter-spacing: 0.3px; }
        .sidebar-bottom { margin-top: auto; padding: 16px 14px; border-top: 1px solid var(--border-color); }
        .btn-sidebar-back { background-color: rgba(255,255,255,0.08); color: white; border: 1px solid rgba(255,255,255,0.12); padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; width: 100%; cursor: pointer; transition: 0.2s; }
        .btn-sidebar-back:hover { background-color: rgba(255,255,255,0.14); }

        /* ===== HEADER ===== */
        #main-wrapper { margin-left: 230px; min-height: 100vh; }
        #top-header { display: flex; justify-content: space-between; align-items: center; padding: 0 32px; height: 64px; position: sticky; top: 0; background-color: rgba(26,33,40,0.97); backdrop-filter: blur(20px); border-bottom: 1px solid var(--border-color); z-index: 90; }
        .search-bar { background-color: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 8px 16px; display: flex; align-items: center; gap: 10px; width: 320px; transition: 0.2s; }
        .search-bar:focus-within { border-color: rgba(0,212,212,0.5); }
        .search-bar input { background: none; border: none; color: white; outline: none; width: 100%; font-size: 13px; }
        .search-bar input::placeholder { color: var(--text-sub); }
        .search-bar i { color: var(--text-sub); font-size: 14px; }
        .header-right { display: flex; align-items: center; gap: 10px; }
        .header-username { font-size: 13px; color: var(--text-sub); }
        .header-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--accent-color); display: flex; align-items: center; justify-content: center; color: #000; font-size: 13px; font-weight: 800; border: 2px solid rgba(0,212,212,0.4); }

        /* ===== CONTENT ===== */
        .content-area { padding: 0 32px 40px; }
        .section-title { font-size: 22px; font-weight: 800; margin: 32px 0 18px; color: white; letter-spacing: -0.3px; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin: 28px 0 16px; }
        .section-header h3 { font-size: 18px; font-weight: 700; margin: 0; color: white; }

        /* ===== STAT CARDS ===== */
        .stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; padding: 20px; display: flex; align-items: center; gap: 16px; transition: 0.2s; }
        .stat-card:hover { border-color: rgba(0,212,212,0.25); transform: translateY(-2px); }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .stat-icon.cyan { background: rgba(0,212,212,0.12); color: var(--accent-color); }
        .stat-icon.orange { background: rgba(255,140,0,0.12); color: var(--accent-orange); }
        .stat-icon.green { background: rgba(34,197,94,0.12); color: var(--accent-green); }
        .stat-icon.red { background: rgba(224,92,109,0.12); color: var(--accent-red); }
        .stat-icon.purple { background: rgba(167,139,250,0.12); color: var(--accent-purple); }
        .stat-card h3 { font-size: 26px; font-weight: 800; margin: 0 0 2px; color: white; }
        .stat-card p { font-size: 12px; color: var(--text-sub); margin: 0; }
        .stat-trend { font-size: 11px; color: var(--accent-green); margin-top: 3px; }

        /* ===== TABLES ===== */
        .admin-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; overflow: hidden; margin-bottom: 28px; }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th { background: rgba(0,0,0,0.2); color: var(--text-sub); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; padding: 14px 20px; border-bottom: 1px solid var(--border-color); }
        .admin-table td { padding: 12px 20px; border-bottom: 1px solid var(--border-color); color: white; font-size: 13px; vertical-align: middle; }
        .admin-table tbody tr:hover td { background: var(--bg-hover); }
        .admin-table tbody tr:last-child td { border-bottom: none; }

        .song-table { width: 100%; color: var(--text-main); border-collapse: separate; border-spacing: 0; }
        .song-table th { color: var(--text-sub); font-size: 11px; font-weight: 600; text-transform: uppercase; padding: 12px 15px; border-bottom: 1px solid #2a3540; letter-spacing: 0.5px; }
        .song-table td { padding: 10px 15px; vertical-align: middle; border-bottom: 1px solid #232d36; }
        .song-table tbody tr:hover { background: rgba(255,255,255,0.04); }

        /* ===== TOPIC / ALBUM CARDS ===== */
        .topic-admin-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 10px; height: 96px; position: relative; overflow: hidden; cursor: pointer; transition: 0.2s; display: flex; align-items: flex-start; padding: 14px; }
        .topic-admin-card:hover { border-color: rgba(0,212,212,0.35); transform: scale(1.02); }
        .topic-admin-card h5 { font-size: 14px; font-weight: 700; color: white; margin: 0; position: relative; z-index: 2; flex: 1; }
        .topic-admin-card .song-count { font-size: 11px; color: var(--text-sub); position: absolute; bottom: 12px; left: 14px; z-index: 2; }
        .topic-admin-card img { width: 60px; height: 60px; position: absolute; bottom: -8px; right: -10px; transform: rotate(20deg); object-fit: cover; border-radius: 4px; opacity: 0.6; }
        .topic-action-btns { position: absolute; top: 8px; right: 8px; display: flex; gap: 4px; opacity: 0; transition: 0.2s; z-index: 10; }
        .topic-admin-card:hover .topic-action-btns { opacity: 1; }

        .square-card { min-width: 0; cursor: pointer; }
        .square-img-box { width: 100%; aspect-ratio: 1; border-radius: 8px; overflow: hidden; margin-bottom: 10px; position: relative; background: var(--bg-card); border: 1px solid var(--border-color); }
        .square-img-box img { width: 100%; height: 100%; object-fit: cover; transition: 0.3s; }
        .square-card:hover .square-img-box img { filter: brightness(0.6); }
        .square-card h6 { font-size: 13px; font-weight: 600; color: white; margin: 0 0 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .square-card p { font-size: 11px; color: var(--text-sub); margin: 0; }
        .album-action-btns { position: absolute; bottom: 8px; right: 8px; display: flex; gap: 4px; opacity: 0; transition: 0.2s; z-index: 5; }
        .square-card:hover .album-action-btns { opacity: 1; }

        /* ===== BUTTONS ===== */
        .btn-cyan { background: var(--accent-color); color: #000; font-weight: 700; border-radius: 30px; padding: 8px 20px; border: none; font-size: 13px; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-cyan:hover { opacity: 0.88; transform: scale(1.03); }
        .btn-outline-admin { background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.15); color: white; border-radius: 20px; padding: 7px 16px; font-size: 12px; font-weight: 600; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-outline-admin:hover { background: rgba(255,255,255,0.12); }
        .btn-icon-action { width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; border: 1px solid var(--border-color); background: rgba(0,0,0,0.5); color: white; cursor: pointer; transition: 0.15s; }
        .btn-icon-edit:hover { border-color: #ffc107; color: #ffc107; background: rgba(255,193,7,0.15); }
        .btn-icon-del:hover  { border-color: var(--accent-red); color: var(--accent-red); background: rgba(224,92,109,0.15); }
        .btn-icon-ok:hover   { border-color: var(--accent-green); color: var(--accent-green); background: rgba(34,197,94,0.15); }
        .btn-icon-ban:hover  { border-color: var(--accent-orange); color: var(--accent-orange); background: rgba(255,140,0,0.15); }

        /* ===== BADGES ===== */
        .badge-role { display: inline-block; font-size: 10px; font-weight: 700; border-radius: 4px; padding: 2px 7px; }
        .badge-role.admin   { background: rgba(0,212,212,0.15); color: var(--accent-color); }
        .badge-role.user    { background: rgba(138,155,176,0.15); color: var(--text-sub); }
        .badge-role.artist  { background: rgba(167,139,250,0.15); color: var(--accent-purple); }
        .badge-role.manager { background: rgba(255,140,0,0.15); color: var(--accent-orange); }
        .badge-status { display: inline-block; font-size: 10px; font-weight: 700; border-radius: 4px; padding: 2px 7px; }
        .badge-status.approved { background: rgba(34,197,94,0.15); color: var(--accent-green); }
        .badge-status.pending  { background: rgba(255,140,0,0.15); color: var(--accent-orange); }
        .badge-status.rejected { background: rgba(224,92,109,0.15); color: var(--accent-red); }
        .badge-status.active   { background: rgba(34,197,94,0.15); color: var(--accent-green); }
        .badge-status.banned   { background: rgba(224,92,109,0.15); color: var(--accent-red); }

        /* ===== BREADCRUMB ===== */
        .admin-breadcrumb { font-size: 13px; color: var(--text-sub); margin: 28px 0 6px; display: flex; align-items: center; gap: 6px; }
        .admin-breadcrumb span { cursor: pointer; transition: 0.2s; }
        .admin-breadcrumb span:hover { color: white; }
        .admin-breadcrumb .active { color: white; font-weight: 600; pointer-events: none; }

        /* ===== PAGINATION ===== */
        .pagination { gap: 4px; margin: 0; }
        .page-link { background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-sub); border-radius: 8px; padding: 7px 13px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.15s; }
        .page-link:hover { background: var(--bg-hover); color: white; }
        .page-item.active .page-link { background: var(--accent-color); border-color: var(--accent-color); color: #000; }
        .page-item.disabled .page-link { opacity: 0.35; cursor: not-allowed; }

        /* ===== LOSSLESS BADGE ===== */
        .lossless-badge { font-size: 9px; font-weight: 700; color: var(--accent-color); border: 1px solid var(--accent-color); border-radius: 3px; padding: 1px 4px; letter-spacing: 0.3px; }

        /* ===== ALERT ===== */
        .nct-alert { background: rgba(0,212,212,0.1); border: 1px solid rgba(0,212,212,0.25); color: white; border-radius: 10px; padding: 12px 18px; font-size: 13px; display: flex; align-items: center; gap: 10px; margin-bottom: 24px; }
        .nct-alert.error { background: rgba(224,92,109,0.1); border-color: rgba(224,92,109,0.3); }

        /* ===== MODALS ===== */
        .modal-content { background-color: #1e2a35; color: white; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 20px 60px rgba(0,0,0,0.5); }
        .modal-header { border-bottom: 1px solid var(--border-color); padding: 22px 24px 16px; }
        .modal-body { padding: 20px 24px; }
        .modal-footer { border-top: 1px solid var(--border-color); padding: 14px 24px; }
        .form-label { font-size: 12px; font-weight: 600; color: var(--text-sub); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .form-control, .form-select { background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.12); color: white; border-radius: 8px; padding: 11px 14px; font-size: 13px; transition: 0.2s; }
        .form-control:focus, .form-select:focus { background: rgba(255,255,255,0.1); border-color: rgba(0,212,212,0.5); color: white; box-shadow: 0 0 0 3px rgba(0,212,212,0.08); }
        .form-control option, .form-select option { background: #1e2a35; }
        .form-control::placeholder { color: var(--text-sub); }
        .btn-close { filter: invert(1) brightness(0.6); }

        /* ===== CHART WRAPPER ===== */
        .chart-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; padding: 22px; margin-bottom: 24px; }
        .chart-card h4 { font-size: 14px; font-weight: 700; color: white; margin-bottom: 18px; }

        /* ===== QUICK ACTIONS BAR ===== */
        .quick-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px; }

        /* ===== PENDING BANNER ===== */
        .pending-banner { background: rgba(255,140,0,0.1); border: 1px solid rgba(255,140,0,0.3); border-radius: 10px; padding: 12px 18px; display: flex; align-items: center; gap: 12px; margin-bottom: 20px; font-size: 13px; }
    </style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<aside id="sidebar">
    <div class="logo-box" onclick="renderDashboard()">
        <div class="logo-icon"><i class="bi bi-soundwave"></i></div>
        <div class="logo-text">
            <span class="brand">NCT Admin</span>
            <span class="tagline">Bảng quản trị</span>
        </div>
    </div>

    <div class="menu-section">
        <div class="menu-section-title">Tổng quan</div>
        <ul class="menu-list">
            <li><a onclick="setNav('db'); renderDashboard()" class="active" id="nav-db">
                <i class="bi bi-grid-1x2-fill"></i> Tổng quan
            </a></li>
            <li><a onclick="setNav('stats'); renderStats()" id="nav-stats">
                <i class="bi bi-bar-chart-line-fill"></i> Thống kê
                <span class="admin-badge">📊</span>
            </a></li>
        </ul>
    </div>

    <div class="menu-section">
        <div class="menu-section-title">Nội dung âm nhạc</div>
        <ul class="menu-list">
            <li><a onclick="setNav('music'); renderMusic()" id="nav-music">
                <i class="bi bi-music-note-list"></i> Bài hát & Album
            </a></li>
            <li><a onclick="setNav('pending'); renderPending()" id="nav-pending">
                <i class="bi bi-clock-history"></i> Chờ duyệt
                <?php if ($total_pending > 0): ?>
                    <span class="nav-badge"><?= $total_pending ?></span>
                <?php endif; ?>
            </a></li>
            <li><a onclick="setNav('artist'); renderArtists()" id="nav-artist">
                <i class="bi bi-person-badge"></i> Nghệ sĩ
            </a></li>
        </ul>
    </div>

    <div class="menu-section">
        <div class="menu-section-title">Người dùng</div>
        <ul class="menu-list">
            <li><a onclick="setNav('users'); renderUsers()" id="nav-users">
                <i class="bi bi-people-fill"></i> Quản lý tài khoản
            </a></li>
        </ul>
    </div>

    <div class="menu-section">
        <div class="menu-section-title">Giao diện & Kinh doanh</div>
        <ul class="menu-list">
            <li><a onclick="setNav('ui'); renderUIConfig()" id="nav-ui">
                <i class="bi bi-images"></i> Banner & Giao diện
                <span class="admin-badge">UI</span>
            </a></li>
            <li><a onclick="setNav('promo'); renderPromo()" id="nav-promo">
                <i class="bi bi-tag-fill"></i> Khuyến mãi
            </a></li>
        </ul>
    </div>

    <div class="menu-section">
        <div class="menu-section-title">Thao tác nhanh</div>
        <ul class="menu-list">
            <li><a onclick="openAddSongModal()">
                <i class="bi bi-plus-circle"></i> Thêm bài hát
            </a></li>
            <li><a onclick="openAddCatModal()">
                <i class="bi bi-folder-plus"></i> Thêm chủ đề
            </a></li>
        </ul>
    </div>

    <div class="sidebar-bottom">
        <p style="font-size:12px;color:var(--text-sub);margin-bottom:10px;">
            Xin chào, <strong class="text-white"><?= htmlspecialchars($_SESSION['username']) ?></strong>
        </p>
        <button class="btn-sidebar-back" onclick="window.location.href='index.php'">
            <i class="bi bi-arrow-left me-1"></i> Về trang chủ
        </button>
    </div>
</aside>

<!-- ===== MAIN ===== -->
<main id="main-wrapper">
    <header id="top-header">
        <div>
            <div class="search-bar">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Tìm kiếm bài hát, nghệ sĩ..." oninput="handleGlobalSearch(this.value)">
            </div>
        </div>
        <div class="header-right">
            <span class="header-username">Xin chào, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
            <div class="header-avatar">A</div>
            <a href="logout.php" style="font-size:12px;color:var(--text-sub);padding:6px 12px;border:1px solid var(--border-color);border-radius:20px;transition:0.2s;"
               onmouseover="this.style.color='white'" onmouseout="this.style.color='var(--text-sub)'">
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
            </a>
        </div>
    </header>

    <div class="content-area" id="admin-content">
        <div class="text-center py-5">
            <div class="spinner-border" style="color:var(--accent-color);"></div>
        </div>
    </div>
</main>

<!-- ==================== MODALS ==================== -->

<!-- MODAL: CHỦ ĐỀ -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="catModalTitle">Thêm chủ đề</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="admin.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action_category" id="cat_action" value="add">
                    <input type="hidden" name="category_id" id="cat_id">
                    <div class="mb-3">
                        <label class="form-label">Tên chủ đề</label>
                        <input type="text" class="form-control" name="name" id="cat_name" placeholder="VD: Rap Việt, Nhạc Hàn..." required>
                    </div>
                    <div>
                        <label class="form-label">Hình ảnh đại diện</label>
                        <input type="file" class="form-control" name="image_file" id="cat_file" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-cyan w-100 justify-content-center"><i class="bi bi-check2-circle"></i> Xác nhận</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: ALBUM -->
<div class="modal fade" id="albumModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="albModalTitle">Tạo Album</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="admin.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action_album" id="alb_action" value="add">
                    <input type="hidden" name="album_id" id="alb_id">
                    <input type="hidden" name="category_id" id="alb_cat_id">
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề Album</label>
                        <input type="text" class="form-control" name="title" id="alb_title" placeholder="Tên album..." required>
                    </div>
                    <div class="mb-3" id="alb_cat_select_group">
                        <label class="form-label">Thuộc chủ đề</label>
                        <select class="form-select" name="category_id" id="alb_select_cat">
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Ảnh bìa Album</label>
                        <input type="file" class="form-control" name="image_file" id="alb_file" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-cyan w-100 justify-content-center"><i class="bi bi-check2-circle"></i> Lưu Album</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: BÀI HÁT (đầy đủ) -->
<div class="modal fade" id="songModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="songModalTitle">Thêm bài hát</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="admin.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action_song" id="song_action" value="add">
                    <input type="hidden" name="song_id" id="song_id">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Tên bài hát</label>
                            <input type="text" class="form-control" name="title" id="song_title" placeholder="Tiêu đề bài hát..." required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Ca sĩ / Nghệ sĩ</label>
                            <input type="text" class="form-control" name="artist" id="song_artist" placeholder="Tên ca sĩ..." required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Nhạc sĩ / Sáng tác</label>
                            <input type="text" class="form-control" name="composer" id="song_composer" placeholder="Tên nhạc sĩ...">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Tâm trạng</label>
                            <select class="form-select" name="mood" id="song_mood">
                                <option value="">-- Chọn tâm trạng --</option>
                                <option value="vui">Vui vẻ</option>
                                <option value="buon">Buồn bã</option>
                                <option value="tinh_cam">Tình cảm</option>
                                <option value="nang_dong">Năng động</option>
                                <option value="thu_gian">Thư giãn</option>
                                <option value="co_vu">Cổ vũ</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Chủ đề</label>
                            <select class="form-select" name="category_id" id="song_cat_id" onchange="filterAlbumSelect(this.value)">
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Album</label>
                            <select class="form-select" name="album_id" id="song_alb_id">
                                <option value="">-- Bài hát đơn lẻ --</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status" id="song_status">
                                <option value="approved">Đã duyệt — công khai</option>
                                <option value="pending">Chờ duyệt</option>
                                <option value="rejected">Từ chối</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">File âm thanh <small class="text-secondary">(mp3, wav, ogg...)</small></label>
                            <input type="file" class="form-control" name="audio_file" id="song_audio_file" accept="audio/*,.mp3,.wav,.ogg,.m4a,.flac">
                            <input type="text" class="form-control mt-1" name="audio_url" id="song_audio_url"
                                   placeholder="Hoặc dán URL audio trực tiếp..." style="font-size:12px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Ảnh bìa <small class="text-secondary">(để trống = tự tạo)</small></label>
                            <input type="file" class="form-control" name="image_file" id="song_image_file" accept="image/*">
                            <input type="text" class="form-control mt-1" name="image_url" id="song_image_url"
                                   placeholder="Hoặc dán URL ảnh bìa..." style="font-size:12px;">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Lời bài hát (LRC hoặc văn bản)</label>
                        <textarea class="form-control" name="lyrics" id="song_lyrics" rows="4"
                            placeholder="[00:12.00] Lời bài hát đồng bộ..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-cyan w-100 justify-content-center"><i class="bi bi-upload"></i> Lưu bài hát</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: BANNER -->
<div class="modal fade" id="bannerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Thêm Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="admin.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action_banner" value="add">
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề Banner</label>
                        <input type="text" class="form-control" name="title" placeholder="Mô tả ngắn..." required>
                    </div>
                    <div>
                        <label class="form-label">Ảnh Banner (tỉ lệ 16:5)</label>
                        <input type="file" class="form-control" name="image_file" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-cyan w-100 justify-content-center"><i class="bi bi-check2-circle"></i> Lưu Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: NGHỆ SĨ (đầy đủ) -->
<div class="modal fade" id="artistModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="artistModalTitle">Thêm Nghệ sĩ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="admin.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action_artist" id="artist_action" value="add">
                    <input type="hidden" name="artist_id" id="artist_id_field">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Tên Nghệ sĩ</label>
                            <input type="text" class="form-control" name="name" id="artist_name" placeholder="VD: Sơn Tùng M-TP" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Ảnh chân dung</label>
                            <input type="file" class="form-control" name="image_file" id="artist_file" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tiểu sử</label>
                            <textarea class="form-control" name="bio" id="artist_bio" rows="3" placeholder="Mô tả về nghệ sĩ..."></textarea>
                        </div>
                        <div class="col-6">
                            <label class="form-label"><i class="bi bi-facebook me-1"></i>Facebook URL</label>
                            <input type="text" class="form-control" name="facebook" id="artist_fb" placeholder="https://facebook.com/...">
                        </div>
                        <div class="col-6">
                            <label class="form-label"><i class="bi bi-youtube me-1"></i>YouTube URL</label>
                            <input type="text" class="form-control" name="youtube" id="artist_yt" placeholder="https://youtube.com/...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-cyan w-100 justify-content-center"><i class="bi bi-person-check"></i> Lưu Nghệ sĩ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: PHÂN QUYỀN NGƯỜI DÙNG -->
<div class="modal fade" id="userRoleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Phân quyền tài khoản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="admin.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action_user" value="edit_role">
                    <input type="hidden" name="user_id" id="role_user_id">
                    <p style="font-size:13px;color:var(--text-sub);margin-bottom:16px;">Thay đổi quyền cho: <strong id="role_username" style="color:white;"></strong></p>
                    <label class="form-label">Vai trò</label>
                    <select class="form-select" name="role" id="role_select">
                        <option value="user">Người dùng thông thường</option>
                        <option value="artist">Nghệ sĩ</option>
                        <option value="manager">Quản lý nội dung</option>
                        <option value="admin">Admin toàn quyền</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-cyan w-100 justify-content-center"><i class="bi bi-shield-check"></i> Cập nhật quyền</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: KHUYẾN MÃI -->
<div class="modal fade" id="promoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tạo mã khuyến mãi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="admin.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action_promo" value="add">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Mã khuyến mãi</label>
                            <input type="text" class="form-control" name="code" placeholder="VD: SALE50" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Giảm giá (%)</label>
                            <input type="number" class="form-control" name="discount_percent" min="1" max="100" placeholder="10" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Mô tả</label>
                            <input type="text" class="form-control" name="description" placeholder="Nhân dịp...">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ngày hết hạn</label>
                            <input type="datetime-local" class="form-control" name="expires_at" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-cyan w-100 justify-content-center"><i class="bi bi-plus-lg"></i> Tạo mã</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Delete / Action Form -->
<form id="hiddenDeleteForm" action="admin.php" method="POST" style="display:none;">
    <input type="hidden" name="category_id" id="del_cat_id"><input type="hidden" name="action_category" id="del_cat_action">
    <input type="hidden" name="album_id"    id="del_alb_id"><input type="hidden" name="action_album"    id="del_alb_action">
    <input type="hidden" name="song_id"     id="del_song_id"><input type="hidden" name="action_song"    id="del_song_action">
    <input type="hidden" name="banner_id"   id="del_banner_id"><input type="hidden" name="action_banner" id="del_banner_action">
    <input type="hidden" name="artist_id"   id="del_artist_id"><input type="hidden" name="action_artist" id="del_artist_action">
    <input type="hidden" name="user_id"     id="del_user_id"><input type="hidden" name="action_user"    id="del_user_action">
    <input type="hidden" name="promo_id"    id="del_promo_id"><input type="hidden" name="action_promo"  id="del_promo_action">
</form>

<!-- Bulk Delete Form -->
<form id="bulkDeleteForm" action="admin.php" method="POST" style="display:none;">
    <input type="hidden" name="action_bulk" value="1">
    <input type="hidden" name="bulk_table" id="bulk_table_field">
    <input type="hidden" name="bulk_ids"   id="bulk_ids_field">
</form>

<!-- Reset Table Form -->
<form id="resetTableForm" action="admin.php" method="POST" style="display:none;">
    <input type="hidden" name="action_reset" value="1">
    <input type="hidden" name="reset_table"   id="reset_table_field">
    <input type="hidden" name="reset_confirm" id="reset_confirm_field">
</form>

<!-- MODAL: XÁC NHẬN RESET BẢNG -->
<div class="modal fade" id="resetModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:1px solid rgba(224,92,109,0.5);">
            <div class="modal-header" style="border-bottom:1px solid rgba(224,92,109,0.3);">
                <h5 class="modal-title fw-bold" style="color:var(--accent-red);">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Xóa toàn bộ dữ liệu
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:13px;color:var(--text-sub);margin-bottom:12px;">Bạn sắp xóa <strong style="color:white;" id="reset_table_label"></strong>. Hành động này <strong style="color:var(--accent-red);">không thể hoàn tác</strong>.</p>
                <div style="background:rgba(224,92,109,0.08);border:1px solid rgba(224,92,109,0.25);border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:12px;color:#e8a0a0;">
                    <i class="bi bi-info-circle me-1"></i> Gõ <strong>XAC NHAN XOA</strong> để xác nhận
                </div>
                <input type="text" id="resetConfirmInput" class="form-control" placeholder="XAC NHAN XOA" autocomplete="off">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-outline-admin" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="resetConfirmBtn" onclick="doResetTable()"
                    style="background:var(--accent-red);color:white;border:none;border-radius:30px;padding:8px 20px;font-weight:700;font-size:13px;cursor:pointer;">
                    <i class="bi bi-trash3-fill me-1"></i>Xóa tất cả
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ==========================================
// DỮ LIỆU PHP → JS
// ==========================================
const dbCategories = <?= json_encode($categories, JSON_UNESCAPED_UNICODE) ?>;
const dbAlbums     = <?= json_encode($albums, JSON_UNESCAPED_UNICODE) ?>;
const dbSongs      = <?= json_encode($songs, JSON_UNESCAPED_UNICODE) ?>;
const dbBanners    = <?= json_encode($banners, JSON_UNESCAPED_UNICODE) ?>;
const dbArtists    = <?= json_encode($artists, JSON_UNESCAPED_UNICODE) ?>;
const dbUsers      = <?= json_encode($users, JSON_UNESCAPED_UNICODE) ?>;
const dbPromos     = <?= json_encode($promotions, JSON_UNESCAPED_UNICODE) ?>;
let   dbSiteConfig = <?= json_encode($site_config, JSON_UNESCAPED_UNICODE) ?>;
const phpAlert     = `<?= addslashes($message) ?>`;
const phpAlertType = `<?= $msgType ?>`;
const TOTAL_VIEWS  = <?= (int)$total_views ?>;
const ITEMS_PER_PAGE = 10;
const container = document.getElementById('admin-content');

document.addEventListener('DOMContentLoaded', () => {
    renderDashboard();

    // ===== Intercept tất cả form trong modal → AJAX =====
    document.querySelectorAll('.modal form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = form.querySelector('[type=submit]');
            const origHtml = btn ? btn.innerHTML : '';
            if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" style="width:14px;height:14px;border-width:2px;"></span>Đang xử lý...'; }

            const fd = new FormData(form);
            const data = await adminFetch(fd);

            // Đóng modal
            const modalEl = form.closest('.modal');
            if (modalEl) { const mi = bootstrap.Modal.getInstance(modalEl); if (mi) mi.hide(); }

            if (data.status === 'success') {
                showToast(data.message || 'Thao tác thành công!', 'success');
                applyFreshData(data);
                rerenderCurrent();
            } else {
                showToast(data.message || 'Đã xảy ra lỗi', 'error');
            }
            if (btn) { btn.disabled = false; btn.innerHTML = origHtml; }
        });
    });

    // Mở modal sửa bài hát nếu được gọi từ BXH bên ngoài
    const urlParams = new URLSearchParams(window.location.search);
    const editSongId = urlParams.get('edit_song_id');
    if (editSongId) {
        const song = dbSongs.find(s => s.id == editSongId);
        if (song) {
            const safeT = song.title;
            const safeA = song.artist;
            const safeC = song.composer || '';
            const safeL = (song.lyrics || '').replace(/\\n/g, '\n');
            setTimeout(() => {
                openEditSongModal(song.id, safeT, safeA, safeC,
                    song.category_id, song.album_id || '',
                    safeL, song.mood || '', song.status || 'approved');
            }, 400);
        }
    }
});

// ==========================================
// HELPERS
// ==========================================
let activeNavId = 'db';

function setNav(id) {
    activeNavId = id;
    ['db','stats','music','pending','artist','users','ui','promo'].forEach(n => {
        const el = document.getElementById('nav-' + n);
        if (el) el.classList.remove('active');
    });
    const el = document.getElementById('nav-' + id);
    if (el) el.classList.add('active');
}

// --- Toast notification ---
function showToast(msg, type = 'success') {
    const existing = document.getElementById('admin-toast');
    if (existing) existing.remove();
    const t = document.createElement('div');
    t.id = 'admin-toast';
    const bg = type === 'success' ? '#22c55e' : '#e05c6d';
    const fc = type === 'success' ? '#000' : '#fff';
    t.style.cssText = `position:fixed;bottom:28px;right:28px;background:${bg};color:${fc};font-weight:700;padding:13px 22px;border-radius:12px;z-index:99999;font-size:13px;display:flex;align-items:center;gap:10px;box-shadow:0 6px 28px rgba(0,0,0,0.45);max-width:400px;animation:fadeIn .25s ease;`;
    t.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'}"></i><span>${msg}</span>`;
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; setTimeout(() => t.remove(), 300); }, 4500);
}

// --- AJAX post to admin.php ---
async function adminFetch(formData) {
    formData.append('is_ajax', '1');
    try {
        const r = await fetch('admin.php', { method: 'POST', body: formData });
        return await r.json();
    } catch (e) {
        return { status: 'error', message: 'Lỗi kết nối máy chủ' };
    }
}

// --- Update all local arrays from fresh server data ---
function applyFreshData(data) {
    if (data.categories)  { dbCategories.length = 0; dbCategories.push(...data.categories); }
    if (data.albums)      { dbAlbums.length = 0;     dbAlbums.push(...data.albums); }
    if (data.songs)       { dbSongs.length = 0;      dbSongs.push(...data.songs); }
    if (data.banners)     { dbBanners.length = 0;    dbBanners.push(...data.banners); }
    if (data.artists)     { dbArtists.length = 0;    dbArtists.push(...data.artists); }
    if (data.users)       { dbUsers.length = 0;      dbUsers.push(...data.users); }
    if (data.promos)      { dbPromos.length = 0;     dbPromos.push(...data.promos); }
    if (data.site_config) { dbSiteConfig = data.site_config; }
}

// --- Re-render current view after data change ---
function rerenderCurrent() {
    const map = {
        'db': renderDashboard, 'stats': renderStats,
        'music': renderMusic,  'pending': renderPending,
        'artist': renderArtists, 'users': () => renderUsers(),
        'ui': renderUIConfig,  'promo': renderPromo
    };
    (map[activeNavId] || renderDashboard)();
}

function renderAlert() {
    if (!phpAlert) return '';
    const icon = phpAlertType === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
    const cls  = phpAlertType === 'success' ? '' : 'error';
    return `<div class="nct-alert ${cls}">
                <i class="bi ${icon}"></i><span>${phpAlert}</span>
                <button onclick="this.parentElement.remove()" style="margin-left:auto;background:none;border:none;color:var(--text-sub);cursor:pointer;font-size:16px;">×</button>
            </div>`;
}

function roleBadge(role) {
    const map = { admin: 'Admin', manager: 'Quản lý', artist: 'Nghệ sĩ', user: 'Người dùng' };
    return `<span class="badge-role ${role}">${map[role] || role}</span>`;
}

function statusBadge(status) {
    const map = { approved: 'Đã duyệt', pending: 'Chờ duyệt', rejected: 'Từ chối', active: 'Hoạt động', banned: 'Bị khoá' };
    return `<span class="badge-status ${status}">${map[status] || status}</span>`;
}

function handleGlobalSearch(query) {
    if (query.trim() === '') { renderDashboard(); return; }
    setNav('');
    renderSongTableView(dbSongs, `Kết quả: "${query}"`, null, null, null, 1, query, 'global');
}

// ==========================================
// 1. DASHBOARD
// ==========================================
function renderDashboard() {
    setNav('db');
    window.scrollTo(0, 0);
    const pendingSongs = dbSongs.filter(s => (s.status || 'approved') === 'pending');
    const totalUsers   = dbUsers.length;

    let html = `
        <div class="d-flex justify-content-between align-items-center" style="margin-top:32px;margin-bottom:8px;">
            <div>
                <h2 class="section-title" style="margin:0 0 4px;">Bảng Điều Khiển</h2>
                <p style="font-size:13px;color:var(--text-sub);margin:0;">Chào mừng trở lại, <strong style="color:white;"><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn-outline-admin" onclick="openAddCatModal()"><i class="bi bi-folder-plus"></i> Thêm chủ đề</button>
                <button class="btn-cyan" onclick="openAddSongModal()"><i class="bi bi-plus-circle-fill"></i> Thêm bài hát</button>
            </div>
        </div>
        ${renderAlert()}`;

    if (pendingSongs.length > 0) {
        html += `<div class="pending-banner">
            <i class="bi bi-clock-history" style="color:var(--accent-orange);font-size:18px;"></i>
            <span>Có <strong>${pendingSongs.length} bài hát</strong> đang chờ duyệt.</span>
            <button class="btn-outline-admin ms-auto" onclick="setNav('pending');renderPending()">Xem ngay <i class="bi bi-arrow-right"></i></button>
        </div>`;
    }

    html += `
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon cyan"><i class="bi bi-music-note-beamed"></i></div>
                    <div><h3>${dbSongs.length}</h3><p>Bài hát</p></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="bi bi-people-fill"></i></div>
                    <div><h3>${totalUsers}</h3><p>Người dùng</p></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-headphones"></i></div>
                    <div><h3>${Number(TOTAL_VIEWS).toLocaleString('vi-VN')}</h3><p>Lượt nghe</p></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="bi bi-person-badge"></i></div>
                    <div><h3>${dbArtists.length}</h3><p>Nghệ sĩ</p></div>
                </div>
            </div>
        </div>

        <div class="section-header">
            <h3>Danh mục Chủ Đề</h3>
            <button class="btn-outline-admin" onclick="openAddCatModal()"><i class="bi bi-plus"></i> Thêm chủ đề</button>
        </div>
        <div class="row row-cols-4 g-3 mb-4">`;

    if (dbCategories.length > 0) {
        dbCategories.forEach(cat => {
            const count = dbSongs.filter(s => s.category_id == cat.id).length;
            const safe  = cat.name.replace(/'/g, "\\'");
            html += `
                <div class="col">
                    <div class="topic-admin-card" onclick="renderAlbumView(${cat.id}, '${safe}')">
                        <div class="topic-action-btns">
                            <button class="btn-icon-action btn-icon-edit" onclick="event.stopPropagation();openEditCatModal(${cat.id},'${safe}')"><i class="bi bi-pencil"></i></button>
                            <button class="btn-icon-action btn-icon-del"  onclick="event.stopPropagation();submitAction('cat',${cat.id},'${safe}','delete')"><i class="bi bi-trash3"></i></button>
                        </div>
                        <div style="flex:1;z-index:2;"><h5>${cat.name}</h5><span class="song-count">${count} bài hát</span></div>
                        <img src="${cat.image_url || 'https://placehold.co/80/2a3540/FFF?text=CAT'}" onerror="this.src='https://placehold.co/80/2a3540/FFF?text=CAT'">
                    </div>
                </div>`;
        });
    } else {
        html += `<div class="col-12" style="color:var(--text-sub);font-size:13px;padding:16px 0;">Chưa có chủ đề nào.</div>`;
    }

    html += `</div>`;

    // Top 5 songs
    const sorted = [...dbSongs].sort((a,b)=>(b.views||0)-(a.views||0)).slice(0,5);
    html += `
        <div class="section-header"><h3>Top 5 Bài Hát Nhiều Lượt Nghe</h3></div>
        <div class="admin-card">
            <table class="admin-table">
                <thead><tr><th>#</th><th>Bài hát</th><th>Nghệ sĩ</th><th>Chủ đề</th><th style="text-align:right;">Lượt nghe</th><th style="text-align:center;">Thao tác</th></tr></thead>
                <tbody>`;
    sorted.forEach((s, i) => {
        const safeT = s.title.replace(/'/g, "\\'");
        const safeA = s.artist.replace(/'/g, "\\'");
        const safeC = (s.composer||'').replace(/'/g, "\\'");
        const safeL = (s.lyrics||'').replace(/'/g, "\\'").replace(/\n/g, "\\n");
        html += `<tr>
            <td style="color:var(--accent-color);font-weight:700;">${i+1}</td>
            <td><div class="d-flex align-items-center gap-2">
                <img src="${s.image_url}" style="width:36px;height:36px;border-radius:6px;object-fit:cover;" onerror="this.src='https://placehold.co/36'">
                <span style="font-weight:600;">${s.title}</span>
            </div></td>
            <td style="color:var(--text-sub);">${s.artist}</td>
            <td><span class="lossless-badge">${s.category_name||'—'}</span></td>
            <td style="text-align:right;font-weight:700;color:var(--accent-green);">${Number(s.views||0).toLocaleString('vi-VN')}</td>
            <td style="text-align:center;">
                <button onclick="openEditSongModal(${s.id},'${safeT}','${safeA}','${safeC}',${s.category_id},'${s.album_id||''}','${safeL}','${s.mood||''}','${s.status||'approved'}')"
                        class="btn-icon-action btn-icon-edit" title="Sửa" style="margin-right:4px;"><i class="bi bi-pencil"></i></button>
                <button onclick="submitAction('song',${s.id},'${safeT}','delete')"
                        class="btn-icon-action btn-icon-del" title="Xóa"><i class="bi bi-trash3"></i></button>
            </td>
        </tr>`;
    });
    html += `</tbody></table></div>`;

    container.innerHTML = html;
}

// ==========================================
// 2. THỐNG KÊ
// ==========================================
function renderStats() {
    setNav('stats');
    window.scrollTo(0,0);

    const totalSongs   = dbSongs.length;
    const totalUsers   = dbUsers.length;
    const totalViews   = TOTAL_VIEWS;
    const totalArtists = dbArtists.length;

    // Dữ liệu biểu đồ chủ đề
    const catLabels = dbCategories.map(c => c.name);
    const catCounts = dbCategories.map(c => dbSongs.filter(s => s.category_id == c.id).length);

    // Dữ liệu biểu đồ tâm trạng
    const moods = ['vui','buon','tinh_cam','nang_dong','thu_gian','co_vu'];
    const moodLabels = ['Vui vẻ','Buồn bã','Tình cảm','Năng động','Thư giãn','Cổ vũ'];
    const moodCounts = moods.map(m => dbSongs.filter(s => s.mood === m).length);

    // Phân quyền users
    const roles = ['admin','manager','artist','user'];
    const roleLabels = ['Admin','Quản lý','Nghệ sĩ','Người dùng'];
    const roleCounts = roles.map(r => dbUsers.filter(u => u.role === r).length);

    // Trạng thái bài hát
    const statusLabels = ['Đã duyệt','Chờ duyệt','Từ chối'];
    const statusCounts = [
        dbSongs.filter(s => (s.status||'approved') === 'approved').length,
        dbSongs.filter(s => s.status === 'pending').length,
        dbSongs.filter(s => s.status === 'rejected').length,
    ];

    // Màu sắc dùng chung
    const PALETTE = ['#00d4d4','#ff8c00','#22c55e','#a78bfa','#e05c6d','#38bdf8','#fb923c','#f9a8d4','#4ade80','#818cf8'];

    let html = `
        <h2 class="section-title">📊 Thống Kê & Báo Cáo</h2>
        ${renderAlert()}

        <!-- KPI CARDS (donut mini) -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="stat-card flex-column align-items-center text-center" style="padding:20px 12px;gap:10px;">
                    <canvas id="donutSongs" width="90" height="90" style="max-width:90px;"></canvas>
                    <div><h3 style="font-size:22px;">${totalSongs}</h3><p>Tổng bài hát</p></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card flex-column align-items-center text-center" style="padding:20px 12px;gap:10px;">
                    <canvas id="donutUsers" width="90" height="90" style="max-width:90px;"></canvas>
                    <div><h3 style="font-size:22px;">${totalUsers}</h3><p>Tài khoản</p></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card flex-column align-items-center text-center" style="padding:20px 12px;gap:10px;">
                    <canvas id="donutViews" width="90" height="90" style="max-width:90px;"></canvas>
                    <div><h3 style="font-size:22px;">${Number(totalViews).toLocaleString('vi-VN')}</h3><p>Lượt nghe</p></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card flex-column align-items-center text-center" style="padding:20px 12px;gap:10px;">
                    <canvas id="donutArtists" width="90" height="90" style="max-width:90px;"></canvas>
                    <div><h3 style="font-size:22px;">${totalArtists}</h3><p>Nghệ sĩ</p></div>
                </div>
            </div>
        </div>

        <!-- BIỂU ĐỒ TRÒN HÀNG 1 -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="chart-card" style="display:flex;flex-direction:column;align-items:center;">
                    <h4 style="width:100%;"><i class="bi bi-pie-chart-fill me-2" style="color:var(--accent-color);"></i>Bài hát theo chủ đề</h4>
                    <canvas id="chartCat" style="max-height:240px;max-width:240px;"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-card" style="display:flex;flex-direction:column;align-items:center;">
                    <h4 style="width:100%;"><i class="bi bi-emoji-smile-fill me-2" style="color:var(--accent-orange);"></i>Phân loại tâm trạng</h4>
                    <canvas id="chartMood" style="max-height:240px;max-width:240px;"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-card" style="display:flex;flex-direction:column;align-items:center;">
                    <h4 style="width:100%;"><i class="bi bi-shield-lock me-2" style="color:var(--accent-purple);"></i>Phân quyền người dùng</h4>
                    <canvas id="chartRoles" style="max-height:240px;max-width:240px;"></canvas>
                </div>
            </div>
        </div>

        <!-- BIỂU ĐỒ TRÒN HÀNG 2 -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="chart-card" style="display:flex;flex-direction:column;align-items:center;">
                    <h4 style="width:100%;"><i class="bi bi-check-circle-fill me-2" style="color:var(--accent-green);"></i>Trạng thái bài hát</h4>
                    <canvas id="chartStatus" style="max-height:240px;max-width:240px;"></canvas>
                </div>
            </div>
            <div class="col-md-8">
                <div class="chart-card">
                    <h4><i class="bi bi-trophy me-2" style="color:var(--accent-orange);"></i>Top 10 bài hát nhiều lượt nghe</h4>
                    <canvas id="chartTopSongs" style="max-height:260px;"></canvas>
                </div>
            </div>
        </div>

        <div class="section-header">
            <h3>Xuất báo cáo</h3>
        </div>
        <div class="quick-actions">
            <button class="btn-outline-admin" onclick="exportCSV()"><i class="bi bi-file-earmark-spreadsheet"></i> Xuất CSV bài hát</button>
            <button class="btn-outline-admin" onclick="exportUsersCSV()"><i class="bi bi-file-earmark-person"></i> Xuất CSV người dùng</button>
            <button class="btn-outline-admin" onclick="window.print()"><i class="bi bi-printer"></i> In báo cáo</button>
        </div>`;

    container.innerHTML = html;

    // Shared chart options
    const legendOpts = { labels: { color: '#8a9bb0', font: { size: 11 }, padding: 14 }, position: 'bottom' };
    const doughnutOpts = (cutout = '65%') => ({
        cutout,
        plugins: { legend: legendOpts },
        responsive: true,
        maintainAspectRatio: true,
    });
    const COLORS_10 = ['#00d4d4','#ff8c00','#22c55e','#a78bfa','#e05c6d','#38bdf8','#fb923c','#f9a8d4','#4ade80','#818cf8'];

    // ---- KPI mini donuts ----
    const makeKpiDonut = (id, val, total, color) => {
        if (!document.getElementById(id)) return;
        new Chart(document.getElementById(id), {
            type: 'doughnut',
            data: {
                datasets: [{ data: [val, Math.max(0, total - val)], backgroundColor: [color, 'rgba(255,255,255,0.06)'], borderWidth: 0 }]
            },
            options: { cutout: '72%', plugins: { legend: { display: false }, tooltip: { enabled: false } }, responsive: true, maintainAspectRatio: true }
        });
    };
    makeKpiDonut('donutSongs',   totalSongs,   Math.max(totalSongs, 100),   '#00d4d4');
    makeKpiDonut('donutUsers',   totalUsers,   Math.max(totalUsers, 100),   '#ff8c00');
    makeKpiDonut('donutViews',   Math.min(totalViews, 999999), 999999,     '#22c55e');
    makeKpiDonut('donutArtists', totalArtists, Math.max(totalArtists, 50),  '#a78bfa');

    // ---- Chart 1: Chủ đề (doughnut) ----
    if (catLabels.length > 0) {
        new Chart(document.getElementById('chartCat'), {
            type: 'doughnut',
            data: { labels: catLabels, datasets: [{ data: catCounts, backgroundColor: COLORS_10, borderWidth: 0 }] },
            options: doughnutOpts('60%')
        });
    }

    // ---- Chart 2: Tâm trạng (doughnut) ----
    new Chart(document.getElementById('chartMood'), {
        type: 'doughnut',
        data: { labels: moodLabels, datasets: [{ data: moodCounts, backgroundColor: COLORS_10, borderWidth: 0 }] },
        options: doughnutOpts('60%')
    });

    // ---- Chart 3: Phân quyền (pie) ----
    new Chart(document.getElementById('chartRoles'), {
        type: 'pie',
        data: { labels: roleLabels, datasets: [{ data: roleCounts, backgroundColor: ['#00d4d4','#ff8c00','#a78bfa','#8a9bb0'], borderWidth: 0 }] },
        options: { plugins: { legend: legendOpts }, responsive: true, maintainAspectRatio: true }
    });

    // ---- Chart 4: Trạng thái (doughnut) ----
    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: { labels: statusLabels, datasets: [{ data: statusCounts, backgroundColor: ['#22c55e','#ff8c00','#e05c6d'], borderWidth: 0 }] },
        options: doughnutOpts('60%')
    });

    // ---- Chart 5: Top 10 (horizontal bar) ----
    const top10 = [...dbSongs].sort((a,b)=>(b.views||0)-(a.views||0)).slice(0,10);
    new Chart(document.getElementById('chartTopSongs'), {
        type: 'bar',
        data: {
            labels: top10.map(s => s.title.length > 18 ? s.title.substr(0,18)+'…' : s.title),
            datasets: [{ label: 'Lượt nghe', data: top10.map(s => s.views||0),
                backgroundColor: top10.map((_,i) => COLORS_10[i % COLORS_10.length]),
                borderRadius: 6, borderWidth: 0 }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#8a9bb0' }, grid: { color: 'rgba(255,255,255,0.06)' } },
                y: { ticks: { color: '#fff', font: { size: 11 } }, grid: { color: 'rgba(255,255,255,0.04)' } }
            },
            responsive: true
        }
    });
}

function exportCSV() {
    const headers = ['ID','Tên bài hát','Ca sĩ','Nhạc sĩ','Chủ đề','Album','Lượt nghe','Tâm trạng','Trạng thái'];
    const rows = dbSongs.map(s => [s.id, s.title, s.artist, s.composer||'', s.category_name||'', s.album_title||'', s.views||0, s.mood||'', s.status||'approved']);
    downloadCSV('bai-hat.csv', headers, rows);
}
function exportUsersCSV() {
    const headers = ['ID','Tên đăng nhập','Email','Vai trò','Trạng thái'];
    const rows = dbUsers.map(u => [u.id, u.username, u.email, u.role, u.status||'active']);
    downloadCSV('nguoi-dung.csv', headers, rows);
}
function downloadCSV(filename, headers, rows) {
    const bom = '\uFEFF';
    let csv = bom + headers.join(',') + '\n';
    rows.forEach(r => { csv += r.map(v => `"${String(v).replace(/"/g,'""')}"`).join(',') + '\n'; });
    const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
    const a = Object.assign(document.createElement('a'), { href: URL.createObjectURL(blob), download: filename });
    a.click();
}

// ==========================================
// 3. MUSIC (Chủ đề → Album → Bài hát)
// ==========================================
function renderMusic() {
    setNav('music');
    window.scrollTo(0,0);

    let html = `
        <div class="d-flex justify-content-between align-items-center" style="margin-top:32px;margin-bottom:8px;">
            <div>
                <h2 class="section-title" style="margin:0 0 4px;">Nội Dung Âm Nhạc</h2>
                <p style="font-size:13px;color:var(--text-sub);margin:0;">Quản lý Chủ đề → Album → Bài hát</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn-outline-admin" onclick="openAddCatModal()"><i class="bi bi-folder-plus"></i> Thêm chủ đề</button>
                <button class="btn-cyan" onclick="openAddSongModal()"><i class="bi bi-plus-circle-fill"></i> Thêm bài hát</button>
            </div>
        </div>
        ${renderAlert()}
        <div class="section-header">
            <h3>Danh mục Chủ Đề (${dbCategories.length})</h3>
            <button class="btn-outline-admin" onclick="openAddCatModal()"><i class="bi bi-plus"></i> Thêm chủ đề</button>
        </div>
        <div class="row row-cols-4 g-3 mb-4">`;

    if (dbCategories.length > 0) {
        dbCategories.forEach(cat => {
            const count = dbSongs.filter(s => s.category_id == cat.id).length;
            const safe  = cat.name.replace(/'/g, "\\'");
            html += `
                <div class="col">
                    <div class="topic-admin-card" onclick="renderAlbumView(${cat.id},'${safe}')">
                        <div class="topic-action-btns">
                            <button class="btn-icon-action btn-icon-edit" onclick="event.stopPropagation();openEditCatModal(${cat.id},'${safe}')"><i class="bi bi-pencil"></i></button>
                            <button class="btn-icon-action btn-icon-del"  onclick="event.stopPropagation();submitAction('cat',${cat.id},'${safe}','delete')"><i class="bi bi-trash3"></i></button>
                        </div>
                        <div style="flex:1;z-index:2;"><h5>${cat.name}</h5><span class="song-count">${count} bài hát</span></div>
                        <img src="${cat.image_url||'https://placehold.co/80/2a3540/FFF?text=CAT'}" onerror="this.src='https://placehold.co/80/2a3540/FFF?text=CAT'">
                    </div>
                </div>`;
        });
    } else {
        html += `<div class="col-12" style="color:var(--text-sub);font-size:13px;padding:16px 0;">Chưa có chủ đề nào.</div>`;
    }
    html += `</div>`;
    container.innerHTML = html;
}

function renderAlbumView(catId, catName) {
    setNav('music');
    window.scrollTo(0,0);
    const filteredAlbums = dbAlbums.filter(a => a.category_id == catId);
    const safe = catName.replace(/'/g, "\\'");

    let html = `
        <div class="admin-breadcrumb">
            <span onclick="renderMusic()">Âm nhạc</span>
            <i class="bi bi-chevron-right"></i>
            <span class="active">${catName}</span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title" style="margin:0;">Albums — ${catName}</h2>
            <button class="btn-cyan" onclick="openAddAlbumModal(${catId})"><i class="bi bi-journal-plus"></i> Tạo Album</button>
        </div>
        <div class="row row-cols-5 g-3 mb-5">`;

    filteredAlbums.forEach(alb => {
        const count    = dbSongs.filter(s => s.album_id == alb.id).length;
        const safeTitle = alb.title.replace(/'/g, "\\'");
        html += `
            <div class="col">
                <div class="square-card" onclick="renderSongTableView(dbSongs.filter(s=>s.album_id==${alb.id}), '${safeTitle}', ${catId}, '${safe}', ${alb.id})">
                    <div class="square-img-box">
                        <img src="${alb.image_url||'https://placehold.co/200/2a3540/FFF?text=Album'}" onerror="this.src='https://placehold.co/200/2a3540/FFF?text=Album'">
                        <div class="album-action-btns">
                            <button class="btn-icon-action btn-icon-edit" onclick="event.stopPropagation();openEditAlbumModal(${alb.id},'${safeTitle}',${catId})"><i class="bi bi-pencil"></i></button>
                            <button class="btn-icon-action btn-icon-del"  onclick="event.stopPropagation();submitAction('alb',${alb.id},'${safeTitle}','delete')"><i class="bi bi-trash3"></i></button>
                        </div>
                    </div>
                    <h6>${alb.title}</h6>
                    <p>${count} bài hát</p>
                </div>
            </div>`;
    });
    if (!filteredAlbums.length) html += `<div class="col-12" style="color:var(--text-sub);font-size:13px;padding:8px 0;">Chưa có Album nào.</div>`;
    html += `</div>`;

    html += `<div class="section-header"><h3>Bài hát đơn lẻ trong chủ đề</h3></div>`;
    html += buildSongTableHTML(dbSongs.filter(s => s.category_id == catId && (s.album_id == null || s.album_id == '')), `Bài hát đơn — ${catName}`, catId, safe, null, 1, '', 'album');
    container.innerHTML = html;
}

// ==========================================
// 4. BẢNG BÀI HÁT
// ==========================================
function renderSongTableView(source, title, catId, catName, albId, page=1, query='', ctx='album') {
    setNav('music');
    window.scrollTo(0,0);
    const safe = catName ? catName.replace(/'/g, "\\'") : '';
    let html = '';
    if (catId !== null && albId !== null) {
        html += `<div class="admin-breadcrumb">
            <span onclick="renderMusic()">Âm nhạc</span>
            <i class="bi bi-chevron-right"></i>
            <span onclick="renderAlbumView(${catId},'${safe}')">${catName}</span>
            <i class="bi bi-chevron-right"></i>
            <span class="active">${title}</span>
        </div>`;
    } else if (ctx === 'global') {
        html += `<div class="admin-breadcrumb"><span onclick="renderMusic()">Âm nhạc</span><i class="bi bi-chevron-right"></i><span class="active">${title}</span></div>`;
    }
    html += buildSongTableHTML(source, title, catId, catName, albId, page, query, ctx);
    container.innerHTML = html;
}

function buildSongTableHTML(sourceArray, titleCtx, catId, catName, albId, page=1, query='', ctx='album') {
    let filtered = sourceArray;
    if (query.trim()) {
        filtered = sourceArray.filter(s =>
            s.title.toLowerCase().includes(query.toLowerCase()) ||
            s.artist.toLowerCase().includes(query.toLowerCase())
        );
    }
    const totalPages = Math.max(1, Math.ceil(filtered.length / ITEMS_PER_PAGE));
    page = Math.min(Math.max(1, page), totalPages);
    const paginated = filtered.slice((page-1)*ITEMS_PER_PAGE, page*ITEMS_PER_PAGE);
    const srcStr = ctx === 'global' ? 'dbSongs' : (albId !== null ? `dbSongs.filter(s=>s.album_id==${albId})` : `dbSongs.filter(s=>s.category_id==${catId}&&(s.album_id==null||s.album_id==''))`);
    const safeTitle = titleCtx ? titleCtx.replace(/'/g, "\\'") : '';
    const safe      = catName  ? catName.replace(/'/g, "\\'")  : '';
    const pageBase  = `renderSongTableView(${srcStr},'${safeTitle}',${catId},'${safe}',${albId},PAGE_NUM,'${query.replace(/'/g,"\\'")}','${ctx}')`;

    // Bulk action bar (hidden by default)
    let html = `
        <div id="bulk_bar_songs" style="display:none;background:rgba(224,92,109,0.1);border:1px solid rgba(224,92,109,0.3);border-radius:10px;padding:10px 18px;margin-bottom:12px;align-items:center;gap:12px;">
            <i class="bi bi-check2-square" style="color:var(--accent-red);font-size:16px;"></i>
            <span style="font-size:13px;">Đã chọn <strong id="bulk_count_songs" style="color:white;">0</strong> bài hát</span>
            <button onclick="doBulkDelete('songs')" style="margin-left:auto;background:var(--accent-red);color:white;border:none;border-radius:20px;padding:6px 16px;font-size:12px;font-weight:700;cursor:pointer;">
                <i class="bi bi-trash3 me-1"></i>Xóa đã chọn
            </button>
            <button onclick="toggleAllCheckboxes('songs_check',false);updateBulkBar('songs')" style="background:rgba(255,255,255,0.08);color:var(--text-sub);border:1px solid var(--border-color);border-radius:20px;padding:6px 14px;font-size:12px;cursor:pointer;">
                Bỏ chọn tất cả
            </button>
        </div>`;

    html += `
        <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top:8px;">
            <h2 class="section-title" style="margin:0;">${titleCtx}</h2>
            <div class="d-flex gap-2 align-items-center">
                <div class="search-bar" style="width:220px;padding:6px 14px;">
                    <i class="bi bi-search" style="font-size:12px;"></i>
                    <input type="text" placeholder="Tìm trong bảng..." value="${query}"
                        oninput="renderSongTableView(${srcStr},'${safeTitle}',${catId},'${safe}',${albId},1,this.value,'${ctx}')">
                </div>
                <button class="btn-cyan" onclick="openAddSongModal(${catId||"''"}, ${albId||"''"})"><i class="bi bi-plus-lg"></i> Thêm bài hát</button>
                ${ctx !== 'global' ? `<button onclick="openResetModal('songs','toàn bộ bài hát')" style="background:rgba(224,92,109,0.15);color:var(--accent-red);border:1px solid rgba(224,92,109,0.3);border-radius:30px;padding:8px 14px;font-size:12px;font-weight:700;cursor:pointer;"><i class="bi bi-trash3-fill me-1"></i>Xóa tất cả</button>` : ''}
            </div>
        </div>
        <div class="admin-card" style="overflow:visible;">
        <table class="song-table">
            <thead><tr>
                <th style="width:4%;text-align:center;"><input type="checkbox" onchange="toggleAllCheckboxes('songs_check',this.checked);updateBulkBar('songs')" style="cursor:pointer;accent-color:var(--accent-red);"></th>
                <th style="width:4%;text-align:center;">ID</th>
                <th style="width:33%;">Bài hát</th>
                <th style="width:13%;">Nhạc sĩ</th>
                <th style="width:15%;">Phân loại</th>
                <th style="width:10%;text-align:center;">Trạng thái</th>
                <th style="width:10%;text-align:right;">Lượt nghe</th>
                <th style="width:11%;text-align:center;">Thao tác</th>
            </tr></thead><tbody>`;

    if (paginated.length > 0) {
        paginated.forEach(song => {
            const safeT = song.title.replace(/'/g, "\\'");
            const safeA = song.artist.replace(/'/g, "\\'");
            const safeC = (song.composer||'').replace(/'/g, "\\'");
            const safeL = (song.lyrics||'').replace(/'/g, "\\'").replace(/\n/g, "\\n");
            const safeM = song.mood||'';
            const safeSt = song.status||'approved';
            html += `
                <tr>
                    <td style="text-align:center;"><input type="checkbox" name="songs_check" value="${song.id}" onchange="updateBulkBar('songs')" style="cursor:pointer;accent-color:var(--accent-red);"></td>
                    <td style="text-align:center;color:var(--text-sub);font-size:11px;font-family:monospace;">#${song.id}</td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="${song.image_url}" style="width:40px;height:40px;border-radius:6px;object-fit:cover;flex-shrink:0;" onerror="this.src='https://placehold.co/40'">
                            <div style="min-width:0;">
                                <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;">${song.title}</div>
                                <div style="font-size:11px;color:var(--text-sub);">${song.artist}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:var(--text-sub);font-size:12px;">${song.composer||'—'}</td>
                    <td>
                        <span class="lossless-badge">${song.category_name||'—'}</span>
                        <div style="font-size:11px;color:var(--text-sub);margin-top:3px;"><i class="bi bi-disc me-1"></i>${song.album_title||'Đơn lẻ'}</div>
                    </td>
                    <td style="text-align:center;">${statusBadge(song.status||'approved')}</td>
                    <td style="text-align:right;font-weight:600;color:var(--accent-green);font-size:12px;">${Number(song.views||0).toLocaleString('vi-VN')}</td>
                    <td style="text-align:center;">
                        <button onclick="openEditSongModal(${song.id},'${safeT}','${safeA}','${safeC}',${song.category_id},'${song.album_id}','${safeL}','${safeM}','${safeSt}')"
                                class="btn-icon-action btn-icon-edit" title="Sửa" style="margin-right:4px;"><i class="bi bi-pencil"></i></button>
                        <button onclick="submitAction('song',${song.id},'${safeT}','delete')"
                                class="btn-icon-action btn-icon-del" title="Xóa"><i class="bi bi-trash3"></i></button>
                    </td>
                </tr>`;
        });
    } else {
        html += `<tr><td colspan="8" style="text-align:center;color:var(--text-sub);padding:48px 0;">Không có bài hát nào.</td></tr>`;
    }
    html += `</tbody></table></div>`;

    if (totalPages > 1) {
        html += `<nav class="mt-4"><ul class="pagination justify-content-end align-items-center">`;
        html += `<li class="page-item ${page===1?'disabled':''}"><a class="page-link" onclick="${page>1?pageBase.replace('PAGE_NUM',page-1):'return false'}">&lsaquo;</a></li>`;
        for (let i=1;i<=totalPages;i++) html += `<li class="page-item ${page===i?'active':''}"><a class="page-link" onclick="${pageBase.replace('PAGE_NUM',i)}">${i}</a></li>`;
        html += `<li class="page-item ${page===totalPages?'disabled':''}"><a class="page-link" onclick="${page<totalPages?pageBase.replace('PAGE_NUM',page+1):'return false'}">&rsaquo;</a></li>`;
        html += `</ul></nav>`;
    }
    return html;
}

// ==========================================
// 5. CHỜ DUYỆT
// ==========================================
function renderPending() {
    setNav('pending');
    window.scrollTo(0,0);
    const pendingSongs = dbSongs.filter(s => (s.status||'approved') === 'pending');
    let html = `
        <h2 class="section-title">Nội Dung Chờ Duyệt</h2>
        ${renderAlert()}`;

    if (!pendingSongs.length) {
        html += `<div style="text-align:center;padding:60px 0;color:var(--text-sub);">
            <i class="bi bi-check-circle" style="font-size:48px;color:var(--accent-green);display:block;margin-bottom:12px;"></i>
            <p>Không có nội dung nào đang chờ duyệt.</p>
        </div>`;
    } else {
        html += `<div class="admin-card"><table class="admin-table">
            <thead><tr><th>#</th><th>Bài hát</th><th>Ca sĩ</th><th>Chủ đề</th><th style="text-align:center;">Thao tác duyệt</th></tr></thead>
            <tbody>`;
        pendingSongs.forEach(song => {
            const safeT = song.title.replace(/'/g, "\\'");
            html += `<tr>
                <td style="color:var(--text-sub);font-size:11px;">#${song.id}</td>
                <td><div class="d-flex align-items-center gap-2">
                    <img src="${song.image_url}" style="width:38px;height:38px;border-radius:6px;object-fit:cover;" onerror="this.src='https://placehold.co/38'">
                    <span style="font-weight:600;">${song.title}</span>
                </div></td>
                <td style="color:var(--text-sub);">${song.artist}</td>
                <td><span class="lossless-badge">${song.category_name||'—'}</span></td>
                <td style="text-align:center;">
                    <button onclick="approveRejectSong(${song.id},'${safeT}','approve')" class="btn-icon-action btn-icon-ok" title="Duyệt" style="margin-right:6px;"><i class="bi bi-check-lg"></i></button>
                    <button onclick="approveRejectSong(${song.id},'${safeT}','reject')"  class="btn-icon-action btn-icon-del" title="Từ chối"><i class="bi bi-x-lg"></i></button>
                </td>
            </tr>`;
        });
        html += `</tbody></table></div>`;
    }
    container.innerHTML = html;
}

async function approveRejectSong(id, name, action) {
    const msg = action === 'approve' ? `Duyệt và công khai bài hát "${name}"?` : `Từ chối và ẩn bài hát "${name}"?`;
    if (!confirm(msg)) return;
    const fd = new FormData();
    fd.append('action_song', action);
    fd.append('song_id', id);
    const data = await adminFetch(fd);
    if (data.status === 'success') {
        showToast(data.message, 'success');
        applyFreshData(data);
        renderPending();
    } else {
        showToast(data.message || 'Đã xảy ra lỗi', 'error');
    }
}

// ==========================================
// 6. NGHỆ SĨ
// ==========================================
function renderArtists() {
    setNav('artist');
    window.scrollTo(0,0);
    let html = `
        <div class="section-header" style="margin-top:32px;">
            <h2 class="section-title" style="margin:0;">Quản Lý Nghệ Sĩ</h2>
            <button class="btn-cyan" onclick="openAddArtistModal()"><i class="bi bi-person-plus"></i> Thêm Nghệ sĩ</button>
        </div>
        ${renderAlert()}
        <div class="admin-card">
            <table class="admin-table">
                <thead><tr><th>Ảnh</th><th>Tên nghệ sĩ</th><th>Tiểu sử</th><th>Liên kết</th><th style="text-align:center;">Thao tác</th></tr></thead>
                <tbody>`;
    if (dbArtists.length > 0) {
        dbArtists.forEach(a => {
            const safeName = a.name.replace(/'/g, "\\'");
            const safeBio  = (a.bio||'').replace(/'/g, "\\'");
            const safeFb   = (a.facebook||'').replace(/'/g, "\\'");
            const safeYt   = (a.youtube||'').replace(/'/g, "\\'");
            html += `<tr>
                <td><img src="${a.image_url}" style="width:48px;height:48px;border-radius:50%;object-fit:cover;" onerror="this.src='https://placehold.co/48'"></td>
                <td style="font-weight:600;color:var(--accent-color);">${a.name}</td>
                <td style="color:var(--text-sub);font-size:12px;max-width:220px;">${(a.bio||'—').substring(0,80)}${(a.bio||'').length>80?'…':''}</td>
                <td>
                    ${a.facebook?`<a href="${a.facebook}" target="_blank" style="color:var(--accent-purple);font-size:18px;margin-right:8px;"><i class="bi bi-facebook"></i></a>`:''}
                    ${a.youtube?`<a href="${a.youtube}" target="_blank" style="color:var(--accent-red);font-size:18px;"><i class="bi bi-youtube"></i></a>`:''}
                    ${(!a.facebook&&!a.youtube)?'<span style="color:var(--text-sub);font-size:12px;">—</span>':''}
                </td>
                <td style="text-align:center;">
                    <button onclick="openEditArtistModal(${a.id},'${safeName}','${safeBio}','${safeFb}','${safeYt}')" class="btn-icon-action btn-icon-edit" style="margin-right:4px;"><i class="bi bi-pencil"></i></button>
                    <button onclick="submitAction('artist',${a.id},'${safeName}','delete')" class="btn-icon-action btn-icon-del"><i class="bi bi-trash3"></i></button>
                </td>
            </tr>`;
        });
    } else {
        html += `<tr><td colspan="5" style="text-align:center;color:var(--text-sub);padding:40px 0;">Chưa có Nghệ sĩ nào.</td></tr>`;
    }
    html += `</tbody></table></div>`;
    container.innerHTML = html;
}

// ==========================================
// 7. NGƯỜI DÙNG
// ==========================================
function renderUsers(page=1, query='') {
    setNav('users');
    window.scrollTo(0,0);
    let filtered = dbUsers;
    if (query.trim()) filtered = dbUsers.filter(u => u.username.toLowerCase().includes(query.toLowerCase()) || u.email.toLowerCase().includes(query.toLowerCase()));
    const total = Math.max(1, Math.ceil(filtered.length / ITEMS_PER_PAGE));
    page = Math.min(Math.max(1, page), total);
    const paged = filtered.slice((page-1)*ITEMS_PER_PAGE, page*ITEMS_PER_PAGE);

    let html = `
        <div class="d-flex justify-content-between align-items-center" style="margin-top:32px;margin-bottom:20px;">
            <h2 class="section-title" style="margin:0;">Quản Lý Tài Khoản</h2>
            <div class="search-bar" style="width:260px;padding:8px 16px;">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Tìm tên, email..." value="${query}" oninput="renderUsers(1,this.value)">
            </div>
        </div>
        ${renderAlert()}
        <div class="admin-card"><table class="admin-table">
            <thead><tr><th>#</th><th>Tên đăng nhập</th><th>Email</th><th>Vai trò</th><th>Trạng thái</th><th>Ngày tạo</th><th style="text-align:center;">Thao tác</th></tr></thead>
            <tbody>`;

    paged.forEach(u => {
        const safeU = u.username.replace(/'/g, "\\'");
        const status = u.status || 'active';
        html += `<tr>
            <td style="color:var(--text-sub);font-size:11px;">#${u.id}</td>
            <td style="font-weight:600;">${u.username}</td>
            <td style="color:var(--text-sub);font-size:12px;">${u.email}</td>
            <td>${roleBadge(u.role)}</td>
            <td>${statusBadge(status)}</td>
            <td style="color:var(--text-sub);font-size:12px;">${u.created_at ? u.created_at.substring(0,10) : '—'}</td>
            <td style="text-align:center;">
                <button onclick="openRoleModal(${u.id},'${safeU}','${u.role}')" class="btn-icon-action btn-icon-ok" title="Phân quyền" style="margin-right:4px;"><i class="bi bi-shield-check"></i></button>
                ${status === 'banned'
                    ? `<button onclick="banUnban(${u.id},'${safeU}','unban')" class="btn-icon-action btn-icon-edit" title="Mở khóa" style="margin-right:4px;"><i class="bi bi-unlock"></i></button>`
                    : `<button onclick="banUnban(${u.id},'${safeU}','ban')"   class="btn-icon-action btn-icon-ban" title="Khóa tài khoản" style="margin-right:4px;"><i class="bi bi-lock"></i></button>`
                }
                ${u.id != <?= $_SESSION['user_id'] ?>
                    ? `<button onclick="submitAction('user',${u.id},'${safeU}','delete')" class="btn-icon-action btn-icon-del" title="Xóa"><i class="bi bi-trash3"></i></button>`
                    : '<span style="font-size:11px;color:var(--text-sub);">Bạn</span>'
                }
            </td>
        </tr>`;
    });

    html += `</tbody></table></div>`;
    if (total > 1) {
        html += `<nav class="mt-4"><ul class="pagination justify-content-end">`;
        html += `<li class="page-item ${page===1?'disabled':''}"><a class="page-link" onclick="${page>1?`renderUsers(${page-1},'${query}')`:''}">&lsaquo;</a></li>`;
        for (let i=1;i<=total;i++) html += `<li class="page-item ${page===i?'active':''}"><a class="page-link" onclick="renderUsers(${i},'${query}')">${i}</a></li>`;
        html += `<li class="page-item ${page===total?'disabled':''}"><a class="page-link" onclick="${page<total?`renderUsers(${page+1},'${query}')`:''}">&rsaquo;</a></li>`;
        html += `</ul></nav>`;
    }
    container.innerHTML = html;
}

function openRoleModal(id, username, currentRole) {
    document.getElementById('role_user_id').value = id;
    document.getElementById('role_username').textContent = username;
    document.getElementById('role_select').value = currentRole;
    new bootstrap.Modal(document.getElementById('userRoleModal')).show();
}

async function banUnban(id, name, action) {
    if (!confirm(action==='ban' ? `Khóa tài khoản "${name}"?` : `Mở khóa tài khoản "${name}"?`)) return;
    const fd = new FormData();
    fd.append('action_user', action);
    fd.append('user_id', id);
    const data = await adminFetch(fd);
    if (data.status === 'success') {
        showToast(data.message, 'success');
        applyFreshData(data);
        renderUsers();
    } else {
        showToast(data.message || 'Đã xảy ra lỗi', 'error');
    }
}

// ==========================================
// 8. GIAO DIỆN (Banner + Sections Trang Chủ)
// ==========================================
function renderUIConfig() {
    setNav('ui');
    window.scrollTo(0,0);

    // Helper: build album multi-select options
    function albOpts(selectedIds) {
        const sel = selectedIds ? selectedIds.split(',').map(s=>s.trim()) : [];
        return dbAlbums.map(a =>
            `<option value="${a.id}" ${sel.includes(String(a.id))?'selected':''}>${a.title}</option>`
        ).join('');
    }
    function catOpts(selectedId) {
        return `<option value="">-- Tất cả --</option>` +
            dbCategories.map(c =>
                `<option value="${c.id}" ${String(dbSiteConfig[selectedId]||'')==String(c.id)?'selected':''}>${c.name}</option>`
            ).join('');
    }

    const cfg = dbSiteConfig;
    const v = (k, def='') => (cfg[k] !== undefined && cfg[k] !== '') ? cfg[k] : def;

    let html = `
        <h2 class="section-title">Giao Diện Trang Chủ</h2>
        ${renderAlert()}

        <!-- ===== BANNER ===== -->
        <div class="section-header">
            <h3>Banners Trang Chủ</h3>
            <button class="btn-cyan" data-bs-toggle="modal" data-bs-target="#bannerModal"><i class="bi bi-plus-lg"></i> Thêm Banner</button>
        </div>
        <div class="admin-card"><table class="admin-table">
            <thead><tr><th>Hình ảnh</th><th>Tiêu đề</th><th style="text-align:center;">Thao tác</th></tr></thead>
            <tbody>`;
    if (dbBanners.length > 0) {
        dbBanners.forEach(b => {
            html += `<tr>
                <td><img src="${b.image_url}" style="height:60px;width:200px;object-fit:cover;border-radius:8px;" onerror="this.src='https://placehold.co/200x60'"></td>
                <td style="font-weight:600;">${b.title}</td>
                <td style="text-align:center;">
                    <button onclick="submitAction('banner',${b.id},'${b.title}','delete')" class="btn-icon-action btn-icon-del"><i class="bi bi-trash3"></i></button>
                </td>
            </tr>`;
        });
    } else {
        html += `<tr><td colspan="3" style="text-align:center;color:var(--text-sub);padding:40px 0;">Chưa có Banner nào.</td></tr>`;
    }
    html += `</tbody></table></div>

        <!-- ===== SECTIONS TRANG CHỦ ===== -->
        <div class="section-header" style="margin-top:36px;">
            <h3><i class="bi bi-layout-text-window-reverse me-2" style="color:var(--accent-color);"></i>Sections Trang Chủ</h3>
            <span style="font-size:12px;color:var(--text-sub);">Tên hiển thị + nội dung cho từng khu vực</span>
        </div>
        <div class="admin-card" style="padding:24px;">
            <form id="configForm">
            <input type="hidden" name="action_config" value="1">

            <!-- BXH COLUMNS -->
            <div style="background:rgba(0,212,212,0.05);border:1px solid rgba(0,212,212,0.15);border-radius:12px;padding:20px;margin-bottom:20px;">
                <h5 style="font-size:14px;font-weight:700;color:var(--accent-color);margin-bottom:16px;"><i class="bi bi-bar-chart-steps me-2"></i>Bảng Xếp Hạng — 3 cột</h5>
                <div class="row g-3">`;

    for (let i = 1; i <= 3; i++) {
        const defTitles = ['Top 50 Bài Hát Thịnh Hành','Top 50 Nhạc Việt','Top 50 Nhạc Hot'];
        const savedIds = (v('bxh_col'+i+'_songs','')).split(',').map(x=>x.trim()).filter(Boolean);
        const songOpts = dbSongs.map(s =>
            `<option value="${s.id}" ${savedIds.includes(String(s.id))?'selected':''}>${escHtml(s.title)} — ${escHtml(s.artist)}</option>`
        ).join('');
        html += `
                    <div class="col-md-4">
                        <div style="background:rgba(255,255,255,0.04);border:1px solid var(--border-color);border-radius:10px;padding:16px;">
                            <div style="font-size:11px;font-weight:700;color:var(--text-sub);text-transform:uppercase;margin-bottom:12px;">Cột ${i}</div>
                            <label class="form-label">Tên cột BXH</label>
                            <input type="text" class="form-control mb-3" id="bxh_col${i}_title_input"
                                value="${v('bxh_col'+i+'_title', defTitles[i-1])}"
                                placeholder="${defTitles[i-1]}">
                            <label class="form-label">Chọn bài hát <span style="color:var(--text-sub);font-size:11px;">(Ctrl/Cmd để chọn nhiều)</span></label>
                            <select id="bxh_col${i}_songs_select" multiple
                                style="height:160px;background:#1a2128;color:white;border:1px solid var(--border-color);border-radius:8px;width:100%;padding:4px;font-size:12px;">
                                ${songOpts || '<option disabled>Chưa có bài hát nào</option>'}
                            </select>
                            <div style="font-size:11px;color:var(--text-sub);margin-top:6px;">
                                <span id="bxh_col${i}_count">${savedIds.length}</span> bài đã chọn
                                <button type="button" onclick="clearBxhCol(${i})" style="float:right;background:none;border:none;color:var(--accent-red);font-size:11px;cursor:pointer;">Bỏ chọn tất cả</button>
                            </div>
                        </div>
                    </div>`;
    }

    html += `
                </div>
            </div>

            <!-- DYNAMIC SECTIONS MANAGER -->
            <div style="margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                    <div>
                        <div style="font-size:14px;font-weight:700;color:white;">Sections Trang Chủ</div>
                        <div style="font-size:12px;color:var(--text-sub);margin-top:2px;">Thêm / xoá / sắp xếp các section — mỗi section sẽ hiện trên trang chủ</div>
                    </div>
                    <button type="button" onclick="addNewSection()" class="btn-cyan" style="padding:8px 18px;font-size:13px;">
                        <i class="bi bi-plus-circle me-1"></i>Thêm Section Mới
                    </button>
                </div>
                <div id="sections-list"></div>
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                <button type="button" onclick="saveConfig()" class="btn-cyan" style="padding:11px 32px;font-size:14px;">
                    <i class="bi bi-cloud-check-fill me-2"></i>Lưu cấu hình trang chủ
                </button>
            </div>
            </form>
        </div>`;

    // Build sections list from current config
    const _secRaw = v('homepage_sections_json', '[]');
    let _secParsed = [];
    try { _secParsed = JSON.parse(_secRaw); } catch(e) {}
    renderSectionsList(Array.isArray(_secParsed) ? _secParsed : []);


    container.innerHTML = html;
}

async function saveConfig() {
    const form = document.getElementById('configForm');
    if (!form) return;
    const fd = new FormData(form);

    // Collect BXH columns — title + selected song IDs
    for (let i = 1; i <= 3; i++) {
        const titleEl = document.getElementById('bxh_col'+i+'_title_input');
        const selEl   = document.getElementById('bxh_col'+i+'_songs_select');
        if (titleEl) fd.append('bxh_col'+i+'_title', titleEl.value);
        if (selEl) {
            const ids = Array.from(selEl.selectedOptions).map(o=>o.value).join(',');
            fd.append('bxh_col'+i+'_songs', ids);
        }
    }

    // Build sections JSON từ danh sách hiện tại
    const sectionsJSON = buildSectionsJSON();
    fd.append('homepage_sections_json', sectionsJSON);

    const btn = document.querySelector('[onclick="saveConfig()"]');
    const origHtml = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width:14px;height:14px;border-width:2px;"></span>Đang lưu...'; }

    const data = await adminFetch(fd);
    if (btn) { btn.disabled = false; btn.innerHTML = origHtml; }

    if (data.status === 'success') {
        showToast(data.message || 'Đã lưu cấu hình!', 'success');
        applyFreshData(data);
    } else {
        showToast(data.message || 'Lỗi khi lưu!', 'error');
    }
}

// ==========================================
// DYNAMIC SECTIONS MANAGER
// ==========================================
let currentSections = [];

function renderSectionsList(sections) {
    currentSections = sections || [];
    const container = document.getElementById('sections-list');
    if (!container) return;

    if (currentSections.length === 0) {
        container.innerHTML = `<div style="text-align:center;padding:32px;color:var(--text-sub);font-size:13px;border:1px dashed var(--border-color);border-radius:12px;">
            Chưa có section nào. Nhấn <strong style="color:white;">"Thêm Section Mới"</strong> để bắt đầu.
        </div>`;
        return;
    }

    let html = '';
    currentSections.forEach((sec, idx) => {
        const albSel = buildAlbumMultiSelect(`sec_albums_${idx}`, sec.album_ids || '');
        html += `
        <div id="sec-item-${idx}" style="background:rgba(255,255,255,0.03);border:1px solid var(--border-color);border-radius:12px;padding:18px 20px;margin-bottom:12px;">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <div style="width:28px;height:28px;background:rgba(0,212,212,0.12);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--accent-color);font-size:12px;font-weight:800;flex-shrink:0;">${idx+1}</div>
                <input type="text" class="form-control" id="sec_title_${idx}" value="${escHtml(sec.title)}" placeholder="Tên section..." style="flex:1;">
                <select class="form-select" id="sec_type_${idx}" style="width:200px;">
                    <option value="scroll" ${sec.type==='scroll'?'selected':''}>📜 Cuộn ngang (Playlist)</option>
                    <option value="grid"   ${sec.type==='grid'  ?'selected':''}>⊞ Ô vuông (Album grid)</option>
                </select>
                <div style="display:flex;gap:6px;">
                    <button type="button" onclick="moveSectionUp(${idx})" class="btn-icon-action" ${idx===0?'disabled style="opacity:0.3;"':''} title="Lên"><i class="bi bi-chevron-up"></i></button>
                    <button type="button" onclick="moveSectionDown(${idx})" class="btn-icon-action" ${idx===currentSections.length-1?'disabled style="opacity:0.3;"':''} title="Xuống"><i class="bi bi-chevron-down"></i></button>
                    <button type="button" onclick="deleteSection(${idx})" class="btn-icon-action btn-icon-del" title="Xoá section"><i class="bi bi-trash3"></i></button>
                </div>
            </div>
            <div>
                <label class="form-label" style="font-size:12px;">Chọn Albums hiển thị <span style="color:var(--text-sub);">(Ctrl/Cmd để chọn nhiều)</span></label>
                ${albSel}
            </div>
        </div>`;
    });
    container.innerHTML = html;
}

function buildAlbumMultiSelect(name, selectedIds) {
    const sel = selectedIds ? selectedIds.split(',').map(s=>s.trim()).filter(Boolean) : [];
    const opts = dbAlbums.map(a =>
        `<option value="${a.id}" ${sel.includes(String(a.id))?'selected':''}>${escHtml(a.title)}</option>`
    ).join('');
    return `<select class="form-select" id="${name}" multiple style="height:100px;">${opts.length ? opts : '<option disabled>Chưa có album nào</option>'}</select>`;
}

function escHtml(s) { return (s||'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function addNewSection() {
    currentSections.push({ title: 'Section Mới', type: 'scroll', album_ids: '' });
    renderSectionsList(currentSections);
    // Scroll to new section
    const newEl = document.getElementById(`sec-item-${currentSections.length-1}`);
    if (newEl) newEl.scrollIntoView({ behavior:'smooth', block:'nearest' });
}

function deleteSection(idx) {
    if (!confirm(`Xoá section "${currentSections[idx]?.title}"?`)) return;
    currentSections.splice(idx, 1);
    renderSectionsList(currentSections);
}

function moveSectionUp(idx) {
    if (idx === 0) return;
    syncSectionsFromDOM();
    [currentSections[idx-1], currentSections[idx]] = [currentSections[idx], currentSections[idx-1]];
    renderSectionsList(currentSections);
}

function moveSectionDown(idx) {
    if (idx >= currentSections.length - 1) return;
    syncSectionsFromDOM();
    [currentSections[idx], currentSections[idx+1]] = [currentSections[idx+1], currentSections[idx]];
    renderSectionsList(currentSections);
}

function syncSectionsFromDOM() {
    currentSections.forEach((sec, idx) => {
        const titleEl = document.getElementById(`sec_title_${idx}`);
        const typeEl  = document.getElementById(`sec_type_${idx}`);
        const albEl   = document.getElementById(`sec_albums_${idx}`);
        if (titleEl) sec.title = titleEl.value;
        if (typeEl)  sec.type  = typeEl.value;
        if (albEl)   sec.album_ids = Array.from(albEl.selectedOptions).map(o=>o.value).join(',');
    });
}

function buildSectionsJSON() {
    syncSectionsFromDOM();
    return JSON.stringify(currentSections);
}

function clearBxhCol(i) {
    const sel = document.getElementById('bxh_col'+i+'_songs_select');
    if (!sel) return;
    Array.from(sel.options).forEach(o => o.selected = false);
    const cnt = document.getElementById('bxh_col'+i+'_count');
    if (cnt) cnt.textContent = '0';
}

// Live count update for BXH selects
document.addEventListener('change', function(e) {
    const m = e.target && e.target.id && e.target.id.match(/^bxh_col(\d)_songs_select$/);
    if (m) {
        const cnt = document.getElementById('bxh_col'+m[1]+'_count');
        if (cnt) cnt.textContent = e.target.selectedOptions.length;
    }
});


// ==========================================
// 9. KHUYẾN MÃI
// ==========================================
function renderPromo() {
    setNav('promo');
    window.scrollTo(0,0);
    let html = `
        <div class="d-flex justify-content-between align-items-center" style="margin-top:32px;margin-bottom:20px;">
            <h2 class="section-title" style="margin:0;">Quản Lý Khuyến Mãi</h2>
            <button class="btn-cyan" data-bs-toggle="modal" data-bs-target="#promoModal"><i class="bi bi-plus-lg"></i> Tạo mã mới</button>
        </div>
        ${renderAlert()}
        <div class="admin-card"><table class="admin-table">
            <thead><tr><th>Mã</th><th>Giảm giá</th><th>Mô tả</th><th>Hết hạn</th><th style="text-align:center;">Thao tác</th></tr></thead>
            <tbody>`;
    if (dbPromos.length > 0) {
        dbPromos.forEach(p => {
            const expired = new Date(p.expires_at) < new Date();
            html += `<tr>
                <td style="font-weight:700;color:var(--accent-color);letter-spacing:1px;">${p.code}</td>
                <td><span class="badge-status approved" style="font-size:13px;">-${p.discount_percent}%</span></td>
                <td style="color:var(--text-sub);font-size:13px;">${p.description||'—'}</td>
                <td style="font-size:12px;${expired?'color:var(--accent-red);':'color:var(--accent-green);'}">${p.expires_at ? p.expires_at.substring(0,16) : '—'} ${expired?'(Hết hạn)':''}</td>
                <td style="text-align:center;">
                    <button onclick="submitAction('promo',${p.id},'${p.code}','delete')" class="btn-icon-action btn-icon-del"><i class="bi bi-trash3"></i></button>
                </td>
            </tr>`;
        });
    } else {
        html += `<tr><td colspan="5" style="text-align:center;color:var(--text-sub);padding:40px 0;">
            Chưa có mã khuyến mãi nào. <br><small style="font-size:11px;">Gợi ý: tạo bảng <code>promotions</code> với cột id, code, discount_percent, description, expires_at.</small>
        </td></tr>`;
    }
    html += `</tbody></table></div>`;
    container.innerHTML = html;
}

// ==========================================
// MODAL HELPERS
// ==========================================
function filterAlbumSelect(catId, selectedAlbId = "") {
    const sel = document.getElementById('song_alb_id');
    sel.innerHTML = '<option value="">-- Bài hát đơn lẻ --</option>';
    dbAlbums.filter(a => a.category_id == catId).forEach(a => {
        sel.innerHTML += `<option value="${a.id}" ${a.id == selectedAlbId ? 'selected' : ''}>${a.title}</option>`;
    });
}

function openAddCatModal() {
    document.getElementById('catModalTitle').textContent = "Thêm chủ đề";
    document.getElementById('cat_action').value = "add";
    document.getElementById('cat_name').value = "";
    document.getElementById('cat_id').value = "";
    document.getElementById('cat_file').required = false;
    document.getElementById('cat_file').value = "";
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}
function openEditCatModal(id, name) {
    document.getElementById('catModalTitle').textContent = "Sửa chủ đề";
    document.getElementById('cat_action').value = "edit";
    document.getElementById('cat_id').value = id;
    document.getElementById('cat_name').value = name;
    document.getElementById('cat_file').required = false;
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}

function openAddAlbumModal(catId) {
    document.getElementById('albModalTitle').textContent = "Tạo Album";
    document.getElementById('alb_action').value = "add";
    document.getElementById('alb_title').value = "";
    document.getElementById('alb_cat_id').value = catId;
    document.getElementById('alb_cat_select_group').style.display = "none";
    document.getElementById('alb_file').required = true;
    new bootstrap.Modal(document.getElementById('albumModal')).show();
}
function openEditAlbumModal(id, title, catId) {
    document.getElementById('albModalTitle').textContent = "Sửa Album";
    document.getElementById('alb_action').value = "edit";
    document.getElementById('alb_id').value = id;
    document.getElementById('alb_title').value = title;
    document.getElementById('alb_select_cat').value = catId;
    document.getElementById('alb_cat_select_group').style.display = "block";
    document.getElementById('alb_file').required = false;
    new bootstrap.Modal(document.getElementById('albumModal')).show();
}

function openAddSongModal(defaultCatId="", defaultAlbId="") {
    document.getElementById('songModalTitle').textContent = "Thêm bài hát";
    document.getElementById('song_action').value = "add";
    ['song_title','song_artist','song_composer','song_lyrics'].forEach(id => document.getElementById(id).value = "");
    document.getElementById('song_mood').value = "";
    document.getElementById('song_status').value = "approved";
    document.getElementById('song_audio_file').required = false;
    document.getElementById('song_audio_file').value = "";
    document.getElementById('song_image_file').required = false;
    document.getElementById('song_image_file').value = "";
    document.getElementById('song_audio_url').value = "";
    document.getElementById('song_image_url').value = "";
    if (defaultCatId !== "") {
        document.getElementById('song_cat_id').value = defaultCatId;
        filterAlbumSelect(defaultCatId, defaultAlbId);
    } else if (dbCategories.length > 0) {
        document.getElementById('song_cat_id').value = dbCategories[0].id;
        filterAlbumSelect(dbCategories[0].id);
    }
    new bootstrap.Modal(document.getElementById('songModal')).show();
}
function openEditSongModal(id, title, artist, composer, catId, albId, lyrics, mood, status) {
    document.getElementById('songModalTitle').textContent = "Chỉnh sửa bài hát";
    document.getElementById('song_action').value = "edit";
    document.getElementById('song_id').value = id;
    document.getElementById('song_title').value = title;
    document.getElementById('song_artist').value = artist;
    document.getElementById('song_composer').value = composer || '';
    document.getElementById('song_lyrics').value = lyrics.replace(/\\n/g, "\n");
    document.getElementById('song_cat_id').value = catId;
    document.getElementById('song_mood').value = mood || '';
    document.getElementById('song_status').value = status || 'approved';
    document.getElementById('song_audio_file').required = false;
    document.getElementById('song_audio_file').value = "";
    document.getElementById('song_image_file').required = false;
    document.getElementById('song_image_file').value = "";
    document.getElementById('song_audio_url').value = "";
    document.getElementById('song_image_url').value = "";
    filterAlbumSelect(catId, albId);
    new bootstrap.Modal(document.getElementById('songModal')).show();
}

function openAddArtistModal() {
    document.getElementById('artistModalTitle').textContent = "Thêm Nghệ sĩ";
    document.getElementById('artist_action').value = "add";
    document.getElementById('artist_id_field').value = "";
    ['artist_name','artist_bio','artist_fb','artist_yt'].forEach(id => document.getElementById(id).value = "");
    document.getElementById('artist_file').required = true;
    new bootstrap.Modal(document.getElementById('artistModal')).show();
}
function openEditArtistModal(id, name, bio, fb, yt) {
    document.getElementById('artistModalTitle').textContent = "Sửa Nghệ sĩ";
    document.getElementById('artist_action').value = "edit";
    document.getElementById('artist_id_field').value = id;
    document.getElementById('artist_name').value = name;
    document.getElementById('artist_bio').value = bio;
    document.getElementById('artist_fb').value = fb;
    document.getElementById('artist_yt').value = yt;
    document.getElementById('artist_file').required = false;
    new bootstrap.Modal(document.getElementById('artistModal')).show();
}

// ==========================================
// BULK DELETE & RESET
// ==========================================
let currentBulkTable = '';
let currentResetTable = '';

function getCheckedIds(prefix) {
    return Array.from(document.querySelectorAll(`input[name="${prefix}"]:checked`))
                .map(cb => parseInt(cb.value)).filter(Boolean);
}

function toggleAllCheckboxes(prefix, checked) {
    document.querySelectorAll(`input[name="${prefix}"]`).forEach(cb => cb.checked = checked);
    updateBulkBar(prefix.replace('_check',''));
}

function updateBulkBar(tableKey) {
    const prefix = tableKey + '_check';
    const ids = getCheckedIds(prefix);
    const bar = document.getElementById('bulk_bar_' + tableKey);
    if (!bar) return;
    if (ids.length > 0) {
        bar.style.display = 'flex';
        const span = document.getElementById('bulk_count_' + tableKey);
        if (span) span.textContent = ids.length;
    } else {
        bar.style.display = 'none';
    }
}

async function doBulkDelete(tableKey) {
    const prefix = tableKey + '_check';
    const ids = getCheckedIds(prefix);
    if (!ids.length) { alert('Chưa chọn mục nào!'); return; }
    const labelMap = { songs:'bài hát', albums:'album', categories:'chủ đề', banners:'banner', artists:'nghệ sĩ' };
    if (!confirm(`Xóa ${ids.length} ${labelMap[tableKey] || 'mục'} đã chọn? Không thể hoàn tác.`)) return;
    const fd = new FormData();
    fd.append('action_bulk', '1');
    fd.append('bulk_table', tableKey);
    fd.append('bulk_ids', ids.join(','));
    const data = await adminFetch(fd);
    if (data.status === 'success') {
        showToast(data.message, 'success');
        applyFreshData(data);
        rerenderCurrent();
    } else {
        showToast(data.message || 'Đã xảy ra lỗi', 'error');
    }
}

function openResetModal(tableKey, labelText) {
    currentResetTable = tableKey;
    document.getElementById('reset_table_label').textContent = labelText;
    document.getElementById('resetConfirmInput').value = '';
    new bootstrap.Modal(document.getElementById('resetModal')).show();
}

async function doResetTable() {
    const input = document.getElementById('resetConfirmInput').value.trim();
    if (input !== 'XAC NHAN XOA') { alert('Nhập đúng "XAC NHAN XOA" để xác nhận!'); return; }
    bootstrap.Modal.getInstance(document.getElementById('resetModal'))?.hide();
    const fd = new FormData();
    fd.append('action_reset', '1');
    fd.append('reset_table', currentResetTable);
    fd.append('reset_confirm', input);
    const data = await adminFetch(fd);
    if (data.status === 'success') {
        showToast(data.message, 'success');
        applyFreshData(data);
        rerenderCurrent();
    } else {
        showToast(data.message || 'Xác nhận không đúng, không xóa dữ liệu.', 'error');
    }
}

// ==========================================
// SUBMIT DELETE / ACTION (AJAX)
// ==========================================
async function submitAction(type, id, name, action) {
    let msg = `Bạn có chắc muốn thực hiện thao tác này với "${name}"?`;
    if (action === 'delete') {
        msg = type === 'cat'
            ? `CẢNH BÁO: Xóa chủ đề "${name}" sẽ xóa toàn bộ Album và Bài hát bên trong! Tiếp tục?`
            : `Xóa "${name}"? Hành động này không thể hoàn tác.`;
    }
    if (!confirm(msg)) return;

    const actionKeys = { cat:'action_category', alb:'action_album', song:'action_song', banner:'action_banner', artist:'action_artist', user:'action_user', promo:'action_promo' };
    const idKeys     = { cat:'category_id',     alb:'album_id',     song:'song_id',     banner:'banner_id',     artist:'artist_id',     user:'user_id',     promo:'promo_id' };

    const fd = new FormData();
    fd.append(actionKeys[type], action);
    fd.append(idKeys[type], id);

    const data = await adminFetch(fd);
    if (data.status === 'success') {
        showToast(data.message, 'success');
        applyFreshData(data);
        rerenderCurrent();
    } else {
        showToast(data.message || 'Đã xảy ra lỗi', 'error');
    }
}
</script>
</body>
</html>