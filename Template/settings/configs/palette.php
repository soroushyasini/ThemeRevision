<fieldset>
    <legend><?= t(ucfirst($color).' Palette') ?></legend>
    <p><small><?= t('Check the file "config-default.php" in the plugin directory for more information.') ?></small> <a href="https://github.com/greyaz/ThemeRevision/blob/main/Model/DefaultConfigsModel.php" target="_blank" rel="noopener noreferrer"><small><?= t("View on Github") ?></small></a></p>
    <div class="color-palette">
        <?php foreach($configs[$color.'_palette'] as $key=>$value):  ?>
            <?= in_array($key, $end_keys[$color]) ? '<hr>' : ''; ?>
            <label class="tr-color-picker"><input type='text' value="<?= $this->text->e($value) ?>" name="<?= $color.'_palette['.$key.']' ?>" /> <span><?= $this->text->e($key) ?></span></label>
        <?php endforeach; ?>
        <hr>
    </div>
</fieldset>
