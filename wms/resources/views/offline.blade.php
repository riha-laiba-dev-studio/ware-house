<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Offline — WMS Pro</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: Inter, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #0f172a; color: #e2e8f0; margin: 0; }
    .box { text-align: center; max-width: 400px; padding: 2rem; }
    .icon { font-size: 4rem; margin-bottom: 1rem; }
    h1 { font-size: 1.5rem; font-weight: 600; margin-bottom: .5rem; }
    p { color: #94a3b8; margin-bottom: 1.5rem; }
    button { background: #2563eb; color: #fff; border: none; padding: .75rem 2rem; border-radius: .5rem; font-size: 1rem; cursor: pointer; }
    button:hover { background: #1d4ed8; }
  </style>
</head>
<body>
  <div class="box">
    <div class="icon">📡</div>
    <h1>You're Offline</h1>
    <p>WMS Pro needs an internet connection. Check your network and try again.</p>
    <button onclick="window.location.reload()">Try Again</button>
  </div>
</body>
</html>
