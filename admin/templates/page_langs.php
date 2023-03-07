
<?
$folderLink = BASE_ADMIN_PATH . '/langs';
$files = scandir($folderLink);
$languages = array();
foreach ($files as $file) {
    if (pathinfo($folderLink . "/" . $file, PATHINFO_EXTENSION) === "xml") {
        $languages[] = array("lang_code" => pathinfo($folderLink . "/" . $file, PATHINFO_FILENAME));
    }
}
?>

<!-- admin interface language -->

<ul class="dropdown-menu">
    <? foreach ($languages as $lang) { ?>
        <li>
            <a data-value="<?=$lang['lang_code']?>" href="<?=$PageUrl->SetAdminLangUrl($lang['lang_code'])?>">
                <?=$lang['lang_code']?>
            </a>
        </li>
    <? } ?>
</ul>

<!-- /admin interface language -->
