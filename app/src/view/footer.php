<?php
require_once '../../../scripts/populate_php_file.php';

$dataFilePath = '../Helpers/JSONData/fr/footer.json';
$fastLinks = 'fastLinks';
$moreLinks = 'moreLinks';
$contactInfo = 'contactInfo';
$socialMedias = 'socialMedias';
$copyright = 'copyright';
populatePage($dataFilePath, $fastLinks, $moreLinks, $contactInfo, $socialMedias, $copyright);
?>

<footer class="footer">
    <section class="box-container">
        <div class="box">
            <h2><?=$fastLinks['title']?></h2>
            <?php foreach ($fastLinks['links'] as $link):?>
                <a href="<?=$link['url']?>"><i class="<?=$link['icon']?>"></i> <?=$link['text']?></a>
            <?php endforeach;?>
        </div>
        <div class="box">
            <h2><?=$moreLinks['title']?></h2>
            <?php foreach ($moreLinks['links'] as $link):?>
                <a href="<?=$link['url']?>"><i class="<?=$link['icon']?>"></i> <?=$link['text']?></a>
            <?php endforeach;?>
        </div>
        <div class="box">
            <h2><?=$contactInfo['title']?></h2>
            <?php foreach ($contactInfo['links'] as $contact):?>
                <p><i class="<?=$contact['icon']?>"></i><?=$contact['text']?></p>
            <?php endforeach;?>
        </div>
        <div class="box">
            <h2><?=$socialMedias['title']?></h2>
            <?php foreach ($socialMedias['links'] as $link):?>
            <a href="<?=$link['url']?>"><i class="<?=$link['icon']?>"></i> <?=$link['text']?></a>
            <?php endforeach;?>
        </div>
    </section>
    <p class="credit">&copy; Copyright <?= date('Y'); ?>
        <span><?=$copyright['text']?></span>
    </p>
</footer>
