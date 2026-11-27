<?php

$session = session_include('/login');
$userId = (int) ($session['user_id'] ?? 0);

$userProfile = fetch(executeQuery(
    "SELECT district, subdistrict FROM user_profiles WHERE user_id = ? ORDER BY profile_id DESC LIMIT 1",
    [$userId]
));

$hasLocation = !empty(trim((string) ($userProfile['district'] ?? '')))
    && !empty(trim((string) ($userProfile['subdistrict'] ?? '')));

if (!$hasLocation) {
    flash('error', 'Silakan lengkapi profil lokasi Anda (kecamatan & kelurahan) terlebih dahulu sebelum menjual barang.');
    redirect('/user', 302);
}

$categories = fetchColumn(executeQuery('SELECT category_name FROM categories ORDER BY category_name ASC'));
$formValues = [
    'namaBarang' => '',
    'kategori' => '',
    'harga' => '',
    'hargaAsli' => '',
    'kondisi' => 'Sangat Bagus',
    'deskripsi' => '',
];

Component::flush();
?>
<link rel="stylesheet" href="<?= asset('css/lib/quill.snow.css') ?>">
<link rel="stylesheet" href="<?= asset('css/barang-form.css') ?>">
<?php $styles = Component::endFlush(); ?>
<?php Component::include('user/header', [
    'title' => 'Tambah Barang Baru',
    'styles' => $styles,
]); ?>

<main class="container py-4 py-lg-5">
    <?php Component::flush() ?>
    <a class="btn btn-outline-secondary d-inline-flex align-items-center px-3 gap-2" href="<?= esc(route('/barang')) ?>">
        <i data-lucide="arrow-left" class="pk-icon" aria-hidden="true"></i>
        <span>Kembali</span>
    </a>
    <?php $extra = Component::endFlush() ?>

    <?php Component::include("common/banner", [
        "title" => 'Tambah barang',
        "subtitle" => "Isi detail barang yang ingin kamu jual di lingkungan kampus",
        "extra" => $extra
    ]) ?>

    <?php Component::include('barang/form', [
        'action' => route('/barang/tambah/post'),
        'target' => 'tambah',
        'productId' => '',
        'submitLabel' => 'Simpan barang',
        'formValues' => $formValues,
        'categories' => $categories,
        'coverImage' => '',
        'thumbImages' => [],
        'productTags' => [],
    ]); ?>
</main>

<?php Component::flush(); ?>
<script src="<?= asset('js/lib/quill.min.js') ?>" defer></script>
<script src="<?= asset('js/barang.js') ?>" defer></script>
<?php $script = Component::endFlush(); ?>
<?php Component::include('user/footer', ['script' => $script]); ?>
