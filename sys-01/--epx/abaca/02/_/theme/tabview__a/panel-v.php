<div class="tab-content" id="<?=$this->html_id__panel()?>">
    <?php foreach($this->list as $k => $v): ?>
    <div class="tab-pane fade" id="tab_body__<?=$k?>" role="tabpanel" aria-labelledby="tab_aria__<?=$k?>">
        <?php include $this->tab_file($k) ?>
    </div>
    <?php endforeach; ?>
</div>
