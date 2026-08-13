<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>送信完了</title>
    <style>
        body {
            font-family: sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }

        h1 {
            color: #28a745;
        }

        .info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            margin-top: 20px;
        }

        .info p {
            margin: 10px 0;
        }

        .label {
            font-weight: bold;
            color: #666;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <h1>送信完了</h1>
    <p>お問い合わせありがとうございます。</p>

    <div class="info">
        <p><span class="label">お名前：</span>{{ $name }}</p>
        <p><span class="label">メールアドレス：</span>{{ $email }}</p>
        <p><span class="label">メッセージ：</span></p>
        <p>{{ $message }}</p>
    </div>

    <p style="margin-top: 20px;"><a href="/contact">戻る</a></p>
</body>

</html>