<section class="card narrow">
    <h1><?= e($title) ?></h1>
    <form method="post" action="<?= e($action) ?>" novalidate>
        <?= Csrf::field() ?>

        <label for="full_name">Full name</label>
        <input type="text" id="full_name" name="full_name" value="<?= e($customer['full_name']) ?>" required maxlength="100">
        <small class="error"><?= e($errors['full_name'] ?? '') ?></small>

        <label for="license_no">Driving license number</label>
        <input type="text" id="license_no" name="license_no" value="<?= e($customer['license_no']) ?>" required maxlength="30">
        <small class="error"><?= e($errors['license_no'] ?? '') ?></small>

        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" value="<?= e($customer['phone']) ?>" required maxlength="20">
        <small class="error"><?= e($errors['phone'] ?? '') ?></small>

        <label for="email">Email (optional)</label>
        <input type="email" id="email" name="email" value="<?= e($customer['email']) ?>" maxlength="120">
        <small class="error"><?= e($errors['email'] ?? '') ?></small>

        <button type="submit" class="btn">Save</button>
        <a class="btn btn-light" href="<?= e(url('customers')) ?>">Cancel</a>
    </form>
</section>
