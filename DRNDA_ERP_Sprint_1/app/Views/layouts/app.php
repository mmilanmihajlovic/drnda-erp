<?php $user = auth_user(); ?>
<!doctype html><html lang="sr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= htmlspecialchars($title ?? 'DRNDA ERP') ?></title><link rel="stylesheet" href="/app.css"></head><body>
<?php if ($user): ?>
<aside class="sidebar"><div class="brand">DRNDA ERP</div><nav><a href="/">Komandni centar</a><a href="/?page=cases">Slučajevi</a><a href="/?page=case-create">Novi slučaj</a><a href="#">Pogrebno</a><a href="#">Operativa</a><a href="#">Cvećara</a><a href="#">Administracija</a><a href="#">Finansije</a></nav><form method="post" action="/?page=logout"><input type="hidden" name="_token" value="<?= csrf_token() ?>"><button class="link-button">Odjava</button></form></aside>
<?php endif; ?>
<main class="main <?= $user ? '' : 'auth-main' ?>"><?php require $view; ?></main></body></html>
