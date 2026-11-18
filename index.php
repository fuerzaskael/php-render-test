<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Render PHP Test</title>
    <style>
        body { font-family: sans-serif; text-align: center; margin-top: 80px; background: #0f172a; color: #f8fafc; }
        .box { display: inline-block; padding: 30px; border-radius: 12px; background: #1e293b; border: 1px solid #334155; }
    </style>
</head>
<body>
    <div class="box">
        <h1>It works on Render!</h1>
        <p>Running PHP version: <strong><?php echo phpversion(); ?></strong></p>
        <p>Server time: <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>
</body>
</html>