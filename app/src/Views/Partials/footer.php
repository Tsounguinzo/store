<?php
$jsonDataPath = '../../Helpers/JSONData/fr/';

$dataFilePath = $jsonDataPath . 'footer.json';
if(file_exists($dataFilePath)){
    $jsonData = file_get_contents($dataFilePath);
    if($jsonData !== false){
        $data = json_decode($jsonData, true);
        if($data !== null){
            $fastLinks = $data['fastLinks'];
            $moreLinks = $data['moreLinks'];
            $contactInfo = $data['contactInfo'];
            $socialMedias = $data['socialMedias'];
            $copyright = $data['copyright'];
        } else {
            echo "Error: Unable to decode the JSON file.";
        }
    } else {
        echo "Error: Unable to read the JSON file.";
    }
} else {
    echo "Error: JSON file not found.";
}
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
