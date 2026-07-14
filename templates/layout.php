<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?></title>

  <link rel="stylesheet" 
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" 
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" 
      crossorigin="anonymous">
  <script 
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" 
        crossorigin="anonymous">
      </script>
</head>
<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid bg-dark">
    <a class="navbar-brand text-white" href="/">Анализатор страниц</a>
    <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-white" href="/urls">Сайты</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
    <main class="container">
      <!-- flash -->
        <div class="flash">
            <?php if (!empty($flash)): ?>
                <?php if (isset($flash['success'])): ?>
                    <?php foreach ($flash['success'] as $message): ?>
                        <p style="color: green"><?= htmlspecialchars($message) ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if (isset($flash['error'])): ?>
                    <?php foreach ($flash['error'] as $message): ?>
                        <p style="color: red"><?= htmlspecialchars($message) ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <!-- content -->
        <?=$content?>
    </main>
</body>
</html>