<?php
if (isset($_GET['file'])) {
    $file = $_GET['file'];
    // Pastikan folder 'uploads/' ada di depan nama fail
    $path = "uploads/" . $file;
} else {
    echo "Fail tidak dinyatakan.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nota</title>
    <style>
        body { margin: 0; padding: 0; overflow: hidden; background-color: #333; }
        iframe { border: none; width: 100%; height: 100vh; }
    </style>
</head>
<body oncontextmenu="return false;">
    <?php if (file_exists($path)): ?>
        <iframe src="<?php echo $path; ?>#toolbar=0&navpanes=0&scrollbar=0"></iframe>
    <?php else: ?>
        <div style="color: white; text-align: center; padding-top: 50px;">
            <h2>Ralat: Fail tidak dijumpai!</h2>
            <p>Pastikan fail <b><?php echo htmlspecialchars($file); ?></b> berada di dalam folder <b>uploads/</b></p>
        </div>
    <?php endif; ?>
</body>
</html>
