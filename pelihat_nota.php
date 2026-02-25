<?php
$file = $_GET['file'];
?>
<body oncontextmenu="return false;">
    <style>
        body { margin: 0; overflow: hidden; }
        iframe { width: 100%; height: 100vh; border: none; }
    </style>
    <iframe src="uploads/<?php echo $file; ?>#toolbar=0&navpanes=0&scrollbar=0"></iframe>
</body>
