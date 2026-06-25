<?php
// api_update_views.php
// Tăng lượt nghe thực tế trong DB và trả về giá trị mới

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json; charset=utf-8');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$song_id = intval($_POST['song_id'] ?? 0);
if ($song_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid song_id']);
    exit;
}

try {
    // Tăng views và trả về giá trị mới
    $pdo->prepare("UPDATE songs SET views = views + 1 WHERE id = ?")->execute([$song_id]);
    $row = $pdo->prepare("SELECT views FROM songs WHERE id = ?");
    $row->execute([$song_id]);
    $result = $row->fetch();

    echo json_encode([
        'status' => 'success',
        'song_id' => $song_id,
        'views'   => (int)($result['views'] ?? 0)
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
