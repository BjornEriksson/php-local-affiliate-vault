<?php if ($editData): ?>
<div class="box" style="border-color: #2563eb;">
    <h2>Editing: <?= h($editData['program_name']) ?></h2>
    <form method="POST">
        <input type="hidden" name="program_file" value="<?= h($editFile) ?>">
        <label>Program Name *</label>
        <input type="text" name="program_name" value="<?= h($editData['program_name']) ?>" required>
        <label>Login URL</label>
        <input type="url" name="login_url" value="<?= h($editData['login_url'] ?? '') ?>">
        <div class="grid-2" style="gap:10px;">
            <div>
                <label>Username</label>
                <input type="text" name="username" value="<?= h($editData['username'] ?? '') ?>">
            </div>
            <div>
                <label>Password</label>
                <input type="text" name="password" value="<?= h($editData['password'] ?? '') ?>">
            </div>
        </div>
        <label>Earnings (numbers only)</label>
        <input type="number" step="any" name="earnings" value="<?= h($editData['earnings'] ?? 0) ?>">

        <div style="margin: 10px 0;">
            <label class="inline-check"><input type="checkbox" name="is_favourite" <?= !empty($editData['is_favourite']) ? 'checked' : '' ?>> ★ Favourite</label>
            <label class="inline-check"><input type="checkbox" name="is_active" <?= !empty($editData['is_active']) ? 'checked' : '' ?>> ● Active</label>
        </div>

        <label>Comments / Notes</label>
        <textarea name="comments"><?= h($editData['comments'] ?? '') ?></textarea>
        <button type="submit" name="update_program">Update Program</button>
        <a href="index.php?open=<?= urlencode($editFile) ?>#prog_<?= urlencode($editFile) ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php else: ?>
<div class="grid-2">
    <div class="box">
        <h2>Add Affiliate Program</h2>
        <form method="POST">
            <label>Program Name *</label>
            <input type="text" name="program_name" placeholder="e.g., AffiliStars" required>
            <label>Login URL</label>
            <input type="url" name="login_url" placeholder="https://...">
            <div class="grid-2" style="gap:10px;">
                <div>
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Username">
                </div>
                <div>
                    <label>Password</label>
                    <input type="text" name="password" placeholder="Password">
                </div>
            </div>
            <label>Earnings (numbers only)</label>
            <input type="number" step="any" name="earnings" value="0">

            <div style="margin: 8px 0;">
                <label class="inline-check"><input type="checkbox" name="is_favourite"> ★ Favourite</label>
                <label class="inline-check"><input type="checkbox" name="is_active" checked> ● Active</label>
            </div>

            <label>Comments / Notes</label>
            <textarea name="comments" placeholder="Payment terms, contact manager, brand warnings..."></textarea>
            <button type="submit" name="save_program">Save Program (.txt)</button>
        </form>
    </div>

    <div class="box">
        <h2>Add Casino (Client)</h2>
        <?php if (!empty($files)): 
            $selectedProgram = basename($_GET['selected'] ?? '');
        ?>
            <form method="POST">
                <label>Select Affiliate Program *</label>
                <select name="program_file" required>
                    <?php foreach ($files as $f): 
                        $p = loadProgram($f); 
                        $fname = basename($f);
                    ?>
                        <option value="<?= h($fname) ?>" <?= $fname === $selectedProgram ? 'selected' : '' ?>>
                            <?= h($p['program_name'] ?? $fname) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>Casino Name *</label>
                <input type="text" name="casino_name" placeholder="e.g., LuckLand Casino" required>
                <label>Affiliate Tracking URL *</label>
                <input type="url" name="affiliate_url" placeholder="https://..." required>
                <button type="submit" name="save_client">Add Casino to Program</button>
            </form>
        <?php else: ?>
            <p><em>Create an affiliate program first before adding casinos.</em></p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
