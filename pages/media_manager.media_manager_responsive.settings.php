<?php

$addon = rex_addon::get('media_manager_responsive');

$form = rex_config_form::factory($addon->getName());

$field = $form->addSelectField('cache_warmup');
$field->setLabel($this->i18n('media_manager_responsive_cache_warmup_label'));
$field->setNotice($this->i18n('media_manager_responsive_cache_warmup_notice'));
$select = $field->getSelect();
$select->setSize(1);
$select->addOption($this->i18n('media_manager_responsive_cache_warmup_original'), 'original');
$select->addOption($this->i18n('media_manager_responsive_cache_warmup_enhanced'), 'enhanced');

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $this->i18n('media_manager_responsive_settings'), false);
$fragment->setVar('body', $form->get(), false);

?>
<div class="row">
	<div class="col-lg-8">
		<?= $fragment->parse('core/page/section.php') ?>
	</div>
	<div class="col-lg-4">
		<?php

$anchor = '<a target="_blank" href="https://donate.alexplus.de/?addon=media_manager_responsive"><img src="'.rex_url::addonAssets('media_manager_responsive', 'jetzt-beauftragen.svg').'" style="width: 100% max-width: 400px;"></a>';

$fragment = new rex_fragment();
$fragment->setVar('class', 'info', false);
$fragment->setVar('title', $this->i18n('media_manager_responsive_donate'), false);
$fragment->setVar('body', '<p>' . $this->i18n('media_manager_responsive_info_donate') . '</p>' . $anchor, false);
echo !rex_config::get("alexplusde", "donated") ? $fragment->parse('core/page/section.php') : "";

if (rex_addon::get('speed_up')->isAvailable()) {
    $anchor = '<a target="_blank" href="https://github.com/alexplusde/speed_up/">'.$this->i18n('media_manager_responsive_info_media_manager_responsive_install').'</a>';
    $fragment = new rex_fragment();
    $fragment->setVar('class', 'info', false);
    $fragment->setVar('title', $this->i18n('media_manager_responsive_info_speed_up_title'), false);
    $fragment->setVar('body', '<p>' . $this->i18n('media_manager_responsive_info_speed_up_inactive') . '</p>' . $anchor, false);
    echo $fragment->parse('core/page/section.php');
}

$package = rex_install_packages::getUpdatePackages();
if (isset($packages['media_manager_responsive'])) {
    $current_version = rex_addon::get('media_manager_responsive')->getProperty('version');
    if (isset($package['files'])) {
        $latest_version = array_pop($updates)['version'];
    }
    if (rex_version::compare($latest_version, $current_version, ">")) {
        echo rex_view::info($this->i18n('media_manager_responsive_update_available') . " " .$latest_version);
    };
};
?>
	</div>
</div>
