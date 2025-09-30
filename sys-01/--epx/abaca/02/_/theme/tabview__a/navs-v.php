    <ul class="nav nav-tabs" id="<?=$this->html_id__navs()?>" role="tablist">
        <?php foreach($this->list as $k => $v): ?>
        <li class="nav-item">
            <a class="nav-link <?=($v['is_active'] ?? null) ? 'active show' : ''?>" id="tab-<?=$k?>" data-toggle="tab" href="#tab_body__<?=$k?>" role="tab" aria-controls="<?=$k?>" aria-selected="true"><i class="nav-icon <?=$v['icon']?>"></i> <?=$v['label']?></a>
        </li>
        <?php endforeach; ?>
    </ul>