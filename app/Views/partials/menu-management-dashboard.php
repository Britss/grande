<?php
/** @var array $menuCatalog */
/** @var array $menuCategoryOptions */
$liveItemCount = 0;
$archivedItemCount = 0;
$sizeCount = 0;

foreach ($menuCatalog as $category) {
    foreach ($category['items'] as $item) {
        if (($item['is_available'] ?? false) === true) {
            $liveItemCount++;
        } else {
            $archivedItemCount++;
        }

        $sizeCount += count($item['sizes'] ?? []);
    }
}
?>
<section class="page-section container">
    <div class="payment-review-header">
        <div>
            <p class="eyebrow">Menu Management</p>
            <h2>Maintain the live menu catalog</h2>
        </div>
        <p class="payment-review-note">This rebuild keeps menu editing inside `grande` so the ordering flow no longer depends on the old project.</p>
    </div>

    <div class="content-grid three-up staff-summary-grid">
        <article class="content-card">
            <p class="eyebrow">Live Items</p>
            <h2><?= e((string) $liveItemCount) ?></h2>
            <p>Menu items currently visible to customers.</p>
        </article>
        <article class="content-card">
            <p class="eyebrow">Archived</p>
            <h2><?= e((string) $archivedItemCount) ?></h2>
            <p>Items kept in the database but hidden from public ordering.</p>
        </article>
        <article class="content-card">
            <p class="eyebrow">Size Entries</p>
            <h2><?= e((string) $sizeCount) ?></h2>
            <p>Size and price combinations attached to the current catalog.</p>
        </article>
    </div>

    <div class="dashboard-filter-bar" data-dashboard-filter="menu">
        <div class="dashboard-filter-field dashboard-filter-field--wide">
            <label for="menu-filter-search">Search menu</label>
            <input id="menu-filter-search" type="search" class="form-control" placeholder="Item, category, description, size" data-filter-search>
        </div>
        <div class="dashboard-filter-field">
            <label for="menu-filter-status">Visibility</label>
            <select id="menu-filter-status" class="form-control" data-filter-status>
                <option value="all">All items</option>
                <option value="live">Live</option>
                <option value="archived">Archived</option>
            </select>
        </div>
        <button type="button" class="button button-secondary button-small" data-filter-reset>Reset</button>
    </div>

    <div class="management-grid">
        <article class="content-card management-card">
            <div class="dashboard-card__header">
                <h3>Create Menu Item</h3>
            </div>
            <form method="post" action="<?= e(url('/dashboard/admin/menu')) ?>" class="stack-form" data-dashboard-form="menu" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="menu_action" value="create_item">
                <input type="hidden" name="section" value="menu">

                <div class="form-grid">
                    <div class="form-field">
                        <label for="menu-item-name">Item Name</label>
                        <input id="menu-item-name" name="name" type="text" class="form-control">
                    </div>
                    <div class="form-field">
                        <label for="menu-item-category">Category</label>
                        <select id="menu-item-category" name="category" class="form-control">
                            <?php foreach ($menuCategoryOptions as $categoryOption): ?>
                                <option value="<?= e($categoryOption) ?>"><?= e(rtrim($categoryOption, '.')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-field">
                    <label for="menu-item-description">Description</label>
                    <textarea id="menu-item-description" name="description" rows="4" class="form-control"></textarea>
                </div>

                <div class="form-field">
                    <label for="menu-item-image-file">Upload Image</label>
                    <input id="menu-item-image-file" name="image_file" type="file" accept="image/jpeg,image/png,image/webp" class="form-control">
                    <p class="field-note">JPG, PNG, or WEBP. Uploaded files are saved under public/uploads/menu-items.</p>
                </div>

                <div class="form-field">
                    <label for="menu-item-image">Existing Image Path</label>
                    <input id="menu-item-image" name="image_url" type="text" class="form-control" placeholder="public/images/menu-items/example.png">
                </div>

                <label class="checkbox-line">
                    <input type="checkbox" name="is_available" value="1" checked>
                    <span>Publish item immediately</span>
                </label>

                <button type="submit" class="button button-primary">Create Item</button>
            </form>
        </article>

        <div class="management-stack">
            <?php foreach ($menuCatalog as $category): ?>
                <article class="content-card management-card">
                    <div class="dashboard-card__header">
                        <h3><?= e($category['name'] ?? 'Menu') ?></h3>
                    </div>

                    <div class="management-stack">
                        <?php foreach ($category['items'] as $item): ?>
                            <?php
                            $sizeSearchText = implode(' ', array_map(
                                static fn (array $size): string => (string) ($size['label'] ?? ''),
                                $item['sizes'] ?? []
                            ));
                            $itemStatus = ($item['is_available'] ?? false) ? 'live' : 'archived';
                            ?>
                            <article
                                class="management-subcard"
                                data-filter-item
                                data-filter-status="<?= e($itemStatus) ?>"
                                data-filter-text="<?= e(strtolower(trim(($item['name'] ?? '') . ' ' . ($item['category'] ?? '') . ' ' . ($item['description'] ?? '') . ' ' . $sizeSearchText))) ?>"
                            >
                                <form method="post" action="<?= e(url('/dashboard/admin/menu')) ?>" class="stack-form" data-dashboard-form="menu" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="menu_action" value="update_item">
                                    <input type="hidden" name="section" value="menu">
                                    <input type="hidden" name="item_id" value="<?= e((string) ($item['id'] ?? 0)) ?>">

                                    <div class="management-subcard__header">
                                        <h4><?= e((string) ($item['name'] ?? 'Menu item')) ?></h4>
                                        <span class="status-pill status-pill--<?= ($item['is_available'] ?? false) ? 'confirmed' : 'cancelled' ?>">
                                            <?= ($item['is_available'] ?? false) ? 'Live' : 'Archived' ?>
                                        </span>
                                    </div>

                                    <div class="form-grid">
                                        <div class="form-field">
                                            <label>Item Name</label>
                                            <input name="name" type="text" class="form-control" value="<?= e((string) ($item['name'] ?? '')) ?>">
                                        </div>
                                        <div class="form-field">
                                            <label>Category</label>
                                            <select name="category" class="form-control">
                                                <?php foreach ($menuCategoryOptions as $categoryOption): ?>
                                                    <option value="<?= e($categoryOption) ?>" <?= ($item['category'] ?? '') === $categoryOption ? 'selected' : '' ?>>
                                                        <?= e(rtrim($categoryOption, '.')) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-field">
                                        <label>Description</label>
                                        <textarea name="description" rows="3" class="form-control"><?= e((string) ($item['description'] ?? '')) ?></textarea>
                                    </div>

                                    <div class="form-field">
                                        <label>Upload Replacement Image</label>
                                        <input name="image_file" type="file" accept="image/jpeg,image/png,image/webp" class="form-control">
                                        <p class="field-note">Leave empty to keep the current image path.</p>
                                    </div>

                                    <div class="form-field">
                                        <label>Current Image Path</label>
                                        <input name="image_url" type="text" class="form-control" value="<?= e((string) ($item['image_url'] ?? '')) ?>">
                                    </div>

                                    <div class="management-inline-actions">
                                        <label class="checkbox-line">
                                            <input type="checkbox" name="is_available" value="1" <?= ($item['is_available'] ?? false) ? 'checked' : '' ?>>
                                            <span>Visible to customers</span>
                                        </label>
                                        <button type="submit" class="button button-secondary button-small">Save Item</button>
                                    </div>
                                </form>

                                <div class="management-size-list">
                                    <?php foreach ($item['sizes'] as $size): ?>
                                        <form method="post" action="<?= e(url('/dashboard/admin/menu')) ?>" class="management-size-card" data-dashboard-form="menu">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="menu_action" value="update_size">
                                            <input type="hidden" name="section" value="menu">
                                            <input type="hidden" name="size_id" value="<?= e((string) ($size['id'] ?? 0)) ?>">

                                            <div class="form-grid form-grid--tight">
                                                <div class="form-field">
                                                    <label>Size</label>
                                                    <input name="size_label" type="text" class="form-control" value="<?= e((string) ($size['label'] ?? '')) ?>">
                                                </div>
                                                <div class="form-field">
                                                    <label>Price</label>
                                                    <input name="price" type="text" class="form-control" value="<?= e(number_format((float) ($size['price'] ?? 0), 2, '.', '')) ?>">
                                                </div>
                                                <div class="form-field">
                                                    <label>Sort</label>
                                                    <input name="sort_order" type="number" min="0" class="form-control" value="<?= e((string) ($size['sort_order'] ?? 0)) ?>">
                                                </div>
                                            </div>

                                            <div class="management-inline-actions">
                                                <label class="checkbox-line">
                                                    <input type="checkbox" name="is_default" value="1" <?= ($size['is_default'] ?? false) ? 'checked' : '' ?>>
                                                    <span>Default size</span>
                                                </label>
                                                <label class="checkbox-line">
                                                    <input type="checkbox" name="size_is_available" value="1" <?= ($size['is_available'] ?? false) ? 'checked' : '' ?>>
                                                    <span>Available</span>
                                                </label>
                                                <button type="submit" class="button button-secondary button-small">Save Size</button>
                                            </div>
                                        </form>
                                    <?php endforeach; ?>
                                </div>

                                <form method="post" action="<?= e(url('/dashboard/admin/menu')) ?>" class="management-size-card management-size-card--create" data-dashboard-form="menu">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="menu_action" value="create_size">
                                    <input type="hidden" name="section" value="menu">
                                    <input type="hidden" name="item_id" value="<?= e((string) ($item['id'] ?? 0)) ?>">

                                    <div class="form-grid form-grid--tight">
                                        <div class="form-field">
                                            <label>New Size</label>
                                            <input name="size_label" type="text" class="form-control" placeholder="Regular">
                                        </div>
                                        <div class="form-field">
                                            <label>Price</label>
                                            <input name="price" type="text" class="form-control" placeholder="120.00">
                                        </div>
                                        <div class="form-field">
                                            <label>Sort</label>
                                            <input name="sort_order" type="number" min="0" class="form-control" value="0">
                                        </div>
                                    </div>

                                    <div class="management-inline-actions">
                                        <label class="checkbox-line">
                                            <input type="checkbox" name="is_default" value="1">
                                            <span>Default size</span>
                                        </label>
                                        <label class="checkbox-line">
                                            <input type="checkbox" name="size_is_available" value="1" checked>
                                            <span>Available</span>
                                        </label>
                                        <button type="submit" class="button button-primary button-small">Add Size</button>
                                    </div>
                                </form>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </article>
            <?php endforeach; ?>

            <div class="content-card payment-review-empty" data-filter-empty hidden>
                <h3>No menu items match those filters.</h3>
                <p>Adjust the search or visibility filter to review more catalog items.</p>
            </div>
        </div>
    </div>
</section>
