<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $errorCode ?> Error</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8f9fa;
            color: #6c757d;
        }
        .error {
            text-align: center;
        }
        .error h1 {
            font-size: 6rem;
            margin-bottom: 1rem;
        }
        .error h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .error p {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="error">
        <h1><?php echo $errorCode ?></h1>
        <h2><?php echo $errorTitle ?></h2>
        <p><?php echo $errorMessage ?></p>
    </div>
</body>
</html>
